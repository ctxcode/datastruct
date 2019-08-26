<?php

namespace DataStruct\Field;

class ArrayField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'array';

    private $_struct = null;

    public function __construct(\DataStruct\FieldInterface $struct) {
        $this->_struct = $struct;
    }

    public function getStruct() {
        return $this->_struct;
    }

    public function getField($fn) {
        return $this->_struct->getField($fn);
    }

    public function validate($data, &$errors = []): bool {

        if ($this->_nullable && $data === null) {
            return true;
        }

        if (!is_array($data)) {
            $errors[] = ['error' => 'incorrect-type', 'message' => 'Incorrect data type'];
            return false;
        }

        $countErrors = count($errors);

        $expectedKey = 0;
        foreach ($data as $k => $v) {
            if ($expectedKey !== $k) {
                $errors[] = ['error' => 'incorrect-type', 'message' => 'Invalid array key', 'field' => $k . ''];
                continue;
            }
            $expectedKey++;

            $newErrors = [];
            $this->_struct->validate($v, $newErrors);
            foreach ($newErrors as $err) {
                $err['field'] = isset($err['field']) ? $k . '.' . $err['field'] : $k;
                $errors[] = $err;
            }
        }

        if (count($errors) > $countErrors) {
            return false;
        }

        return true;
    }

    public function getDefault() {
        if ($this->_defaultValue !== null) {
            return $this->_defaultValue;
        }

        if ($this->_nullable) {
            return null;
        }

        return [];
    }

    public function fixData($data) {
        if ($this->validate($data)) {
            return $data;
        }

        if (is_array($data)) {
            $result = [];
            foreach ($data as $k => $v) {
                $result[] = $this->_struct->fixData($v);
            }
            return $result;
        }

        if ($this->_defaultValue !== null) {
            return $this->_defaultValue;
        }
        if ($this->_nullable) {
            return null;
        }

        return [];
    }

    public function getExample() {
        $result = [];
        $count = 2;
        for ($i = 0; $i < $count; $i++) {
            $result[] = $this->_struct->getExample();
        }
        return $result;
    }

}