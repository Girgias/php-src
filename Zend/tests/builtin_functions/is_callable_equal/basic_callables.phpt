--TEST--
is_callable_equal(): with basic callables.
--FILE--
<?php

echo "Free standing functions:\n";
echo "Variations of strlen():\n";
$free_fn_str = 'strlen';
$free_fn_closure = strlen(...);
$free_fn_closure2 = strlen(...);

var_dump(is_callable_equal($free_fn_str, $free_fn_str));
var_dump(is_callable_equal($free_fn_str, $free_fn_closure));
var_dump(is_callable_equal($free_fn_closure, $free_fn_closure2));

?>
--EXPECT--
Free standing functions:
Variations of strlen():
bool(true)
bool(true)
bool(true)
