--TEST--
Bug GH-10891 (dba_insert() for ini files can result in duplicate keys)
--EXTENSIONS--
dba
--SKIPIF--
<?php
require_once __DIR__ . '/setup/setup_dba_tests.inc';
check_skip('tcadb');
?>
--FILE--
<?php
require_once __DIR__ . '/setup/setup_dba_tests.inc';
$db_name = __DIR__ . DIRECTORY_SEPARATOR . 'GH10891.tcadb.tch';

touch($db_name);
$db = dba_open($db_name, "wl", "tcadb");
assert($db);

var_dump(dba_fetch("nonexisting", $db));
var_dump(dba_insert("nonexisting", "newvalue", $db));
var_dump(dba_insert("nonexisting", "newvalue", $db));

dba_close($db);
?>
--EXPECT--
bool(false)
bool(true)
bool(false)
--CLEAN--
<?php
require_once __DIR__ . '/setup/setup_dba_tests.inc';
$db_name = __DIR__ . DIRECTORY_SEPARATOR . 'GH10891.tcadb.tch';
cleanup_standard_db($db_name);
?>
