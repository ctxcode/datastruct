<?php

namespace DataStruct\Field;

class ObjectField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'object';

    private $_optional = [];
    public $fields = [];

    public function __construct(Array $fields) {
        foreach ($fields as $fn => $field) {
            if (!is_string($fn)) {
                throw new \Exception('ObjectField: tried to add field without a fieldname');
            }
            if (!($field instanceof \DataStruct\FieldInterface)) {
                throw new \Exception('ObjectField: tried to add field that was not an instance of FieldInterface');
            }
            $this->fields[$fn] = $field;
        }
    }

    public function getField($fn) {
        if (!isset($this->fields[$fn])) {
            throw new \Exception('Unknown field: ' . $fn);
        }
        return $this->fields[$fn];
    }

    public function validate($data, &$errors = []): bool {

        if (!is_array($errors)) {
            $errors = [];
        }

        if ($this->isNullable() && $data === null) {
            return true;
        }

        if (!is_object($data)) {
            $errors[] = ['error' => 'incorrect-type', 'message' => 'Incorrect data type'];
            return false;
        }

        $countErrors = count($errors);

        $fieldnames = array_keys($this->fields);

        foreach ($fieldnames as $fn) {
            $field = $this->fields[$fn];
            $newErrors = [];
            if (!property_exists($data, $fn)) {
                if (in_array($fn, $this->_optional, true)) {
                    continue;
                }
                $newErrors[] = ['error' => 'missing-field', 'message' => 'Missing field'];
            } else {
                $field->validate($data->$fn, $newErrors);
            }
            foreach ($newErrors as $err) {
                $err['field'] = isset($err['field']) ? $fn . '.' . $err['field'] : $fn;
                $errors[] = $err;
            }
        }

        if (count($errors) > $countErrors) {
            return false;
        }

        return true;
    }

    public function fixData($data) {
        if ($this->validate($data)) {
            return $data;
        }

        if (!is_object($data)) {
            return $this->getDefault();
        }

        $result = json_decode(json_encode($data));

        foreach ($this->fields as $fn => $field) {
            if (!property_exists($data, $fn)) {
                if (in_array($fn, $this->_optional, true)) {
                    continue;
                }
                $data->$fn = null;
            }
            $result->$fn = $field->fixData($data->$fn);
        }

        return $result;
    }

    public function getDefault($depth = 0) {
        if ($this->hasDefault()) {
            return json_decode($this->getDefault());
        }

        $result = (object) [];
        foreach ($this->fields as $fn => $field) {
            try {
                $result->$fn = $field->getDefault($depth + 1);
            } catch (\Exception $e) {
                $f = $fn . (isset($e->field) ? '.' . $e->field : '');
                $errorMessagePrefix = $depth === 0 ? ('Field "' . $f . '" : ') : '';
                $ex = new \Exception($errorMessagePrefix . $e->getMessage());
                $ex->field = $f;
                throw $ex;
            }
        }
        return $result;
    }

    public function getExample() {
        $result = (object) [];

        foreach ($this->fields as $fn => $field) {
            $result->$fn = $field->getExample();
        }

        return $result;
    }

    public function allRequired() {
        $this->_optional = [];
        return $this;
    }

    public function allOptional() {
        $this->_optional = array_keys($this->fields);
        return $this;
    }

    public function required(Array $fields) {
        $fields = array_unique($fields);
        $this->fieldsExistCheck($fields, 'required() -> ');
        $this->_optional = array_values(array_diff($this->_optional, $fields));
        return $this;
    }

    public function optional(Array $fields) {
        $this->fieldsExistCheck($fields, 'optional() -> ');
        $this->_optional = array_unique(array_merge($this->_optional, $fields));
        return $this;
    }

    private function fieldsExistCheck(Array $fields, $errorPrefix = '') {
        foreach ($fields as $field) {
            if (!isset($this->fields[$field])) {
                $this->error(new \Exception($errorPrefix . 'Unknown field "' . $field . '"'));
            }
        }
        return true;
    }

}