<?php
// app/helpers/Validator.php - Validación del lado del servidor

class Validator {
    private array $data;
    private array $errors = [];
    private array $clean = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function required(string $field, string $label): self {
        $value = $this->data[$field] ?? '';
        if (is_string($value) && trim($value) === '') {
            $this->addError($field, "El campo {$label} es obligatorio.");
        } elseif ($value === null) {
            $this->addError($field, "El campo {$label} es obligatorio.");
        } else {
            $this->clean[$field] = is_string($value) ? trim($value) : $value;
        }
        return $this;
    }

    public function email(string $field, string $label): self {
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "El {$label} no tiene un formato válido.");
        } else {
            $this->clean[$field] = strtolower($value);
        }
        return $this;
    }

    public function min(string $field, int $length, string $label): self {
        $value = (string)($this->data[$field] ?? '');
        if (mb_strlen($value) < $length) {
            $this->addError($field, "El campo {$label} debe tener al menos {$length} caracteres.");
        }
        return $this;
    }

    public function max(string $field, int $length, string $label): self {
        $value = (string)($this->data[$field] ?? '');
        if (mb_strlen($value) > $length) {
            $this->addError($field, "El campo {$label} no debe superar {$length} caracteres.");
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $label): self {
        $value = $this->data[$field] ?? '';
        if (!in_array($value, $allowed, true)) {
            $this->addError($field, "El valor seleccionado en {$label} no es válido.");
        }
        return $this;
    }

    public function numeric(string $field, string $label): self {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->addError($field, "El campo {$label} debe ser numérico.");
        }
        return $this;
    }

    public function same(string $field, string $other, string $label, string $otherLabel): self {
        if (($this->data[$field] ?? null) !== ($this->data[$other] ?? null)) {
            $this->addError($field, "El campo {$label} no coincide con {$otherLabel}.");
        }
        return $this;
    }

    public function regex(string $field, string $pattern, string $label, string $message): self {
        $value = (string)($this->data[$field] ?? '');
        if ($value !== '' && !preg_match($pattern, $value)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): ?string {
        return $this->errors ? reset($this->errors) : null;
    }

    public function clean(): array {
        return $this->clean;
    }

    public static function keepOld(array $data, array $except = []): void {
        unset($data['_csrf'], $data['password'], $data['password_confirm']);
        foreach ($except as $key) {
            unset($data[$key]);
        }
        $_SESSION['_old'] = $data;
    }

    public static function clearOld(): void {
        unset($_SESSION['_old']);
    }
}
