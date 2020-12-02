--TEST--
Silence exception with @ operator
--FILE--
<?php

function test1() {
    throw new Exception();
    return true;
}

$var = @test1();

echo "Done\n";
?>
--EXPECT--
