--TEST--
Test autoload_register(): basic behavior 002
--FILE--
<?php
/**
 * This tests the proper fallback behavior of function calls that are not "use"d.
 *
 * Only the global function name should be attempted to be autoloaded
 */
namespace Bar {
echo "*** Testing autoload_register() : basic behavior ***\n";

autoload_register(\AUTOLOAD_FUNCTION, "var_dump");

Foo();

}

?>
--EXPECTF--
*** Testing autoload_register() : basic behavior ***
string(3) "Foo"

Fatal error: Call to undefined function Bar\Foo() in %s on line %d
