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
   | Author: Marcus Boerger <helly@php.net>                               |
   +----------------------------------------------------------------------+
 */

#ifndef PHP_LIB_FLATFILE_H
#define PHP_LIB_FLATFILE_H

#include "zend_types.h"

typedef struct {
	char *lockfn;
	int lockfd;
	php_stream *fp;
	size_t CurrentFlatFilePos;
	zend_string *next_key;
} flatfile;

#define FLATFILE_INSERT 1
#define FLATFILE_REPLACE 0

int flatfile_store(flatfile *dba, const zend_string *key, const zend_string *value, int mode);
zend_string *flatfile_fetch(flatfile *dba, /* const */ zend_string *key);
zend_result flatfile_delete(flatfile *dba, /* const */ zend_string *key);
bool flatfile_findkey(flatfile *dba, /* const */ zend_string *key);
zend_string *flatfile_firstkey(flatfile *dba);
zend_string *flatfile_nextkey(flatfile *dba);
char *flatfile_version(void);

#endif
