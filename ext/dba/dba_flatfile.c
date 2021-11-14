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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"

#if DBA_FLATFILE
#include "php_flatfile.h"

#include "libflatfile/flatfile.h"

#ifdef HAVE_UNISTD_H
#include <unistd.h>
#endif
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>

DBA_OPEN_FUNC(flatfile)
{
	info->dbf = pemalloc(sizeof(flatfile), info->flags&DBA_PERSISTENT);
	memset(info->dbf, 0, sizeof(flatfile));

	((flatfile*)info->dbf)->fp = info->fp;

	return SUCCESS;
}

DBA_CLOSE_FUNC(flatfile)
{
	flatfile *dba = info->dbf;

	if (dba->next_key) {
		zend_string_release_ex(dba->next_key, /* persistent */ false);
	}
	pefree(dba, info->flags&DBA_PERSISTENT);
}

DBA_FETCH_FUNC(flatfile)
{
	flatfile *dba = info->dbf;

	return flatfile_fetch(dba, key);
}

DBA_UPDATE_FUNC(flatfile)
{
	flatfile *dba = info->dbf;

	switch(flatfile_store(dba, key, val, mode==1 ? FLATFILE_INSERT : FLATFILE_REPLACE)) {
		case 0:
			return SUCCESS;
		case 1:
			return FAILURE;
		case -1:
			// TODO Check when this happens and confirm this can even happen
			php_error_docref(NULL, E_WARNING, "Operation not possible");
			return FAILURE;
		default:
			// TODO Convert this to an assertion failure
			php_error_docref(NULL, E_WARNING, "Unknown return value");
			return FAILURE;
	}
}

DBA_EXISTS_FUNC(flatfile)
{
	flatfile *dba = info->dbf;
	zend_string *value;
	value = flatfile_fetch(dba, key);
	if (value) {
		zend_string_release_ex(value, /* persistent */ false);
		return SUCCESS;
	}
	return FAILURE;
}

DBA_DELETE_FUNC(flatfile)
{
	flatfile *dba = info->dbf;

	return flatfile_delete(dba, key);
}

DBA_FIRSTKEY_FUNC(flatfile)
{
	flatfile *dba = info->dbf;

	if (dba->next_key) {
		zend_string_release_ex(dba->next_key, /* persistent */ false);
	}
	dba->next_key = flatfile_firstkey(dba);
	return dba->next_key;
}

DBA_NEXTKEY_FUNC(flatfile)
{
	flatfile *dba = info->dbf;

	if (!dba->next_key) {
		return NULL;
	}

	if (dba->next_key) {
		zend_string_release_ex(dba->next_key, /* persistent */ false);
	}
	dba->next_key = flatfile_nextkey(dba);
	return dba->next_key;
}

DBA_OPTIMIZE_FUNC(flatfile)
{
	/* dummy */
	return SUCCESS;
}

DBA_SYNC_FUNC(flatfile)
{
	/* dummy */
	return SUCCESS;
}

DBA_INFO_FUNC(flatfile)
{
	return estrdup(flatfile_version());
}

#endif
