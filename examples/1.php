<?php

include __DIR__ . '/../vendor/autoload.php';

use \DataStruct\Ds;

Ds::registerFormat('email', function ($string) {
    // ...
    return true;
});

Ds::registerFormat('password', function ($string) {
    // ...
    return true;
});

$userDs = Ds::object([
    'id' => Ds::integer()->min(0),
    'email' => Ds::string()->format('email'),
    'password' => Ds::string()->format('password'),
    'name' => Ds::string()->min(2)->max(24)->nullable(),
]);

$user = (object) [
    'id' => 123,
    'email' => 'test@validate.com',
    'password' => 'mypass123',
    'name' => 'Johny',
];

if ($userDs->validate($user, $errors)) {
    echo 'Success :)';
} else {
    echo 'Failed :(';
    var_dump($errors);
}
