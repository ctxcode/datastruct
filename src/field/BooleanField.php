<?php

namespace DataStruct\Field;

class BooleanField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'boolean';

    public function validate($data, &$errors = []): bool {

        if ($this->_nullable && $data === null) {
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
        if ($this->_defaultValue !== null) {
            return $this->_defaultValue;
        }
        if ($this->_nullable) {
            return null;
        }

        return 0;
    }

    public function getExample() {
        return true;
    }

}