<?php

namespace Amirhshokri\LaravelFilterable\Main\Request\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class ValueRule implements DataAwareRule, ValidationRule
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $index = explode(".", $attribute)[1];

        $operand = $this->data["filters"][$index]["operand"];

        switch ($operand) {
            case 'in':
                if (is_array($value) === false) {
                    $fail(":attribute must be an array when operand is \"$operand\".");
                    return;
                }

                if (count($value) === 0) {
                    $fail('":attribute": array can not be empty.');
                    return;
                }

                break;

            case 'between':
                if (is_array($value) === false) {
                    $fail(":attribute must be an array when operand is \"$operand\".");
                    return;
                }

                if (count($value) !== 2) {
                    $fail('":attribute": array must have exactly 2 items.');
                    return;
                }

                break;

            default:
                if (is_array($value) === true) {
                    $fail(":attribute must not be an array when operand is \"$operand\".");
                }

                break;
        }
    }
}
