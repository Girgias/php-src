/*
   +----------------------------------------------------------------------+
   | Copyright (c) The PHP Group                                          |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | https://www.php.net/license/3_01.txt                                 |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: Marcus Boerger <helly@php.net>                              |
   | based on ext/db/db.c by:                                             |
   |          Rasmus Lerdorf <rasmus@php.net>                             |
   |          Jim Winstead <jimw@php.net>                                 |
   +----------------------------------------------------------------------+
 */

/* $Id$ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_globals.h"

#include <stdlib.h>
#include <string.h>
#include <errno.h>
#if HAVE_UNISTD_H
#include <unistd.h>
#endif

#include "flatfile.h"

#define FLATFILE_BLOCK_SIZE 1024

/*
 * ret = -1 means that database was opened for read-only
 * ret = 0  success
 * ret = 1  key already exists - nothing done
 */

/* {{{ flatfile_store */
int flatfile_store(flatfile *dba, const zend_string *key, const zend_string *value, int mode) {
	if (mode == FLATFILE_INSERT) {
		if (flatfile_findkey(dba, key)) {
			return 1;
		}
		php_stream_seek(dba->fp, 0L, SEEK_END);
		php_stream_printf(dba->fp, "%zu\n", ZSTR_LEN(key));
		php_stream_flush(dba->fp);
		if (php_stream_write(dba->fp, ZSTR_VAL(key), ZSTR_LEN(key)) < ZSTR_LEN(key)) {
			return -1;
		}
		php_stream_printf(dba->fp, "%zu\n", ZSTR_LEN(value));
		php_stream_flush(dba->fp);
		if (php_stream_write(dba->fp, ZSTR_VAL(value), ZSTR_LEN(value)) < ZSTR_LEN(value)) {
			return -1;
		}
	} else { /* FLATFILE_REPLACE */
		flatfile_delete(dba, key);
		php_stream_printf(dba->fp, "%zu\n", ZSTR_LEN(key));
		php_stream_flush(dba->fp);
		if (php_stream_write(dba->fp, ZSTR_VAL(key), ZSTR_LEN(key)) < ZSTR_LEN(key)) {
			return -1;
		}
		php_stream_printf(dba->fp, "%zu\n", ZSTR_LEN(value));
		if (php_stream_write(dba->fp, ZSTR_VAL(value), ZSTR_LEN(value)) < ZSTR_LEN(value)) {
			return -1;
		}
	}

	php_stream_flush(dba->fp);
	return 0;
}
/* }}} */

/* {{{ flatfile_fetch */
zend_string *flatfile_fetch(flatfile *dba, /* const */ zend_string *key) {
	zend_string *value = NULL;
	char buf[sizeof(size_t)];

	if (flatfile_findkey(dba, key)) {
		if (php_stream_gets(dba->fp, buf, sizeof(buf))) {
			size_t val_len = atoi(buf);

			// TODO Adjust so the assertion below works
			value = zend_string_safe_alloc(val_len, 1, 1, /* persistent */ false);
			val_len = php_stream_read(dba->fp, ZSTR_VAL(value), val_len);
			ZSTR_LEN(value) = val_len;
			ZSTR_VAL(value)[val_len] = 0;
			//ZEND_ASSERT(val_len == ZSTR_LEN(value));
		}
	}
	return value;
}
/* }}} */

static void flatfile_skip_value(php_stream *stream)
{
	char buf[sizeof(size_t)];
	if (php_stream_eof(stream)) { return; }
	if (!php_stream_gets(stream, buf, sizeof(buf))) {
		ZEND_ASSERT(0 && "Value length should be here");
	}
	size_t val_len = atoi(buf);
	/* Skip over value */
	php_stream_seek(stream, val_len, SEEK_CUR);
}

/* {{{ flatfile_delete */
/* zend_string_equals discards const qualifier */
zend_result flatfile_delete(flatfile *dba, /* const */ zend_string *key) {
	size_t buf_size = FLATFILE_BLOCK_SIZE;
	char *buf = emalloc(buf_size);
	size_t num;
	size_t pos;

	php_stream_rewind(dba->fp);
	while(!php_stream_eof(dba->fp)) {
		/* read in the length of the key name */
		if (!php_stream_gets(dba->fp, buf, 15)) {
			break;
		}
		num = atoi(buf);
		if (num >= buf_size) {
			buf_size = num + FLATFILE_BLOCK_SIZE;
			buf = erealloc(buf, buf_size);
		}
		pos = php_stream_tell(dba->fp);

		/* read in the key name */
		num = php_stream_read(dba->fp, buf, num);

		zend_string *lookup_key = php_stream_read_to_str(dba->fp, num);
		if (zend_string_equals(lookup_key, key)) {
			php_stream_seek(dba->fp, pos, SEEK_SET);
			php_stream_putc(dba->fp, 0);
			php_stream_flush(dba->fp);
			php_stream_seek(dba->fp, 0L, SEEK_END);
			efree(buf);
			zend_string_release_ex(lookup_key, false);
			return SUCCESS;
		}
		zend_string_release_ex(lookup_key, false);

		flatfile_skip_value(dba->fp);
	}
	efree(buf);
	return FAILURE;
}
/* }}} */

