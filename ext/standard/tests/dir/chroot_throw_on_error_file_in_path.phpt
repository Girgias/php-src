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
$directory = __FILE__;

echo "\n-- Pass chroot() a non directory --\n";
try {
    chroot($directory);
} catch (\NotDirectory $e) {
    echo $e->getMessage() . \PHP_EOL;
}

?>
--EXPECTF--
*** Testing chroot() : error conditions ***

-- Pass chroot() a non directory --
"%schroot_throw_on_error_file_in_path.php" is not a directory
