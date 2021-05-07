--TEST--
throw_on_error declare statement must promote warning emit in user defined function
--FILE--
<?php
declare(throw_on_error=1);

function foo() {
    echo $undef;
}

try {
    foo();
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
  array(1) {
    [0]=>
    array(4) {
      ["file"]=>
      string(%d) "%s"
      ["line"]=>
      int(9)
      ["function"]=>
      string(3) "foo"
      ["args"]=>
      array(0) {
      }
    }
  }
  ["previous":"Exception":private]=>
  NULL
}
