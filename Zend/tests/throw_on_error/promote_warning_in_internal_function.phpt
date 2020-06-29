--TEST--
throw_on_error declare statement must promote warning emit in internal function
--FILE--
<?php
declare(throw_on_error=1);

try {
    file_get_contents('not_found.txt');
} catch (\Exception $e) {
    var_dump($e);
}

--EXPECTF--
object(Exception)#1 (7) {
  ["message":protected]=>
  string(82) "file_get_contents(not_found.txt): Failed to open stream: No such file or directory"
  ["string":"Exception":private]=>
  string(0) ""
  ["code":protected]=>
  int(2)
  ["file":protected]=>
  string(%d) "%s"
  ["line":protected]=>
  int(5)
  ["trace":"Exception":private]=>
  array(1) {
    [0]=>
    array(4) {
      ["file"]=>
      string(%d) "%s"
      ["line"]=>
      int(5)
      ["function"]=>
      string(17) "file_get_contents"
      ["args"]=>
      array(1) {
        [0]=>
        string(13) "not_found.txt"
      }
    }
  }
  ["previous":"Exception":private]=>
  NULL
}
