--TEST--
Check that assigned class map works
--FILE--
<?php

function test_map(array $map) {
	try {
		autoload_register_class_map($map);
	} catch (\Throwable $e) {
		echo $e::class, ': ', $e->getMessage(), PHP_EOL;
	}
}

$pathTo = __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
$map = [
    'Foo' => $pathTo . "Foo.inc",
    'not a symbol name' => $pathTo . "Bar.inc",
];
test_map($map);

$map = [
    'Foo' => $pathTo . "Foo.inc",
    0 => $pathTo . "Bar.inc",
];
test_map($map);

$map = [
    'Foo' => 0,
];
test_map($map);

$map = [
    'Foo' => "",
];
test_map($map);

$map = [
    'Foo' => "Path\0hasNull",
];
test_map($map);

?>
--EXPECT--
ValueError: autoload_register_class_map(): Argument #1 ($classes) key "not a symbol name" is not a valid class name
ValueError: autoload_register_class_map(): Argument #1 ($classes) must only have string keys
TypeError: autoload_register_class_map(): Argument #1 ($classes) path for class "Foo" must be of type string
ValueError: autoload_register_class_map(): Argument #1 ($classes) path for class "Foo" must not be empty
ValueError: autoload_register_class_map(): Argument #1 ($classes) path for class "Foo" must not contain null bytes
