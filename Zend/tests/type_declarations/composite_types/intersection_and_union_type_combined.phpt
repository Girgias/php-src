--TEST--
Test for a complex type with an intersection type and union of a simple type
--FILE--
<?php

interface X {}
interface Y {}

class A implements X, Y {}
class B {}

class Test {
    public X&Y|int $prop1;
    public int|X&Y $prop2;
    public X&Y|B $prop3;
    public B|X&Y $prop4;

    public function foo1(X&Y|int $v): X&Y|int {
        var_dump($v);
        return $v;
    }
    public function foo2(int|X&Y $v): int|X&Y {
        var_dump($v);
        return $v;
    }
    public function bar1(B|X&Y $v): B|X&Y {
        var_dump($v);
        return $v;
    }
    public function bar2(X&Y|B $v): X&Y|B {
        var_dump($v);
        return $v;
    }
}

$test = new Test();
$a = new A();
$b = new B();
$i = 10;

$test->foo1($a);
$test->foo2($a);
$test->foo1($i);
$test->foo2($i);
$test->prop1 = $a;
$test->prop1 = $i;
$test->prop2 = $a;
$test->prop2 = $i;

$test->bar1($a);
$test->bar2($a);
$test->bar1($b);
$test->bar2($b); // This currently fails
$test->prop3 = $a;
$test->prop4 = $b;
$test->prop3 = $a;
$test->prop4 = $b;

?>
===DONE===
--EXPECT--
===DONE===
