<?php

namespace Amirhshokri\LaravelFilterable\Main\Filter\Contract;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    /**
     * @param Builder $builder
     * @param array $requestParameters
     * @param array|null $allowedFilterParameters
     * @return void
     */
    public function apply(Builder $builder, array $requestParameters, array $allowedFilterParameters = null): void;
}
