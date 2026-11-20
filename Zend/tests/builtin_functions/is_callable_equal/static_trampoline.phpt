--TEST--
is_callable_equal(): static __callStatic() trampoline methods callables.
--FILE--
<?php

class C {
	public static function __callStatic(string $name, array $args) {}
}
$obj = new C();
$obj2 = new C();

echo "Variations of trampoline C::foo():\n";
$fns = [
	'C::foo',
	['C', 'foo'],
	[$obj, 'foo'],
	[$obj2, 'foo'],
	C::foo(...),
	C::foo(...),
	$obj::foo(...),
	$obj::foo(...),
	$obj2::foo(...),
	$obj2::foo(...),
];
foreach ($fns as $fn1) {
	foreach ($fns as $fn2) {
	try {
		if (is_callable_equal($fn1, $fn2) === false) {
			echo 'Unexpected unequal callables:', "\n";
			var_dump($fn1, $fn2);
		}
	} catch (Throwable $e) {
		echo 'Unexpected non callables:', "\n";
		echo $e::class, ': ', $e->getMessage(), "\n";
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
