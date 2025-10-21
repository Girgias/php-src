--TEST--
SPL: spl_autoload_functions()
--FILE--
<?php

function SplAutoloadTest1($name) {}
function SplAutoloadTest2($name) {}

var_dump(spl_autoload_functions());

spl_autoload_register();

var_dump(spl_autoload_functions());

spl_autoload_register('SplAutoloadTest1');
spl_autoload_register('SplAutoloadTest2');
spl_autoload_register('SplAutoloadTest1');

var_dump(spl_autoload_functions());

spl_autoload_unregister('SplAutoloadTest1');

var_dump(spl_autoload_functions());

spl_autoload_unregister('spl_autoload_call');

var_dump(spl_autoload_functions());

spl_autoload_register();

var_dump(spl_autoload_functions());

spl_autoload_unregister('spl_autoload');

var_dump(spl_autoload_functions());

?>
--EXPECTF--
array(0) {
}
array(1) {
  [0]=>
  string(12) "spl_autoload"
}
array(3) {
  [0]=>
  string(12) "spl_autoload"
  [1]=>
  string(16) "SplAutoloadTest1"
  [2]=>
  string(16) "SplAutoloadTest2"
}
array(2) {
  [0]=>
  string(12) "spl_autoload"
  [1]=>
  string(16) "SplAutoloadTest2"
}

Deprecated: Using autoload_call_class() as a callback for autoload_unregister_class() is deprecated, to remove all registered autoloaders, call autoload_unregister_class() for all values returned from autoload_list_class() in %s on line %d
array(0) {
}
array(1) {
  [0]=>
  string(12) "spl_autoload"
}
array(0) {
}