/* {{{ flatfile_findkey */
/* zend_string_equals discards const qualifier */
bool flatfile_findkey(flatfile *dba, /* const */ zend_string *key) {
	size_t buf_size = FLATFILE_BLOCK_SIZE;

	php_stream_rewind(dba->fp);
	while (!php_stream_eof(dba->fp)) {
		char buf[sizeof(size_t)];
		if (!php_stream_gets(dba->fp, buf, sizeof(buf))) {
			break;
		}
		/* Find key length */
		size_t num = atoi(buf);
		/* Read key into buffer variable */
		zend_string *lookup_key = php_stream_read_to_str(dba->fp, num);

		if (zend_string_equals(lookup_key, key)) {
			zend_string_release_ex(lookup_key, false);
			return true;
			break;
		}
		zend_string_release_ex(lookup_key, false);
		flatfile_skip_value(dba->fp);
	}
	return false;
}
/* }}} */

/* {{{ flatfile_firstkey */
zend_string *flatfile_firstkey(flatfile *dba) {
	size_t num;
	size_t buf_size = FLATFILE_BLOCK_SIZE;
	char *buf = emalloc(buf_size);

	php_stream_rewind(dba->fp);
	while(!php_stream_eof(dba->fp)) {
		if (!php_stream_gets(dba->fp, buf, 15)) {
			break;
		}
		num = atoi(buf);
		if (num >= buf_size) {
			buf_size = num + FLATFILE_BLOCK_SIZE;
			buf = erealloc(buf, buf_size);
		}
		num = php_stream_read(dba->fp, buf, num);

		if (*(buf) != 0) {
			dba->CurrentFlatFilePos = php_stream_tell(dba->fp);
			zend_string *key = zend_string_init(buf, num, /* persistent */ false);
			efree(buf);
			return key;
		}
		flatfile_skip_value(dba->fp);
	}
	efree(buf);
	return NULL;

	// TODO First key can be deleted and data is 0 out

/*
	php_stream_rewind(dba->fp);
	// No entries
	while (!php_stream_eof(dba->fp)) {
		char buf[sizeof(size_t)];

		if (!php_stream_gets(dba->fp, buf, sizeof(buf))) {
			break;
		}

		size_t key_len = atoi(buf);
		zend_string *key = php_stream_read_to_str(dba->fp, key_len);
		if (ZSTR_VAL(key)[0] != 0) {
			ZEND_ASSERT(ZSTR_VAL(key)[key_len] == 0);
			ZEND_ASSERT(key_len == ZSTR_LEN(key));
			dba->CurrentFlatFilePos = php_stream_tell(dba->fp);
			return key;
		}
		zend_string_release_ex(key, /* persistent / false);
		flatfile_skip_value(dba->fp);
	}

	return NULL;
	*/
}
/* }}} */

/* {{{ flatfile_nextkey */
zend_string *flatfile_nextkey(flatfile *dba) {
	/*
	php_stream_seek(dba->fp, dba->CurrentFlatFilePos, SEEK_SET);
	while (!php_stream_eof(dba->fp)) {
		char buf[sizeof(size_t)];

		if (!php_stream_gets(dba->fp, buf, sizeof(buf))) {
			continue;
		}

		size_t key_len = atoi(buf);
		zend_string *key = php_stream_read_to_str(dba->fp, key_len);
		if (ZSTR_VAL(key)[key_len] != 0) {
			ZEND_ASSERT(ZSTR_VAL(key)[key_len] == 0);
			ZEND_ASSERT(key_len == ZSTR_LEN(key));
			dba->CurrentFlatFilePos = php_stream_tell(dba->fp);
			return key;
		}
		zend_string_release_ex(key, /* persistent / false);
		flatfile_skip_value(dba->fp);
	}

	return NULL;
	*/

	size_t num;
	size_t buf_size = FLATFILE_BLOCK_SIZE;
	char *buf = emalloc(buf_size);

	php_stream_seek(dba->fp, dba->CurrentFlatFilePos, SEEK_SET);
	while(!php_stream_eof(dba->fp)) {
		if (!php_stream_gets(dba->fp, buf, 15)) {
			break;
		}
		num = atoi(buf);
		if (num >= buf_size) {
			buf_size = num + FLATFILE_BLOCK_SIZE;
			buf = erealloc(buf, buf_size);
		}
		num = php_stream_read(dba->fp, buf, num);

		if (!php_stream_gets(dba->fp, buf, 15)) {
			break;
		}
		num = atoi(buf);
		if (num >= buf_size) {
			buf_size = num + FLATFILE_BLOCK_SIZE;
			buf = erealloc(buf, buf_size);
		}
		num = php_stream_read(dba->fp, buf, num);

		if (*(buf)!=0) {
			dba->CurrentFlatFilePos = php_stream_tell(dba->fp);
			zend_string *key = zend_string_init(buf, num, /* persistent */ false);
			efree(buf);
			return key;
		}
	}
	efree(buf);
	return NULL;
}
/* }}} */

/* {{{ flatfile_version */
char *flatfile_version()
{
	return "1.0, $Id$";
}
/* }}} */
