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

#ifndef PHP_IO_EXCEPTION
#define PHP_IO_EXCEPTION

PHP_MINIT_FUNCTION(io_exceptions);

BEGIN_EXTERN_C()

extern PHPAPI zend_class_entry *zend_ce_filesystem;
extern PHPAPI zend_class_entry *zend_ce_network;
extern PHPAPI zend_class_entry *zend_ce_filesystem_error;
extern PHPAPI zend_class_entry *zend_ce_insufficient_permissions;
extern PHPAPI zend_class_entry *zend_ce_temporary_failure;

END_EXTERN_C()

#endif /* PHP_IO_EXCEPTION */
