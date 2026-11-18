--TEST--
is_callable_equal(): instanced __call() trampoline methods callables.
--EXTENSIONS--
zend_test
--FILE--
<?php

class C {
	public function __call(string $name, array $args) {}
}
$obj = new C();
$obj2 = new C();

echo "Variations of trampoline C::foo():\n";
$fns = [
	'[$obj, \'foo\']' => [$obj, 'foo'],
	'[$obj2, \'foo\']' => [$obj2, 'foo'],
	'$obj->foo(...)' => $obj->foo(...),
	'$obj->foo(...)' => $obj->foo(...),
	'$obj2->foo(...)' => $obj2->foo(...),
	'$obj2->foo(...)' => $obj2->foo(...),
];
foreach ($fns as $fn1str => $fn1) {
	foreach ($fns as $fn2str => $fn2) {
		echo 'var_dump(is_callable_equal(' . $fn1str . ', ' . $fn2str . "));\n" ;
		var_dump(is_callable_equal($fn1, $fn2));
	}
}

?>
DONE
--EXPECT--
Variations of trampoline C::foo():
var_dump(is_callable_equal([$obj, 'foo'], [$obj, 'foo']));
bool(true)
var_dump(is_callable_equal([$obj, 'foo'], [$obj2, 'foo']));
bool(false)
var_dump(is_callable_equal([$obj, 'foo'], $obj->foo(...)));
bool(false)
var_dump(is_callable_equal([$obj, 'foo'], $obj2->foo(...)));
bool(false)
var_dump(is_callable_equal([$obj2, 'foo'], [$obj, 'foo']));
bool(false)
var_dump(is_callable_equal([$obj2, 'foo'], [$obj2, 'foo']));
bool(true)
var_dump(is_callable_equal([$obj2, 'foo'], $obj->foo(...)));
bool(false)
var_dump(is_callable_equal([$obj2, 'foo'], $obj2->foo(...)));
bool(false)
var_dump(is_callable_equal($obj->foo(...), [$obj, 'foo']));
bool(false)
var_dump(is_callable_equal($obj->foo(...), [$obj2, 'foo']));
bool(false)
var_dump(is_callable_equal($obj->foo(...), $obj->foo(...)));
bool(true)
var_dump(is_callable_equal($obj->foo(...), $obj2->foo(...)));
bool(false)
var_dump(is_callable_equal($obj2->foo(...), [$obj, 'foo']));
bool(false)
var_dump(is_callable_equal($obj2->foo(...), [$obj2, 'foo']));
bool(false)
var_dump(is_callable_equal($obj2->foo(...), $obj->foo(...)));
bool(false)
var_dump(is_callable_equal($obj2->foo(...), $obj2->foo(...)));
bool(true)
DONE
