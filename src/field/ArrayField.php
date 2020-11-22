<?php

namespace DataStruct\Field;

class ArrayField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'array';

    private $_struct = null;
    private $_min = null;
    private $_max = null;

    public function __construct(\DataStruct\FieldInterface $struct) {
        $this->_struct = $struct;
    }

    public function getStruct() {
        return $this->_struct;
    }

    public function validate($data, &$errors = []): bool {

        if (!is_array($errors)) {
            $errors = [];
        }

        if ($this->isNullable() && $data === null) {
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

    public function getDefault($depth = 0) {
        if ($this->hasDefault()) {
            return $this->getDefault();
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

        if ($this->hasDefault()) {
            return $this->getDefault();
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

    public function min(int $count) {
        $this->_min = $count;
        return $this;
    }

    public function max(int $count) {
        $this->_max = $count;
        return $this;
    }

}