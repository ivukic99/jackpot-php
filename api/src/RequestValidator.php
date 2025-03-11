<?php
class RequestValidator {
    private array $errors = [];
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function validate(array $request, array $rules) {
        foreach ($rules as $rule_key => $rules_array) {
            foreach ($rules_array as $rule) {
                if ($rule === 'required' && !isset($request[$rule_key])) {
                    $this->addError($rule_key, "The $rule_key field is required.");
                }
                if ($rule === 'number' && isset($request[$rule_key]) && is_string($request[$rule_key])) {
                    $this->addError($rule_key, "The $rule_key must be a number.");
                }
                if ($rule === 'positive' && isset($request[$rule_key]) && $request[$rule_key] < 0) {
                    $this->addError($rule_key, "The $rule_key must be greater than 0.");
                }
                if ($rule === 'int' && isset($request[$rule_key]) && !is_int($request[$rule_key])) {
                    $this->addError($rule_key, "The $rule_key must be integer.");
                }
                if (str_starts_with($rule, 'exists:') && isset($request[$rule_key])) {
                    [$table, $column] = explode(',', str_ireplace('exists:', '', $rule));

                    if (!$this->recordExists($table, $column, $request[$rule_key])) {
                        $this->addError($rule_key, "The $rule_key doesn't exist.");
                    }
                }
            }
        }
        return empty($this->errors);
    }

    private function addError(string $key, string $message) {
        $this->errors[$key] = $message;
    }

    public function getErrors() {
        return $this->errors;
    }

    private function recordExists(string $table, string $column, mixed $value) {
        $query = "SELECT COUNT(*) FROM $table WHERE `$column` = :value";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['value' => $value]);

        return $stmt->fetchColumn() > 0;
    }
}