<?php

namespace Amirhshokri\LaravelFilterable\Main\Filter\Custom;

use Amirhshokri\LaravelFilterable\Main\Filter\BaseFilter;
use Amirhshokri\LaravelFilterable\Main\Filter\Contract\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class CustomFilter extends BaseFilter implements FilterInterface
{
    /**
     * @param Builder $builder
     * @param array $requestParameters
     * @param array|null $allowedFilterParameters
     * @return void
     */
    public function apply(Builder $builder, array $requestParameters, array $allowedFilterParameters = null): void
    {
        $this->eloquentBuilder = $builder;

        foreach ($requestParameters["filters"] as $filterItem) {

            if ($this->isParameterNameAllowed($filterItem, $allowedFilterParameters) === false) {
                continue;
            }

            $name = $filterItem["name"];
            $operator = $filterItem["operator"];
            $value = $filterItem["value"];

            if (method_exists($this, $name) === false) {
                $this->prepareWhere($name, $operator, $value);
                continue;
            }

            $this->{$name}($value, $operator);
        }
    }
}
