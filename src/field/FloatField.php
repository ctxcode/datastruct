<?php

namespace DataStruct\Field;

class FloatField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'float';

    private $_min = null;
    private $_max = null;

    public function validate($data, &$errors = []): bool {

        if ($this->_nullable && $data === null) {
            return true;
        }

        if (!is_double($data)) {
            $errors[] = ['error' => 'incorrect-type', 'message' => 'Incorrect data type'];
            return false;
        }

        if ($this->_min && $data < $this->_min) {
            $errors[] = ['error' => 'integer-min', 'message' => 'Float field must be larger or equal to ' . $this->_min . ''];
            return false;
        }
        if ($this->_max && $data > $this->_max) {
            $errors[] = ['error' => 'integer-max', 'message' => 'Float field must be smaller or equal to ' . $this->_max . ''];
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
        return 123.45;
    }

    public function min(double $min) {
        $this->_min = $min;
    }

    public function max(double $max) {
        $this->_max = $max;
    }

}