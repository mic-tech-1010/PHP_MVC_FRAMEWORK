<?php

// namespace Core\Support;

// class ErrorBag
// {
//     protected array $errors = [];

//     public function __construct(array $errors = [])
//     {
//         $this->errors = $errors;
//     }

//     public function has(string $field): bool
//     {
//         return isset($this->errors[$field]);
//     }

//     public function first(string $field): ?string
//     {
//         return $this->errors[$field][0] ?? null;
//     }

//     public function get(string $field): array
//     {
//         return $this->errors[$field] ?? [];
//     }

//     public function all(): array
//     {
//         return array_merge(...array_values($this->errors));
//     }

//     public function isEmpty(): bool
//     {
//         return empty($this->errors);
//     }
// }

namespace Core\Support;

class ErrorBag
{
    protected array $errors = [];

    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function get(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    public function all(): array
    {
        $flattened = [];
        foreach ($this->errors as $messages) {
            foreach ($messages as $msg) {
                $flattened[] = $msg;
            }
        }
        return $flattened;
    }

    public function isEmpty(): bool
    {
        return empty($this->errors);
    }

    public function any(): bool
    {
        return !$this->isEmpty();
    }
}

