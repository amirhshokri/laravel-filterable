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
     * @param string $operator
     * @param $value
     * @return void
     */
    protected function prepareWhere(string $name, string $operator, $value): void
    {
        $this->prepareWhereBetween($this->eloquentBuilder, $name, $operator, $value);

        $this->prepareWhereIn($this->eloquentBuilder, $name, $operator, $value);

        $this->prepareWhereContains($this->eloquentBuilder, $name, $operator, $value);

        $this->prepareNormalWhere($this->eloquentBuilder, $name, $operator, $value);
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
     * @param string $operator
     * @return string|null
     */
    protected function operatorMapper(string $operator): ?string
    {
        $operatorsMap = [
            'isEqualTo' => '=',
            'isNotEqualTo' => '!=',
            'greaterThan' => '>',
            'lessThan' => '<',
            'greaterThanOrEqualTo' => '>=',
            'lessThanOrEqualTo' => '<=',
        ];

        return $operatorsMap[$operator] ?? null;
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operator
     * @param $value
     * @return void
     */
    private function prepareWhereBetween(Builder $eloquentBuilder, string $name, string $operator, $value): void
    {
        if ($operator !== 'between') {
            return;
        }

        $eloquentBuilder->whereBetween($name, $value);
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operator
     * @param $value
     * @return void
     */
    private function prepareWhereIn(Builder $eloquentBuilder, string $name, string $operator, $value): void
    {
        if ($operator !== 'in') {
            return;
        }

        $eloquentBuilder->whereIn($name, $value);
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operator
     * @param $value
     * @return void
     */
    private function prepareWhereContains(Builder $eloquentBuilder, string $name, string $operator, $value): void
    {
        if ($operator !== 'contains') {
            return;
        }

        $eloquentBuilder->where($name, 'like', '%' . $value . '%');
    }

    /**
     * @param Builder $eloquentBuilder
     * @param string $name
     * @param string $operator
     * @param $value
     * @return void
     */
    private function prepareNormalWhere(Builder $eloquentBuilder, string $name, string $operator, $value): void
    {
        $mappedOperator = $this->operatorMapper($operator);

        if ($mappedOperator === null) {
            return;
        }

        $eloquentBuilder->where($name, $mappedOperator, $value);
    }
}
