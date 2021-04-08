--TEST--
Test autoload_register(): basic function behavior 001
--FILE--
<?php

echo "*** Testing autoload_register() : basic behavior ***\n";

autoload_register(\AUTOLOAD_FUNCTION, "var_dump");

foo();

?>
--EXPECTF--
*** Testing autoload_register() : basic behavior ***
string(3) "foo"

Fatal error: Call to undefined function foo() in %s on line %d
