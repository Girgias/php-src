--TEST--
Literal Strings
--ENV--
TAINTED=tainted
--FILE--
<?php
var_dump(
    is_literal("strings"),
    is_literal($_ENV["TAINTED"]));
?>
--EXPECT--
bool(true)
bool(false)
