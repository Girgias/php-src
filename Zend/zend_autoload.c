/*
   +----------------------------------------------------------------------+
   | Zend Engine                                                          |
   +----------------------------------------------------------------------+
   | Copyright (c) Zend Technologies Ltd. (http://www.zend.com)           |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.00 of the Zend license,     |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.zend.com/license/2_00.txt.                                |
   | If you did not receive a copy of the Zend license and are unable to  |
   | obtain it through the world-wide-web, please send a note to          |
   | license@zend.com so we can mail you a copy immediately.              |
   +----------------------------------------------------------------------+
   | Authors: Gina Peter Banyard <girgias@php.net>                        |
   +----------------------------------------------------------------------+
*/

#include "zend.h"
#include "zend_API.h"
#include "zend_autoload.h"
#include "zend_hash.h"
#include "zend_types.h"
#include "zend_exceptions.h"
#include "zend_string.h"

ZEND_TLS HashTable *zend_class_autoload_functions;
ZEND_TLS HashTable *zend_class_autoload_map;

static void zend_autoload_callback_zval_destroy(zval *element)
{
   zend_fcall_info_cache *fcc = Z_PTR_P(element);
   zend_fcc_dtor(fcc);
   efree(fcc);
}

static Bucket *autoload_find_registered_function(const HashTable *autoloader_table, const zend_fcall_info_cache *function_entry)
{
	zend_fcall_info_cache *current_function_entry;
	ZEND_HASH_MAP_FOREACH_PTR(autoloader_table, current_function_entry) {
		if (zend_fcc_equals(current_function_entry, function_entry)) {
			return _p;
		}
	} ZEND_HASH_FOREACH_END();
	return NULL;
}

ZEND_API zend_class_entry *zend_perform_class_autoload(zend_string *class_name, zend_string *lc_name)
{
	if (!zend_class_autoload_functions) {
		return NULL;
	}

	zval zname;
	ZVAL_STR(&zname, class_name);

	const HashTable *class_autoload_functions = zend_class_autoload_functions;

	/* Cannot use ZEND_HASH_MAP_FOREACH_PTR here as autoloaders may be
	 * added/removed during autoloading. */
	HashPosition pos;
	zend_hash_internal_pointer_reset_ex(class_autoload_functions, &pos);
	while (true) {
		zend_fcall_info_cache *func_info = zend_hash_get_current_data_ptr_ex(class_autoload_functions, &pos);
		if (!func_info) {
			break;
		}
		zend_call_known_fcc(func_info, /* retval */ NULL, /* param_count */ 1, /* params */ &zname, /* named_params */ NULL);

		if (EG(exception)) {
			return NULL;
		}
		if (ZSTR_HAS_CE_CACHE(class_name) && ZSTR_GET_CE_CACHE(class_name)) {
			return (zend_class_entry*)ZSTR_GET_CE_CACHE(class_name);
		}

		zend_class_entry *ce = zend_hash_find_ptr(EG(class_table), lc_name);
		if (ce) {
			return ce;
		}

		zend_hash_move_forward_ex(class_autoload_functions, &pos);
	}
	return NULL;
}

/* Needed for compatibility with spl_register_autoload() */
ZEND_API void zend_autoload_register_class_loader(zend_fcall_info_cache *fcc, bool prepend)
{
   ZEND_ASSERT(ZEND_FCC_INITIALIZED(*fcc));

   if (!zend_class_autoload_functions) {
      ALLOC_HASHTABLE(zend_class_autoload_functions);
      zend_hash_init(zend_class_autoload_functions, 1, NULL, zend_autoload_callback_zval_destroy, false);
      /* Initialize as non-packed hash table for prepend functionality. */
      zend_hash_real_init_mixed(zend_class_autoload_functions);
   }

   ZEND_ASSERT(
      fcc->function_handler->type != ZEND_INTERNAL_FUNCTION
      || !zend_string_equals_literal(fcc->function_handler->common.function_name, "spl_autoload_call")
   );

   /* If function is already registered, don't do anything */
   if (autoload_find_registered_function(zend_class_autoload_functions, fcc)) {
      /* Release potential call trampoline */
      zend_release_fcall_info_cache(fcc);
      return;
   }

   zend_fcc_addref(fcc);
   zend_hash_next_index_insert_mem(zend_class_autoload_functions, fcc, sizeof(zend_fcall_info_cache));
   if (prepend && zend_hash_num_elements(zend_class_autoload_functions) > 1) {
      /* Move the newly created element to the head of the hashtable */
      ZEND_ASSERT(!HT_IS_PACKED(zend_class_autoload_functions));
      Bucket tmp = zend_class_autoload_functions->arData[zend_class_autoload_functions->nNumUsed-1];
      memmove(zend_class_autoload_functions->arData + 1, zend_class_autoload_functions->arData, sizeof(Bucket) * (zend_class_autoload_functions->nNumUsed - 1));
      zend_class_autoload_functions->arData[0] = tmp;
      zend_hash_rehash(zend_class_autoload_functions);
   }
}

