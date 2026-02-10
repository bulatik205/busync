<?php
class fieldsValidator
{
    /*
    $fields = [
        'first_name', strign 2 < n < 72
        'last_name', strign 2 < n < 72
        'father_name', strign 2 < n < 72
        'business_type', strign 2 < n < 4
        'business_site', strign 10 n < 100
        'phone' number 4 < n < 20
    ];

    */
    public array $update;


    public function __construct(array $update)
    {
        $this->update = $update;
    }

    public function validate()
    {
        if (
            !is_string($this->update['first_name']) || 
            !is_string($this->update['last_name']) || 
            !is_string($this->update['father_name']) || 
            !is_string($this->update['business_type']) || 
            !is_string($this->update['business_site']) || 
            !is_string($this->update['phone'])
        ) {
            return false;
        }

        if (
            strlen($this->update['first_name']) < 2 ||
            strlen($this->update['first_name']) > 72 ||
            strlen($this->update['last_name']) < 2 ||
            strlen($this->update['last_name']) > 72 ||
            strlen($this->update['father_name']) < 2 ||
            strlen($this->update['father_name']) > 72 ||
            strlen($this->update['business_type']) < 2 ||
            strlen($this->update['business_type']) > 4 ||
            strlen($this->update['business_site']) < 10 ||
            strlen($this->update['business_site']) > 100 ||
            strlen($this->update['phone']) < 4 ||
            strlen($this->update['phone']) > 20
        ) {
            return false;
        }

        return true;
    }
}