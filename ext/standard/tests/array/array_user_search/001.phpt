--TEST--
Test array_user_search()
--FILE--
<?php

$data = [
    'hello',
    'welcome',
    5,
    'key1' => 25.6,
    'key2' => null,
    new stdClass(),
];

function cmp(mixed $value, mixed $entry): bool {
    return get_debug_type($value) === get_debug_type($entry);
}

$key = array_user_search(42, $data, 'cmp');
var_dump($key);

$key = array_user_search(13.4, $data, 'cmp');
var_dump($key);

$key = array_user_search([], $data, 'cmp');
var_dump($key);

?>
--EXPECT--
int(2)
string(4) "key1"
NULL
