<?php

namespace DataStruct\Field;

class BooleanField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'boolean';

    public function validate($data, &$errors = []): bool {

        if (!is_array($errors)) {
            $errors = [];
        }

        if ($this->isNullable() && $data === null) {
            return true;
        }

        if (!is_bool($data)) {
            $errors[] = ['error' => 'incorrect-type', 'message' => 'Incorrect data type'];
            return false;
        }

        return true;
    }

    public function fixData($data) {
        if ($this->validate($data)) {
            return $data;
        }

        return $this->getDefault();
    }

    public function getExample() {
        return true;
    }

}