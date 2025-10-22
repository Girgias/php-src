--TEST--
Check that assigned class map works
--FILE--
<?php
$pathTo = __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
$classes = [
    'Foo' => $pathTo . "Foo.inc",
    'Bar' => $pathTo . "Bar.inc",
];

autoload_register_class_map($classes);

$f = new Foo();
var_dump($f);
$b = new Bar();
var_dump($b);

?>
--EXPECT--
object(Foo)#1 (0) {
}
object(Bar)#2 (0) {
}
