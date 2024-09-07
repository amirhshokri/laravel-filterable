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
     * @param array|null $acceptableFilterParameters
     * @return void
     */
    public function apply(Builder $builder, array $requestParameters, array $acceptableFilterParameters = null): void
    {
        $this->eloquentBuilder = $builder;

        foreach ($requestParameters["filters"] as $filterItem) {

            if ($this->isParameterNameAcceptable($filterItem, $acceptableFilterParameters) === false) {
                continue;
            }

            $name = $filterItem["name"];
            $operand = $filterItem["operand"];
            $value = $filterItem["value"];

            if (method_exists($this, $name) === false) {
                $this->prepareWhere($name, $operand, $value);
                continue;
            }

            $this->{$name}($value, $this->operandMapper($operand));
        }
    }
}
