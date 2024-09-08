<?php

namespace Amirhshokri\LaravelFilterable\Main\Filter\Defaults;

use Amirhshokri\LaravelFilterable\Main\Filter\BaseFilter;
use Amirhshokri\LaravelFilterable\Main\Filter\Contract\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class DefaultFilter extends BaseFilter implements FilterInterface
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
            $operator = $filterItem["operator"];
            $value = $filterItem["value"];

            $this->prepareWhere($name, $operator, $value);
        }
    }
}
