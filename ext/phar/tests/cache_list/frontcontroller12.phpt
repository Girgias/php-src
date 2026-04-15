--TEST--
Phar front controller mime type unknown int [cache_list]
--INI--
default_charset=UTF-8
phar.cache_list={PWD}/frontcontroller12.php
--EXTENSIONS--
phar
--ENV--
SCRIPT_NAME=/frontcontroller12.php
REQUEST_URI=/frontcontroller12.php/a.php
PATH_INFO=/a.php
--FILE_EXTERNAL--
files/frontcontroller6.phar
--EXPECTHEADERS--
Content-type: text/html; charset=UTF-8
--EXPECTF--
Fatal error: Uncaught ValueError: Phar::webPhar(): Argument #4 ($mimeTypes) mime type specifier must be one of Phar::PHP or Phar::PHPS in %sfrontcontroller12.php:2
Stack trace:
#0 %sfrontcontroller12.php(2): Phar::webPhar('whatever', 'index.php', NULL, Array)
#1 {main}
  thrown in %sfrontcontroller12.php on line 2
