<?php

namespace Amirhshokri\LaravelFilterable\Main;

use Amirhshokri\LaravelFilterable\Main\AutoDiscovery\AutoDiscovery;
use Amirhshokri\LaravelFilterable\Main\Config\FilterConfig;
use Amirhshokri\LaravelFilterable\Main\Filter\Contract\FilterInterface;
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;
use Amirhshokri\LaravelFilterable\Main\Filter\Defaults\DefaultFilter;
use Amirhshokri\LaravelFilterable\Main\Request\FilterRequest;
use Illuminate\Database\Eloquent\Builder;
use Exception;

/**
 * @method filter(CustomFilter $customFilter = null)
 * @method setFilterParameters(array $filterParameters)
 * @method setFilterDiscovery(bool $filterDiscovery)
 */
trait Filterable
{
    /**
     * @param Builder $query
     * @param CustomFilter|null $customFilter
     * @return void
     * @throws Exception
     */
    public function scopeFilter(Builder $query, CustomFilter $customFilter = null): void
    {
        $requestParameters = FilterRequest::getRequestParameters();

        /** @var DefaultFilter|CustomFilter $filter */
        $filter = $customFilter ?? $this->autoDiscoverFilter();

        $filter->apply($query, $requestParameters, $this->getAcceptableFilterParameters());
    }

    /**
     * @param Builder $query
     * @param array $filterParameters
     * @return void
     */
    public function scopeSetFilterParameters(Builder $query, array $filterParameters): void
    {
        FilterRequest::setRequestParameters($filterParameters);
    }

    /**
     * @param Builder $query
     * @param bool $filterDiscovery
     * @return void
     */
    public function scopeSetFilterDiscovery(Builder $query, bool $filterDiscovery): void
    {
        FilterConfig::setAutoDiscovery($filterDiscovery);
    }

    /**
     * @return FilterInterface
     * @throws Exception
     */
    private function autoDiscoverFilter(): FilterInterface
    {
        return (FilterConfig::hasAutoDiscovery()) ? AutoDiscovery::discoverFilter($this) : new DefaultFilter();
    }

    /**
     * @return array|null
     */
    private function getAcceptableFilterParameters(): ?array
    {
        return (isset($this->filterableParameters)) ? $this->filterableParameters : null;
    }
}
