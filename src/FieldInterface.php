<?php

namespace DataStruct;

interface FieldInterface {

    public function validate($data, &$errors = []): bool;
    public function fixData($data);
    public function getExample();
    public function getDefault();

}