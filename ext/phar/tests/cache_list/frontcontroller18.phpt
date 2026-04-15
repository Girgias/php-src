--TEST--
Phar front controller $_SERVER munging failure [cache_list]
--INI--
phar.cache_list={PWD}/frontcontroller18.php
--EXTENSIONS--
phar
--ENV--
SCRIPT_NAME=/frontcontroller18.php
REQUEST_URI=/frontcontroller18.php/fronk.gronk
PATH_INFO=/fronk.gronk
--FILE_EXTERNAL--
files/frontcontroller9.phar
--EXPECTF--
Fatal error: Uncaught ValueError: Phar::mungServer(): Argument #1 ($variables) must not be empty in %sfrontcontroller18.php:2
Stack trace:
#0 %sfrontcontroller18.php(2): Phar::mungServer(Array)
#1 {main}
  thrown in %sfrontcontroller18.php on line 2
