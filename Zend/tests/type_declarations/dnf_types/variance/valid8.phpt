--TEST--
Replacing iterable type with intersection type in DUT
--FILE--
<?php

abstract class MyIterator implements Traversable {}

class Test {
    function method1(): iterable|string {}
    function method2(): iterable|int {}
}
class Test2 extends Test {
    function method1(): (X&Traversable)|string {}
    function method2(): (X&MyIterator)|int {}
}

?>
===DONE===
--EXPECT--
===DONE===
