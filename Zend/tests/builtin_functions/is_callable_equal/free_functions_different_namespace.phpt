--TEST--
is_callable_equal(): free functions with the same name in different namespaces.
--FILE--
<?php
namespace Foo {
	function f() {}
	$fooFClosure = f(...);
}
namespace Bar {
	function f() {}
	$barFClosure = f(...);
}
namespace {
	function f() {}
	$fClosure = f(...);
}
namespace Foo {
	echo "In namespace Foo:\n";
	var_dump(\is_callable_equal($fClosure, $fooFClosure));
	var_dump(\is_callable_equal($fClosure, $barFClosure));
	var_dump(\is_callable_equal('f', $fooFClosure));
	var_dump(\is_callable_equal('f', $barFClosure));
	var_dump(\is_callable_equal('f', $fClosure));
}
namespace Bar {
	echo "In namespace Bar:\n";
	var_dump(\is_callable_equal($fClosure, $fooFClosure));
	var_dump(\is_callable_equal($fClosure, $barFClosure));
	var_dump(\is_callable_equal('f', $fooFClosure));
	var_dump(\is_callable_equal('f', $barFClosure));
	var_dump(\is_callable_equal('f', $fClosure));
}
namespace {
	echo "In global namespace:\n";
	var_dump(\is_callable_equal($fClosure, $fooFClosure));
	var_dump(\is_callable_equal($fClosure, $barFClosure));
	var_dump(\is_callable_equal('f', $fooFClosure));
	var_dump(\is_callable_equal('f', $barFClosure));
	var_dump(\is_callable_equal('f', $fClosure));
}
?>
--EXPECT--
In namespace Foo:
bool(false)
bool(false)
bool(false)
bool(false)
bool(true)
In namespace Bar:
bool(false)
bool(false)
bool(false)
bool(false)
bool(true)
In global namespace:
bool(false)
bool(false)
bool(false)
bool(false)
bool(true)
