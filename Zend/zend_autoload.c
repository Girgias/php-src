/*
   +----------------------------------------------------------------------+
   | Zend Engine														  |
   +----------------------------------------------------------------------+
   | Copyright (c) 1998-2021 Zend Technologies Ltd. (http://www.zend.com) |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.00 of the Zend license,     |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.zend.com/license/2_00.txt.                                |
   | If you did not receive a copy of the Zend license and are unable to  |
   | obtain it through the world-wide-web, please send a note to          |
   | license@zend.com so we can mail you a copy immediately.              |
   +----------------------------------------------------------------------+
   | Authors: Anthony Ferrara <ircmaxell@php.net>                         |
   |          George Peter Banyard <girgias@php.net>                      |
   +----------------------------------------------------------------------+
*/

#include "zend.h"
#include "zend_API.h"
#include "zend_autoload.h"
#include "zend_hash.h"
#include "zend_types.h"
#include "zend_exceptions.h"
#include "zend_interfaces.h"
#include "zend_string.h"

#define HT_MOVE_TAIL_TO_HEAD(ht) \
	do { \
		Bucket tmp = (ht)->arData[(ht)->nNumUsed-1]; \
		memmove((ht)->arData + 1, (ht)->arData, \
			sizeof(Bucket) * ((ht)->nNumUsed - 1)); \
		(ht)->arData[0] = tmp; \
		if (!((ht)->u.flags & HASH_FLAG_PACKED)) { \
			zend_hash_rehash(ht); \
		} else { \
			zend_autoload_reindex(ht); \
		} \
	} while (0)

static void zend_autoload_reindex(HashTable *ht)
{
	ZEND_ASSERT(ht->u.flags & HASH_FLAG_PACKED);
	for (size_t i = 0; i < ht->nNumUsed; i++) {
		ht->arData[i].h = i;
	}
}


static zend_always_inline bool zend_autoload_callback_equals(zend_autoload_func* func, zend_autoload_func* current)
{
	zval *func_name = &func->fci.function_name;
	zval *current_name = &current->fci.function_name;

	if (Z_TYPE_P(func_name) != Z_TYPE_P(current_name)) {
		return false;
	}
	switch (Z_TYPE_P(func_name)) {
		case IS_STRING:
			if (zend_string_equals(Z_STR_P(func_name), Z_STR_P(current_name))) {
				return true;
			}
		case IS_OBJECT:
			if (Z_OBJ_HANDLE_P(current_name) == Z_OBJ_HANDLE_P(func_name)) {
				return true;
			}
	}
	return false;
}



void *zend_autoload_call(zend_string *name, zend_string *lname, zend_long type)
{
	void *value = NULL;
	HashTable *symbol_table, *stack;
	zval zname, retval;
	zend_autoload_func *func_info;
	zend_bool dtor_name = 0, dtor_lname = 0;

	if (UNEXPECTED(name->val[0] == '\\')) {
		/* need to remove leading \ */
		name = zend_string_init(name->val + 1, name->len - 1, 0);
		dtor_name = 1;
	}
	if (UNEXPECTED(lname->val[0] == '\\')) {
		/* need to remove leading \
		 * This is a separate check, since some places will strip
		 * the leading slash from the lname already as it's used as a key
		 */
		lname = zend_string_init(lname->val + 1, lname->len - 1, 0);
		dtor_lname = 1;
	}

	ZVAL_STR(&zname, name);

	switch (type) {
		case ZEND_AUTOLOAD_CLASS:
			symbol_table = EG(class_table);
			stack = &EG(autoload.stack.class);
			break;
		case ZEND_AUTOLOAD_FUNCTION:
			symbol_table = EG(function_table);
			stack = &EG(autoload.stack.function);
			break;
		case ZEND_AUTOLOAD_CONSTANT:
			symbol_table = EG(zend_constants);
			stack = &EG(autoload.stack.constant);
			break;
		default:
			goto return_null;
	}

/*
	if (zend_hash_add_empty_element(stack, lname) == NULL) {
		// Recursion protection, add it early
		// as it will protect __autoload legacy behavior
		// as well
		goto return_null;
	}
*/

	ZEND_HASH_FOREACH_PTR(&EG(autoload.functions), func_info)
		if (func_info->type & type) {
			func_info->fci.retval = &retval;
			zend_fcall_info_argn(&func_info->fci, 1, &zname);
			zend_call_function(&func_info->fci, &func_info->fcc);
			zend_fcall_info_args_clear(&func_info->fci, 1);
			zend_exception_save();
			if (zend_hash_exists(symbol_table, lname)) {
				break;
			}
		}
	ZEND_HASH_FOREACH_END();

	zend_exception_restore();

	//zend_hash_del(stack, lname);

return_lookup:
	value = zend_hash_find_ptr(symbol_table, lname);
	if (dtor_name) {
		/* release the string, as an autoloader may have acquired a reference to it */
		zend_string_release(name);
	}
	if (dtor_lname) {
		zend_string_release(lname);
	}
	return value;

return_null:
	if (dtor_name) {
		/* release the string, as an autoloader may have acquired a reference to it */
		zend_string_release(name);
	}
	if (dtor_lname) {
		zend_string_release(lname);
	}
	return NULL;
}

