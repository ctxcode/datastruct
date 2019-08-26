<?php

namespace DataStruct;

abstract class Field {

    protected $type;
    public $_nullable = false;
    public $_defaultValue = null;

    public function nullable($bool = true) {
        $this->_nullable = $bool;
        return $this;
    }

    public function default($value) {
        if (!$this->validate($value, $errors)) {
            throw new \Exception('setDefault value in DataStruct is invalid. Value: ' . json_encode($value) . ' for Class: "' . get_class($this) . '". Errors: ' . json_encode($errors));
        }
        if (is_object($value)) {
            $value = clone ($value);
        }
        $this->_defaultValue = $value;
        return $this;
    }

    public function getDefault() {
        if ($this->_defaultValue !== null) {
            return $this->_defaultValue;
        }

        if ($this->_nullable) {
            return null;
        }

        throw new \Exception('Field has no default value');
    }

}