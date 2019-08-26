<?php

namespace DataStruct\Field;

class ObjectField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'object';

    private $interfaces = [];
    private $usesInterface = null;
    private $usesFields = null;
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
        return $this->fields[$fn] ?? null;
    }

    public function validate($data, &$errors = []): bool {

        if ($this->_nullable && $data === null) {
            return true;
        }

        if (!is_object($data)) {
            $errors[] = ['error' => 'incorrect-type', 'message' => 'Incorrect data type'];
            return false;
        }

        $countErrors = count($errors);

        $fieldnames = $this->usesFields ? $this->usesFields : array_keys($this->fields);

        foreach ($fieldnames as $fn) {
            $field = $this->fields[$fn];
            $newErrors = [];
            if (!isset($data->$fn)) {
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
            if ($this->_defaultValue !== null) {
                return clone ($this->_defaultValue);
            }

            if ($this->_nullable) {
                return null;
            }
        }

        $result = (object) [];

        if (!is_object($data)) {
            $data = (object) [];
        }

        foreach ($this->fields as $fn => $field) {
            if (!isset($data->$fn)) {
                $data->$fn = null;
            }
            $result->$fn = $field->fixData($data->$fn);
        }

        return $result;
    }

    public function getDefault() {
        if ($this->_defaultValue !== null) {
            return clone ($this->_defaultValue);
        }

        if ($this->_nullable) {
            return null;
        }

        $result = (object) [];
        foreach ($this->fields as $fn => $field) {
            try {
                $result->$fn = $field->getDefault();
            } catch (\Exception $e) {
                $f = $fn . (isset($e->field) ? '.' . $e->field : '');
                $ex = new \Exception('Field "' . $f . '" : ' . $e->getMessage());
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

    public function addInterface($name, Array $fields) {
        $this->validateInterfaceFields($fields);
        $this->interfaces[$name] = $fields;

        return $this;
    }

    private function validateInterfaceFields(Array $fields) {
        foreach ($fields as $field) {
            if (!is_string($field)) {
                throw new \Exception('Interface fields must be an array of strings only');
            }
            if (!isset($this->fields[$field])) {
                throw new \Exception('Unknown field: ' . $field);
            }
        }
    }

    public function useInterface($name) {
        if (!isset($this->interfaces[$name])) {
            throw new \Exception('Unknown interface: ' . $name);
        }

        $this->usesInterface = $name;
        $this->usesFields = $this->interfaces[$name];

        return $this;
    }

    public function only(Array $fields) {
        $this->validateInterfaceFields($fields);
        $this->usesInterface = null;
        $this->usesFields = $fields;

        return $this;
    }

}