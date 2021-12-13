--TEST--
Avoid function confusion when fallback is used.
--FILE--
<?php
namespace Foo {
    function bar() {
        var_dump(is_callable('Foo\\count'));
        echo count([1,2,3,4,5]) . "\n";
        var_dump(is_callable('Foo\\count'));
    }
}

namespace {
    \Foo\bar();
    eval('namespace Foo; function count() { return -1; }');
}
?>
--EXPECT--
false
5
true
Exception: cannot re-register function Foo\count, fallback to root has been used.
