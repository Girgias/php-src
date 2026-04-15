--TEST--
Phar front controller mime type not string/int [cache_list]
--INI--
default_charset=UTF-8
phar.cache_list={PWD}/frontcontroller13.php
--EXTENSIONS--
phar
--ENV--
SCRIPT_NAME=/frontcontroller13.php
REQUEST_URI=/frontcontroller13.php/a.php
PATH_INFO=/a.php
--FILE_EXTERNAL--
files/frontcontroller7.phar
--EXPECTHEADERS--
Content-type: text/html; charset=UTF-8
--EXPECTF--
Fatal error: Uncaught TypeError: Phar::webPhar(): Argument #4 ($mimeTypes) mime type specifier must be of type string|int, null given in %sfrontcontroller13.php:2
Stack trace:
#0 %sfrontcontroller13.php(2): Phar::webPhar('whatever', 'index.php', NULL, Array)
#1 {main}
  thrown in %sfrontcontroller13.php on line 2
