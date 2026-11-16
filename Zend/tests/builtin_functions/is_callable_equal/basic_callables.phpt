--TEST--
is_callable_equal(): with basic callables.
--FILE--
<?php

echo "Free standing functions:\n";
echo "Variations of strlen():\n";
$free_internal_fn_str = 'strlen';
$free_internal_fn_closure = strlen(...);
$free_internal_fn_closure2 = strlen(...);

var_dump(is_callable_equal($free_internal_fn_str, $free_internal_fn_str));
var_dump(is_callable_equal($free_internal_fn_str, $free_internal_fn_closure));
var_dump(is_callable_equal($free_internal_fn_closure, $free_internal_fn_closure2));

function foo() {}

echo "Variations of userland foo():\n";
$free_userland_fn_str = 'foo';
$free_userland_fn_closure = foo(...);
$free_userland_fn_closure2 = foo(...);

var_dump(is_callable_equal($free_userland_fn_str, $free_userland_fn_str));
var_dump(is_callable_equal($free_userland_fn_str, $free_userland_fn_closure));
var_dump(is_callable_equal($free_userland_fn_closure, $free_userland_fn_closure2));

?>
--EXPECT--
Free standing functions:
Variations of strlen():
bool(true)
bool(true)
bool(true)
Variations of userland foo():
bool(true)
bool(true)
bool(true)
