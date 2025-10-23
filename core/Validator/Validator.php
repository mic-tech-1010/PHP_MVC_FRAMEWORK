<?php

namespace Core\Validator;

use Exception;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): array
    {
        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        if (!empty($this->errors)) {
            throw new Exception(json_encode($this->errors));
        }

        // Return only validated fields
        return array_intersect_key($this->data, $this->rules);
    }

    protected function applyRule(string $field, $value, string $rule): void
    {
        $param = null;

        // Rules like min:3, max:10
        if (strpos($rule, ':') !== false) {
            [$rule, $param] = explode(':', $rule, 2);
        }

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->errors[$field][] = "The {$field} field is required.";
                }
                break;

            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "The {$field} must be a valid email.";
                }
                break;

            case 'min':
                if ($value && strlen($value) < (int)$param) {
                    $this->errors[$field][] = "The {$field} must be at least {$param} characters.";
                }
                break;

            case 'max':
                if ($value && strlen($value) > (int)$param) {
                    $this->errors[$field][] = "The {$field} may not be greater than {$param} characters.";
                }
                break;

            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->errors[$field][] = "The {$field} must be numeric.";
                }
                break;

            // You can easily add more rules like 'confirmed', 'alpha', etc.
        }
    }
}