bool zend_autoload_register(zend_autoload_func* func, zend_long flags)
{
	zend_autoload_func *current;

	ZEND_HASH_FOREACH_PTR(&EG(autoload.functions), current)
		if (zend_autoload_callback_equals(func, current)) {
			if (current->type == func->type) {
				/* already registered!!! */
				zval_ptr_dtor(&func->fci.function_name);
				efree(func);
				return true;
			}
		}
	ZEND_HASH_FOREACH_END();

	Z_TRY_ADDREF(func->fci.function_name);
	if (zend_hash_next_index_insert_ptr(&EG(autoload.functions), func) == NULL) {
		Z_TRY_DELREF(func->fci.function_name);
		return false;
	}

	if (func->type & ZEND_AUTOLOAD_CLASS) {
		EG(autoload.class_loader_count)++;
	}

	if (flags & ZEND_AUTOLOAD_FLAG_PREPEND) {
		HT_MOVE_TAIL_TO_HEAD(&EG(autoload.functions));
	}

	return true;
}

bool zend_autoload_unregister(zend_autoload_func* func)
{
	zend_ulong h;
	zend_autoload_func *current;
	bool retval = false;

	ZEND_HASH_FOREACH_NUM_KEY_PTR(&EG(autoload.functions), h, current)
		if (zend_autoload_callback_equals(func, current)) {
			if (current->type & ZEND_AUTOLOAD_CLASS) {
				EG(autoload.class_loader_count)--;
			}
			zend_hash_index_del(&EG(autoload.functions), h);
			retval = true;
		}
	ZEND_HASH_FOREACH_END();
	return retval;
}

bool zend_autoload_unregister_all(zend_long type)
{
	zend_ulong h;
	zend_autoload_func *current;

	ZEND_HASH_FOREACH_NUM_KEY_PTR(&EG(autoload.functions), h, current)
		if (current->type & type) {
			if (current->type & ZEND_AUTOLOAD_CLASS) {
				EG(autoload.class_loader_count)--;
			}
			zend_hash_index_del(&EG(autoload.functions), h);
		}
	ZEND_HASH_FOREACH_END();
	return true;
}

void zend_autoload_dtor(zval *pzv)
{
	zend_autoload_func *func = Z_PTR_P(pzv);
	zval_ptr_dtor(&func->fci.function_name);
	if (func->type & ZEND_AUTOLOAD_CLASS) {
		EG(autoload.class_loader_count)--;
	}
	efree(func);
}

ZEND_FUNCTION(autoload_register)
{
	zend_autoload_func *func;
	zend_long flags = 0;

	func = emalloc(sizeof(zend_autoload_func));

	if (FAILURE == zend_parse_parameters(ZEND_NUM_ARGS(), "lf|l", &func->type, &func->fci, &func->fcc, &flags)) {
		efree(func);
		RETURN_THROWS();
	}

	switch (func->type) {
		case ZEND_AUTOLOAD_CLASS:
		case ZEND_AUTOLOAD_FUNCTION:
		case ZEND_AUTOLOAD_CONSTANT:
			break;
		default:
			zend_argument_value_error(1, "must be one of AUTOLOAD_CLASS, AUTOLOAD_FUNCTION, or AUTOLOAD_CONSTANT");
			RETURN_THROWS();
	}

	if (!zend_autoload_register(func, flags)) {
		efree(func);
		RETURN_FALSE;
	}

	Z_TRY_ADDREF(func->fci.function_name);

	RETURN_TRUE;
}

ZEND_FUNCTION(autoload_unregister)
{
	zend_autoload_func *func;

	func = emalloc(sizeof(zend_autoload_func));

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "f", &func->fci, &func->fcc) == FAILURE) {
		efree(func);
		return;
	}

	if (!zend_autoload_unregister(func)) {
		efree(func);
		RETURN_FALSE;
	}
	efree(func);
	RETURN_TRUE;
}

ZEND_FUNCTION(autoload_unregister_all)
{
	long type = ~0;
	if (zend_parse_parameters(ZEND_NUM_ARGS(), "|l", &type) == FAILURE) {
		RETURN_THROWS();
	}

	RETURN_BOOL(zend_autoload_unregister_all(type));
}
