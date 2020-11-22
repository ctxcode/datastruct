<?php

namespace DataStruct;

abstract class Field {

    protected $type;
    private $_name = false;
    private $_nullable = false;
    private $_hasExplicitDefault = false;
    private $_explicitDefault = null;

    public function nullable($bool = true) {
        $this->_nullable = $bool;
        return $this;
    }

    public function isNullable() {
        return $this->_nullable;
    }

    public function default($value) {
        if (!$this->validate($value, $errors)) {
            throw new \Exception('setDefault value in DataStruct is invalid. Value: ' . json_encode($value) . ' for Class: "' . get_class($this) . '". Errors: ' . json_encode($errors));
        }
        if (is_object($value)) {
            $value = json_encode($value);
        }
        $this->_hasExplicitDefault = true;
        $this->_explicitDefault = $value;
        return $this;
    }

    public function hasDefault() {
        return $this->_hasExplicitDefault || $this->_nullable;
    }

    public function hasExplicitDefault() {
        return $this->_hasExplicitDefault;
    }

    public function getDefault($depth = 0) {
        if ($this->_hasExplicitDefault) {
            return $this->_explicitDefault;
        }

        if ($this->_nullable) {
            return null;
        }

        throw new \Exception('Field has no default value');
    }

    public function clearDefault() {
        $this->_hasExplicitDefault = false;
        $this->_explicitDefault = null;
        return $this;
    }

    public function name(String $name = null) {
        $this->_name = $name;
        return $this;
    }
    public function hasName() {
        return $this->_name ? true : false;
    }
    public function clearName() {
        $this->_name = null;
        return $this;
    }

    public function error(\Exception $ex) {
        $prefix = 'DataStruct ';
        if ($this->hasName()) {
            $prefix .= '"' . ($this->_name) . '" ';
        }
        $prefix .= 'error :: ';
        $msg = $ex->getMessage();
        throw new \Exception($prefix . $msg, 0, $ex);
    }

}