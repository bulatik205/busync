<?php
class fieldsValidator
{
    public array $update;

    public function __construct(array $update)
    {
        $this->update = $update;
    }

    public function validate() : bool {
        $isValid = true;

        foreach ($this->update as $key => $value) {
            if (!isset($value) || strlen($value) > 100 || strlen($value) < 2) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }
}