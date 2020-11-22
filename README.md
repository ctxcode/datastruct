
# DataStruct (WIP)

DataStruct is a package to validate and/or autocorrect the data given.

## Install
```
composer require ctxkiwi/datastruct
```

## Basic usage

```php
$orderStruct = Ds::object([
    'id' => Ds::integer()->min(1),
    'user' => Ds::object([
        'id' => Ds::integer()->min(1),
        'email' => Ds::string()->format('email'),
    ]),
    'price' => Ds::float()->min(0),
    'comment' => Ds::string()->nullable(),
    'paid' => Ds::boolean(),
    'created_at' => Ds::string()->dateFormat('Y-m-d H:i:s'),
]);

$order = [
    'id' => 1,
    'user' => [
        'id' => 123,
        'email' => 'example@datastruct.tld',
    ],
    'price' => 1.23,
    'comment' => null,
    'paid' => true,
    'created_at' => date('Y-m-d') . ' 12:00:00',
];

$errors = [];
if ($orderStruct->validate($order, $errors)) {
    echo 'Success :)';
} else {
    echo 'Failed :(';
    var_dump($errors);
}


```

Or more simple ones, like

```php
$emailStruct = Ds::string()->format('email');
if($emailStruct->validate('test@example.com')){ ... }
```

## Shortcut class

To make coding easier, you can create this class to type just Ds instead of DataStrcut\Ds everytime

```php
<?php

class Ds extends \DataStruct\Ds {
    
    // You can also add some shortcut types here
    // Like:
    public static function color(){
        return static::string()->matchRegex('/^#[0-9A-F]{6}$/'); // e.g. #FF00CC
    }

}
```

## Field types

```
Object: For key value
Array
Integer
Float
String
Boolean
```

## String Formats

Todo

## API

Todo