ZEND_API bool zend_autoload_unregister_class_loader(const zend_fcall_info_cache *fcc) {
   if (zend_class_autoload_functions) {
      Bucket *p = autoload_find_registered_function(zend_class_autoload_functions, fcc);
      if (p) {
         zend_hash_del_bucket(zend_class_autoload_functions, p);
         return true;
      }
   }
   return false;
}

/* We do not return a HashTable* because zend_empty_array is not collectable,
 * therefore the zval holding this value must do so. Something that ZVAL_EMPTY_ARRAY(); does. */
ZEND_API void zend_autoload_fcc_map_to_callable_zval_map(zval *return_value) {
   if (zend_class_autoload_functions) {
      zend_fcall_info_cache *fcc;

      zend_array *map = zend_new_array(zend_hash_num_elements(zend_class_autoload_functions));
      ZEND_HASH_MAP_FOREACH_PTR(zend_class_autoload_functions, fcc) {
         zval tmp;
         zend_get_callable_zval_from_fcc(fcc, &tmp);
         zend_hash_next_index_insert(map, &tmp);
      } ZEND_HASH_FOREACH_END();
      RETURN_ARR(map);
   }
   RETURN_EMPTY_ARRAY();
}

/* Only for deprecated strange behaviour of spl_autoload_unregister() */
ZEND_API void zend_autoload_clean_class_loaders(void)
{
   if (zend_class_autoload_functions) {
      /* Don't destroy the hash table, as we might be iterating over it right now. */
      zend_hash_clean(zend_class_autoload_functions);
   }
}

#define ZEND_FAKE_OP_ARRAY ((zend_op_array*)(intptr_t)-1)

static zend_never_inline zend_op_array* zend_autoload_execute_file(zend_string *file_path)
{
	zend_op_array *new_op_array = NULL;
	zend_file_handle file_handle;

	ZEND_ASSERT(!zend_str_has_nul_byte(file_path));

	zend_string *resolved_path = zend_resolve_path(file_path);
	if (EXPECTED(resolved_path)) {
		/* If file has already been required once */
		if (zend_hash_exists(&EG(included_files), resolved_path)) {
			zend_string_release_ex(resolved_path, false);
			return ZEND_FAKE_OP_ARRAY;
		}
	} else if (UNEXPECTED(EG(exception))) {
		return NULL;
	} else {
		resolved_path = zend_string_copy(file_path);
	}

	zend_stream_init_filename_ex(&file_handle, resolved_path);
	if (SUCCESS == zend_stream_open(&file_handle)) {
		if (!file_handle.opened_path) {
			file_handle.opened_path = zend_string_copy(resolved_path);
		}

		if (zend_hash_add_empty_element(&EG(included_files), file_handle.opened_path)) {
			new_op_array = zend_compile_file(&file_handle, ZEND_REQUIRE);
		} else {
			new_op_array = ZEND_FAKE_OP_ARRAY;
		}
	} else if (!EG(exception)) {
		zend_message_dispatcher(ZMSG_FAILED_REQUIRE_FOPEN, file_path);
	}
	zend_destroy_file_handle(&file_handle);
	zend_string_release_ex(resolved_path, false);

	return new_op_array;
}

ZEND_API zend_class_entry *zend_perform_class_map_autoload(zend_string *class_name, zend_string *lc_name)
{
	if (!zend_class_autoload_map) {
		return zend_perform_class_autoload(class_name, lc_name);
	}

	zval *class_file_path = zend_hash_find_deref(zend_class_autoload_map, class_name);
	/* An entry has been found in the class map */
	if (class_file_path) {
		ZEND_ASSERT(Z_TYPE_P(class_file_path) == IS_STRING);

		zend_op_array *op_array = zend_autoload_execute_file(Z_STR_P(class_file_path));
		if (op_array != NULL && op_array != ZEND_FAKE_OP_ARRAY) {
			destroy_op_array(op_array);
			efree_size(op_array, sizeof(zend_op_array));
		}

		if (EG(exception)) {
			return NULL;
		}

		zend_class_entry *ce = zend_hash_find_ptr(EG(class_table), lc_name);
		if (EXPECTED(ce)) {
			return ce;
		}

		/* if the item is in the classmap it must be valid after including the file */
		zend_throw_error(NULL, "Error during autoloading from classmap. Entry \"%s\" failed to load the class from \"%s\" (Class undefined after file included)",
			ZSTR_VAL(class_name), Z_STRVAL_P(class_file_path));
		return NULL;
	}


	return zend_perform_class_autoload(class_name, lc_name);
}

