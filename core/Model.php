<?php

namespace app\core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_MAIL = 'mail';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public const RULE_DATE = 'date';
    public const RULE_EXP_DATE = 'exp_date';
    public const RULE_HOUR = 'hour';

    public array $errors = [];

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules(): array;

    public function labels(): array
    {
        return [];
    }

    public function placeholders(): array
    {
        return [];
    }

    public function getLabel($attr)
    {
        return $this->labels()[$attr];
    }

    public function getPlaceholder($attr)
    {
        return $this->placeholders()[$attr];
    }

    public function validate()
    {
        foreach ($this->rules() as $attr => $rules) {
            $value = $this->{$attr};
            foreach ($rules as $rule) {
                $rule_name = $rule;
                if (is_array($rule_name)) {
                    $rule_name = $rule[0];
                    $rule_value = $rule[$rule_name];
                }

                // implement rules
                if ($rule_name === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attr, self::RULE_REQUIRED);
                }
                if ($rule_name === self::RULE_MAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attr, self::RULE_MAIL);
                }
                if ($rule_name === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attr, self::RULE_MIN, $rule);
                }
                if ($rule_name === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attr, self::RULE_MAX, $rule);
                }
                if ($rule_name === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addErrorForRule($attr, self::RULE_MATCH, $rule);
                }
                if ($rule_name === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttribute = $rule['attribute'] ?? $attr;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttribute = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attr, self::RULE_UNIQUE, ['field' => ucfirst($attr)]);
                    }
                }

                if ($rule_name === self::RULE_DATE) {
                    $pattern = '/^\d{4}-\d{2}-\d{2}$/';
                    if (!preg_match($pattern, $value)) {
                        $this->addError($attr, self::RULE_MATCH, $rule);
                    } else {
                        [$year, $month, $day] = explode('-', $value);
                        if (!checkdate($month, $day, $year)) {
                            $this->addError($attr, self::RULE_MATCH);
                        }
                    }
                }
                if ($rule_name === self::RULE_EXP_DATE) {
                    $pattern = '/^\d{2}/\d{2}$/';
                    if (!preg_match($pattern, $value)) {
                        $this->addError($attr, self::RULE_MATCH, $rule);
                    }
                }
                if ($rule_name === self::RULE_HOUR) {
                    $pattern = '/^\d{1,2}h\d{2}$/';
                    if (!preg_match($pattern, $value)) {
                        $this->addError($attr, self::RULE_MATCH, $rule);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    private function addErrorForRule(string $attr, string $rule, $params = [])
    {
        $message = $this->errorMessage()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attr][] = $message;
    }

    public function addError(string $attr, string $message)
    {
        $this->errors[$attr][] = $message;
    }

    public function errorMessage(): array
    {
        return [
            self::RULE_REQUIRED => 'Ce champs est obligatoire',
            self::RULE_MAIL => 'Ce champs doit être une email valide',
            self::RULE_MIN => 'La taille minimum de ce champs est de {min} caractères',
            self::RULE_MAX => 'La taille maximum de ce champs est de {max} caractères',
            self::RULE_MATCH => 'Ce champs doit être le même que {match}',
            self::RULE_UNIQUE => '{field} existe déjà',
            self::RULE_DATE => 'Format de date incorrecte',
            self::RULE_EXP_DATE => 'Format de date d\'expiration incorrecte',
            self::RULE_HOUR => 'Format d\'heure incorrecte'
        ];
    }

    public function hasError($attr)
    {
        return $this->errors[$attr] ?? false;
    }

    public function getFirstError($attr)
    {
        return $this->errors[$attr][0] ?? '';
    }
}