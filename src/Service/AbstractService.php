<?php

namespace App\Service;

abstract class AbstractService
{
    protected array $fields = [];

    public function checkRequestFields(array $requestData): array
    {
        $skippedFields = [];

        if ($this->fields) {
            foreach ($this->fields as $field) {
                if (!in_array($field, array_keys($requestData))) {
                    $skippedFields[] = $field;
                }
            }
        }

        return $skippedFields;
    }

}