// TODO USERLAND FUNCTIONS, maybe namespace them?
/* Register given function as a class autoloader */
ZEND_FUNCTION(autoload_register_class)
{
	bool prepend = false;
	zend_fcall_info fci;
	zend_fcall_info_cache fcc;

	ZEND_PARSE_PARAMETERS_START(1, 2)
		Z_PARAM_FUNC(fci, fcc)
		Z_PARAM_OPTIONAL
		Z_PARAM_BOOL(prepend)
	ZEND_PARSE_PARAMETERS_END();

	if (!ZEND_FCC_INITIALIZED(fcc)) {
		/* Call trampoline has been cleared by zpp. Refetch it, because we want to deal
		 * with it ourselves. It is important that it is not refetched on every call,
		 * because calls may occur from different scopes. */
		zend_is_callable_ex(&fci.function_name, NULL, IS_CALLABLE_SUPPRESS_DEPRECATIONS, NULL, &fcc, NULL);
	}

	if (fcc.function_handler->type == ZEND_INTERNAL_FUNCTION &&
		fcc.function_handler->internal_function.handler == zif_autoload_call_class) {
		zend_argument_value_error(1, "must not be the autoload_call_class() function");
		RETURN_THROWS();
		}

	zend_autoload_register_class_loader(&fcc, prepend);
}

static bool zend_check_map_is_valid(const HashTable *map) {
	zend_string *name;
	zval *zval_path;

	if (zend_hash_num_elements(map) == 0) {
		zend_argument_must_not_be_empty_error(1);
		return false;
	}

	ZEND_HASH_FOREACH_STR_KEY_VAL(map, name, zval_path) {
		if (!name) {
			zend_argument_value_error(1, "must only have string keys", name ? ZSTR_VAL(name) : "");
			return false;
		}
		if (ZSTR_LEN(name) == 0 || !zend_is_valid_class_name(name)) {
			zend_argument_value_error(1, "key \"%s\" is not a valid class name", name ? ZSTR_VAL(name) : "");
			return false;
		}
		if (Z_TYPE_P(zval_path) != IS_STRING) {
			zend_argument_type_error(1, "path for class \"%s\" must be of type string", ZSTR_VAL(name));
			return false;
		}
		if (Z_STRLEN_P(zval_path) == 0) {
			zend_argument_value_error(1, "path for class \"%s\" must not be empty", ZSTR_VAL(name));
			return false;
		}
		if (zend_str_has_nul_byte(Z_STR_P(zval_path))) {
			zend_argument_value_error(1, "path for class \"%s\" must not contain null bytes", ZSTR_VAL(name));
			return false;
		}
	} ZEND_HASH_FOREACH_END();

	return true;
}

ZEND_FUNCTION(autoload_register_class_map)
{
	HashTable *class_map;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_ARRAY_HT(class_map)
	ZEND_PARSE_PARAMETERS_END();

	if (UNEXPECTED(!zend_check_map_is_valid(class_map))) {
		RETURN_THROWS();
	}

	zend_class_autoload_map = zend_array_dup(class_map);
	zend_autoload_class = zend_perform_class_map_autoload;
}

ZEND_FUNCTION(autoload_unregister_class)
{
	zend_fcall_info fci;
	zend_fcall_info_cache fcc;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_FUNC_NO_TRAMPOLINE_FREE(fci, fcc)
	ZEND_PARSE_PARAMETERS_END();

	RETVAL_BOOL(zend_autoload_unregister_class_loader(&fcc));
	/* Release trampoline */
	zend_release_fcall_info_cache(&fcc);
}

/* Try all registered class autoloader functions to load the requested class */
ZEND_FUNCTION(autoload_call_class)
{
	zend_string *class_name;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "S", &class_name) == FAILURE) {
		RETURN_THROWS();
	}

	zend_string *lc_name = zend_string_tolower(class_name);
	zend_perform_class_autoload(class_name, lc_name);
	zend_string_release(lc_name);
}

/* Return all registered class autoloader functions */
ZEND_FUNCTION(autoload_list_class) {
	ZEND_PARSE_PARAMETERS_NONE();

	zend_autoload_fcc_map_to_callable_zval_map(return_value);
}

void zend_autoload_shutdown(void)
{
   if (zend_class_autoload_functions) {
      zend_hash_destroy(zend_class_autoload_functions);
      FREE_HASHTABLE(zend_class_autoload_functions);
      zend_class_autoload_functions = NULL;
   }
	if (zend_class_autoload_map) {
		zend_array_release(zend_class_autoload_map);
		zend_class_autoload_map = NULL;
	}
}
