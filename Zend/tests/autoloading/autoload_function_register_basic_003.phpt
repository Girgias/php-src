--TEST--
Test autoload_register(): basic behavior 003
--FILE--
<?php
/**
 * This tests the proper fallback behavior of function calls that are "use"d.
 *
 * Only the namespaced function name should be attempted to be autoloaded
 */
namespace Bar {

use function Baz\foo;

echo "*** Testing autoload_register() : basic behavior ***\n";

\autoload_register(\AUTOLOAD_FUNCTION, "var_dump");

foo();

}

?>
--EXPECTF--
*** Testing autoload_register() : basic behavior ***
string(7) "Baz\foo"

Fatal error: Call to undefined function Baz\foo() in %s on line %d
