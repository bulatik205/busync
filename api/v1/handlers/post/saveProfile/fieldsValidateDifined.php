<?php
class fieldsValidateDifined
{
    public array $fields;
    public array $update;

    public function __construct(array $fields, array $update)
    {
        $this->fields = $fields;
        $this->update = $update;
    }

    public function validate(): bool
    {
        /*
        * Sorry about O(n^2)
        */
        foreach ($this->update as $key) {
            $isDefined = false;

            foreach ($this->fields as $field) {
                if ($field !== $key) {
                    $isDefined = true;
                    break;
                }
            }

            if (!$isDefined) {
                return false;
                break;
            }
        }

        return true;
    }
}
