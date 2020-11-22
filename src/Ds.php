<?php

namespace DataStruct;

class Ds {

    public static function string() {
        return new \DataStruct\Field\StringField();
    }

    public static function object(...$options) {
        return new \DataStruct\Field\ObjectField(...$options);
    }

    public static function array(...$options) {
        return new \DataStruct\Field\ArrayField(...$options);
    }

    public static function integer() {
        return new \DataStruct\Field\IntegerField();
    }

    public static function float() {
        return new \DataStruct\Field\FloatField();
    }

    public static function boolean() {
        return new \DataStruct\Field\BooleanField();
    }

    public static function registerFormat(...$options) {
        return \DataStruct\Field\StringField::registerFormat(...$options);
    }
}