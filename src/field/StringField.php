<?php

namespace DataStruct\Field;

class StringField extends \DataStruct\Field implements \DataStruct\FieldInterface {

    protected $type = 'string';

    private $_minLength = null;
    private $_maxLength = null;
    private $_matchRegex = null;
    private $_datetimeFormat = null;
    private $_in = null;
    private $_useCustomFormats = [];
    private static $_customFormats = [];

    public function validate($data, &$errors = []): bool {

        if (!is_array($errors)) {
            $errors = [];
        }

        if ($this->isNullable() && $data === null) {
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
        if ($this->_in && !in_array($data, $this->_in, true)) {
            $errors[] = ['error' => 'string-in', 'message' => 'String field must have one the following values: ' . implode(', ', $this->_in)];
            return false;
        }
        if ($this->_matchRegex && !preg_match($this->_matchRgex, $data)) {
            $errors[] = ['error' => 'string-match-regex', 'message' => 'String field must match our regex format', 'format' => $this->_matchRegex];
            return false;
        }
        if ($this->_datetimeFormat && date($this->_datetimeFormat, strtotime($data)) !== $data) {
            $errors[] = ['error' => 'string-datetime-format', 'message' => 'String field must be a valid date/time and match our date/time format', 'format' => $this->_datetimeFormat];
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

    public function matchRegex($regex) {
        $this->_matchRegex = $regex;
        return $this;
    }

    public function datetimeFormat($format) {
        $this->_datetimeFormat = $format;
        return $this;
    }

    public function in(Array $options) {
        $this->_in = $options;
        return $this;
    }

    public function format($name) {
        if (!isset(static::$_customFormats[$name])) {
            throw new \Exception('Custom format "' . $name . '" does not exist');
        }
        $this->_useCustomFormats[] = $name;
        $this->_useCustomFormats = array_unique($this->_useCustomFormats);
        return $this;
    }

    public static function registerFormat($name, $func) {
        if (isset(static::$_customFormats[$name])) {
            throw new \Exception('Custom format "' . $name . '" already exists');
        }
        static::$_customFormats[$name] = $func;
    }

}