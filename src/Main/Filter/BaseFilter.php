<?php

namespace Amirhshokri\LaravelFilterable\Main\Filter;

use Illuminate\Database\Eloquent\Builder;

class BaseFilter
{
    /**
     * @var Builder
     */
    protected Builder $eloquentBuilder;

    /**
     * @param string $name
     * @param string $operand
     * @param $value
     * @return void
     */
    protected function prepareWhere(string $name, string $operand, $value): void
    {
        $this->prepareWhereBetween($this->eloquentBuilder, $name, $operand, $value);

        $this->prepareWhereIn($this->eloquentBuilder, $name, $operand, $value);

        $this->prepareWhereContains($this->eloquentBuilder, $name, $operand, $value);

        $this->prepareNormalWhere($this->eloquentBuilder, $name, $operand, $value);
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operand
     * @param $value
     * @return void
     */
    private function prepareWhereBetween(Builder $eloquentBuilder, string $name, string $operand, $value): void
    {
        if ($operand !== 'between') {
            return;
        }

        $eloquentBuilder->whereBetween($name, $value);
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operand
     * @param $value
     * @return void
     */
    private function prepareWhereIn(Builder $eloquentBuilder, string $name, string $operand, $value): void
    {
        if ($operand !== 'in') {
            return;
        }

        $eloquentBuilder->whereIn($name, $value);
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operand
     * @param $value
     * @return void
     */
    private function prepareWhereContains(Builder $eloquentBuilder, string $name, string $operand, $value): void
    {
        if ($operand !== 'contains') {
            return;
        }

        $eloquentBuilder->where($name, 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operand
     * @param $value
     * @return void
     */
    private function prepareNormalWhere(Builder $eloquentBuilder, string $name, string $operand, $value): void
    {
        $mappedOperand = $this->operandMapper($operand);

        if ($mappedOperand === null) {
            return;
        }

        $eloquentBuilder->where($name, $mappedOperand, $value);
    }

    /**
     * @param array $filterItem
     * @param array|null $acceptableFilterParameters
     * @return bool
     */
    protected function isParameterNameAcceptable(array $filterItem, array $acceptableFilterParameters = null): bool
    {
        if ($acceptableFilterParameters) {
            return in_array($filterItem['name'], $acceptableFilterParameters);
        }

        return true;
    }

    /**
     * @param string $operand
     * @return string|null
     */
    private function operandMapper(string $operand): ?string
    {
        $operandsMap = [
            'isEqualTo' => '=',
            'isNotEqualTo' => '!=',
            'greaterThan' => '>',
            'lessThan' => '<',
            'greaterThanOrEqualTo' => '>=',
            'lessThanOrEqualTo' => '<=',
        ];

        return $operandsMap[$operand] ?? null;
    }
}
