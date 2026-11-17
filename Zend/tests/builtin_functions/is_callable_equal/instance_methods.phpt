--TEST--
is_callable_equal(): instance methods callables.
--EXTENSIONS--
zend_test
--FILE--
<?php

echo "Static methods:\n";
echo "Variations of internal instance method:\n";
$obj = new _ZendTestClass();
$obj2 = new _ZendTestClass();
$fns = [
	'[$obj, \'takesUnionType\']' => [$obj, 'takesUnionType'],
	'[$obj2, \'takesUnionType\']' => [$obj2, 'takesUnionType'],
	'$obj->takesUnionType(...)' => $obj->takesUnionType(...),
	'$obj->takesUnionType(...)' => $obj->takesUnionType(...),
	'$obj2->takesUnionType(...)' => $obj2->takesUnionType(...),
	'$obj2->takesUnionType(...)' => $obj2->takesUnionType(...),
];
foreach ($fns as $fn1str => $fn1) {
	foreach ($fns as $fn2str => $fn2) {
		echo 'var_dump(is_callable_equal(' . $fn1str . ', ' . $fn2str . "));\n" ;
		var_dump(is_callable_equal($fn1, $fn2));
	}
}

class C {
	public function foo() {}
}
$obj = new C();
$obj2 = new C();

echo "Variations of userland C::foo():\n";
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
Static methods:
Variations of internal instance method:
var_dump(is_callable_equal([$obj, 'takesUnionType'], [$obj, 'takesUnionType']));
bool(true)
var_dump(is_callable_equal([$obj, 'takesUnionType'], [$obj2, 'takesUnionType']));
bool(false)
var_dump(is_callable_equal([$obj, 'takesUnionType'], $obj->takesUnionType(...)));
bool(true)
var_dump(is_callable_equal([$obj, 'takesUnionType'], $obj2->takesUnionType(...)));
bool(false)
var_dump(is_callable_equal([$obj2, 'takesUnionType'], [$obj, 'takesUnionType']));
bool(false)
var_dump(is_callable_equal([$obj2, 'takesUnionType'], [$obj2, 'takesUnionType']));
bool(true)
var_dump(is_callable_equal([$obj2, 'takesUnionType'], $obj->takesUnionType(...)));
bool(false)
var_dump(is_callable_equal([$obj2, 'takesUnionType'], $obj2->takesUnionType(...)));
bool(true)
var_dump(is_callable_equal($obj->takesUnionType(...), [$obj, 'takesUnionType']));
bool(true)
var_dump(is_callable_equal($obj->takesUnionType(...), [$obj2, 'takesUnionType']));
bool(false)
var_dump(is_callable_equal($obj->takesUnionType(...), $obj->takesUnionType(...)));
bool(true)
var_dump(is_callable_equal($obj->takesUnionType(...), $obj2->takesUnionType(...)));
bool(false)
var_dump(is_callable_equal($obj2->takesUnionType(...), [$obj, 'takesUnionType']));
bool(false)
var_dump(is_callable_equal($obj2->takesUnionType(...), [$obj2, 'takesUnionType']));
bool(true)
var_dump(is_callable_equal($obj2->takesUnionType(...), $obj->takesUnionType(...)));
bool(false)
var_dump(is_callable_equal($obj2->takesUnionType(...), $obj2->takesUnionType(...)));
bool(true)
Variations of userland C::foo():
var_dump(is_callable_equal([$obj, 'foo'], [$obj, 'foo']));
bool(true)
var_dump(is_callable_equal([$obj, 'foo'], [$obj2, 'foo']));
bool(false)
var_dump(is_callable_equal([$obj, 'foo'], $obj->foo(...)));
bool(true)
var_dump(is_callable_equal([$obj, 'foo'], $obj2->foo(...)));
bool(false)
var_dump(is_callable_equal([$obj2, 'foo'], [$obj, 'foo']));
bool(false)
var_dump(is_callable_equal([$obj2, 'foo'], [$obj2, 'foo']));
bool(true)
var_dump(is_callable_equal([$obj2, 'foo'], $obj->foo(...)));
bool(false)
var_dump(is_callable_equal([$obj2, 'foo'], $obj2->foo(...)));
bool(true)
var_dump(is_callable_equal($obj->foo(...), [$obj, 'foo']));
bool(true)
var_dump(is_callable_equal($obj->foo(...), [$obj2, 'foo']));
bool(false)
var_dump(is_callable_equal($obj->foo(...), $obj->foo(...)));
bool(true)
var_dump(is_callable_equal($obj->foo(...), $obj2->foo(...)));
bool(false)
var_dump(is_callable_equal($obj2->foo(...), [$obj, 'foo']));
bool(false)
var_dump(is_callable_equal($obj2->foo(...), [$obj2, 'foo']));
bool(true)
var_dump(is_callable_equal($obj2->foo(...), $obj->foo(...)));
bool(false)
var_dump(is_callable_equal($obj2->foo(...), $obj2->foo(...)));
bool(true)
DONE
