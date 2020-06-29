--TEST--
Promoted warning by throw_on_error must ignore error_reporting setting
--FILE--
<?php
declare(throw_on_error=1);
ini_set("error_reporting", 0);
try {
    echo $undef;
} catch(Exception $e) {
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
