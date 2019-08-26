<?php

namespace DataStruct\Field;

class StringField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'string';

    private $_minLength = null;
    private $_maxLength = null;

    public function validate($data, &$errors = []): bool {

        if ($this->_nullable && $data === null) {
            return true;
        }

        if (!is_string($data)) {
            $errors[] = ['error' => 'incorrect-type', 'message' => 'Incorrect data type'];
            return false;
        }

        $length = mb_strlen($data);
        if ($this->_minLength && $length < $this->_minLength) {
            $errors[] = ['error' => 'string-min-length', 'message' => 'String field must be atleast ' . $this->_minLength . ' characters long'];
            return false;
        }
        if ($this->_maxLength && $length > $this->_maxLength) {
            $errors[] = ['error' => 'string-max-length', 'message' => 'String field must be maximumly ' . $this->_maxLength . ' characters long'];
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

        return '';
    }

    public function getExample() {
        try {
            return $this->getDefault();
        } catch (\Exception $e) {}
        return 'Example';
    }

    public function min(int $length) {
        $this->_minLength = $length;
        return $this;
    }

    public function max(int $length) {
        $this->_maxLength = $length;
        return $this;
    }

}