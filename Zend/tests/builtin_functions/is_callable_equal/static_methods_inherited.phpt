--TEST--
is_callable_equal(): static methods callables that are inherited.
--EXTENSIONS--
zend_test
--FILE--
<?php

class C {
	public static function foo() {}
}
$obj = new C();

class C2 extends C {}
$obj2 = new C2();

$fn_parents = [
	['C', 'foo'],
	[$obj, 'foo'],
];
$fn_children = [
	['C2', 'foo'],
	[$obj2, 'foo'],
];
foreach ($fn_parents as $fn1) {
	foreach ($fn_children as $fn2) {
		if (is_callable_equal($fn1, $fn2) === true) {
			echo 'Unexpected equal callables:', "\n";
			var_dump($fn1, $fn2);
		}
	}
}

?>
DONE
--EXPECT--
DONE
