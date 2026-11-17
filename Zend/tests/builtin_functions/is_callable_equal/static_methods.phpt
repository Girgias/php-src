--TEST--
is_callable_equal(): static methods callables.
--EXTENSIONS--
zend_test
--FILE--
<?php

echo "Static methods:\n";
echo "Variations of internal static method():\n";
$obj = new _ZendTestClass();
$fns = [
	'_ZendTestClass::is_object',
	['_ZendTestClass', 'is_object'],
	[$obj, 'is_object'],
	_ZendTestClass::is_object(...),
	_ZendTestClass::is_object(...),
	$obj->is_object(...),
	$obj->is_object(...),
];
foreach ($fns as $fn1) {
	foreach ($fns as $fn2) {
		if (is_callable_equal($fn1, $fn2) === false) {
			echo 'Unexpected unequal callables:', "\n";
			var_dump($fn1, $fn2);
		}
	}
}

class C {
	public static function foo() {}
}
$obj = new C();
$obj2 = new C();

echo "Variations of userland C::foo():\n";
$fns = [
	'C::foo',
	['C', 'foo'],
	[$obj, 'foo'],
	[$obj2, 'foo'],
	C::foo(...),
	C::foo(...),
	$obj->foo(...),
	$obj->foo(...),
	$obj2->foo(...),
	$obj2->foo(...),
];
foreach ($fns as $fn1) {
	foreach ($fns as $fn2) {
		if (is_callable_equal($fn1, $fn2) === false) {
			echo 'Unexpected unequal callables:', "\n";
			var_dump($fn1, $fn2);
		}
	}
}

?>
DONE
--EXPECT--
Static methods:
Variations of internal static method():
Variations of userland C::foo():
DONE
