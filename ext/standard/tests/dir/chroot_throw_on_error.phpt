--TEST--
Test chroot() function error conditions with throw on error declare enabled - Non-existent directory
--SKIPIF--
<?php
if (substr(PHP_OS, 0, 3) == 'WIN') {
    die('skip.. Not valid for Windows');
}

if (!function_exists('chroot')) {
    die('skip.. chroot() not defined in this build');
}
?>
--FILE--
<?php

declare(throw_on_error=1);

echo "*** Testing chroot() : error conditions ***\n";
$directory = __DIR__ . '/idonotexist';

echo "\n-- Pass chroot() an absolute path that does not exist --\n";
try {
    chroot($directory);
} catch (\FileNotFound $e) {
    echo $e->getMessage() . \PHP_EOL;
}

?>
--EXPECTF--
*** Testing chroot() : error conditions ***

-- Pass chroot() an absolute path that does not exist --
File not found: "%sidonotexist"
