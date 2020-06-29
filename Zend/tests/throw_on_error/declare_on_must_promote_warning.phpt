--TEST--
throw_on_error declare statement promote warning
--FILE--
<?php
declare(throw_on_error=1);

try {
    echo $undef;
} catch (\Exception $e) {
    var_dump($e);
}

--EXPECTF--
object(Exception)#1 (7) {
  ["message":protected]=>
  string(25) "Undefined variable $undef"
  ["string":"Exception":private]=>
  string(0) ""
  ["code":protected]=>
  int(2)
  ["file":protected]=>
  string(%d) "%s"
  ["line":protected]=>
  int(5)
  ["trace":"Exception":private]=>
  array(0) {
  }
  ["previous":"Exception":private]=>
  NULL
}
