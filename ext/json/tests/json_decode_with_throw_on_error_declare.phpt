--TEST--
Test json_decode() function when throw_on_error declare is present
--FILE--
<?php
declare(throw_on_error=1);

try {
    var_dump(json_decode("{", false, 512));
} catch (JsonException $e) {
    var_dump($e);
}

?>
--EXPECTF--
object(JsonException)#1 (7) {
  ["message":protected]=>
  string(12) "Syntax error"
  ["string":"Exception":private]=>
  string(0) ""
  ["code":protected]=>
  int(4)
  ["file":protected]=>
  string(%d) "%s"
  ["line":protected]=>
  int(%d)
  ["trace":"Exception":private]=>
  array(1) {
    [0]=>
    array(4) {
      ["file"]=>
      string(%d) "%s"
      ["line"]=>
      int(%d)
      ["function"]=>
      string(11) "json_decode"
      ["args"]=>
      array(3) {
        [0]=>
        string(1) "{"
        [1]=>
        bool(false)
        [2]=>
        int(512)
      }
    }
  }
  ["previous":"Exception":private]=>
  NULL
}
