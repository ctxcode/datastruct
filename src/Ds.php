<?php

namespace DataStruct;

class Ds {

    public static function string(...$options) {
        return new \DataStruct\Field\StringField(...$options);
    }

    public static function object(...$options) {
        return new \DataStruct\Field\ObjectField(...$options);
    }

    public static function array(...$options) {
        return new \DataStruct\Field\ArrayField(...$options);
    }

    public static function integer(...$options) {
        return new \DataStruct\Field\IntegerField(...$options);
    }

    public static function float(...$options) {
        return new \DataStruct\Field\FloatField(...$options);
    }

    public static function boolean(...$options) {
        return new \DataStruct\Field\BooleanField(...$options);
    }

    public static function registerFormat(...$options) {
        return \DataStruct\Field\StringField::registerFormat(...$options);
    }
}