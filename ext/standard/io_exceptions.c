/*
   +----------------------------------------------------------------------+
   | Copyright (c) The PHP Group                                          |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: George Peter Banyard <girgias@php.net>                      |
   +----------------------------------------------------------------------+
 */

/* {{{ includes */
#include "php.h"
#include "zend_exceptions.h"
#include "io_exceptions.h"
#include "io_exceptions_arginfo.h"

/* Class entry pointers */
PHPAPI zend_class_entry *zend_ce_filesystem;
PHPAPI zend_class_entry *zend_ce_network;
PHPAPI zend_class_entry *zend_ce_filesystem_error;
PHPAPI zend_class_entry *zend_ce_file_not_found;
PHPAPI zend_class_entry *zend_ce_not_directory;
PHPAPI zend_class_entry *zend_ce_insufficient_permissions;
PHPAPI zend_class_entry *zend_ce_temporary_failure;

PHP_MINIT_FUNCTION(io_exceptions) {
	/* Register interfaces */
	zend_ce_filesystem = register_class_FileSystem(zend_ce_io);
	zend_ce_network = register_class_Network(zend_ce_io);

	/* Register exceptions */
	zend_ce_filesystem_error = register_class_FileSystemError(zend_ce_exception, zend_ce_filesystem);
	zend_ce_file_not_found = register_class_FileNotFound(zend_ce_exception, zend_ce_filesystem);
	zend_ce_not_directory = register_class_NotDirectory(zend_ce_exception, zend_ce_filesystem);
	zend_ce_insufficient_permissions = register_class_InsufficientPermissions(zend_ce_exception, zend_ce_filesystem);

	zend_ce_temporary_failure = register_class_TemporaryFailure(zend_ce_exception, zend_ce_network);

	return SUCCESS;
}

PHPAPI void handle_io_error(int error, const char *path) {
	if (path == NULL) {
		path = "[unknown]";
	}
	switch (error) {
		case ENOENT:
            php_exception_or_warning_docref(NULL, zend_ce_file_not_found, "File not found: \"%s\"", path);
		    break;
		case ENOTDIR:
            php_exception_or_warning_docref(NULL, zend_ce_not_directory, "\"%s\" is not a directory", path);
		    break;
		default:
            php_exception_or_warning_docref(NULL, zend_ce_filesystem_error, "%s (path: \"%s\", errno %d)", strerror(errno), path, errno);
			break;
	}
}
