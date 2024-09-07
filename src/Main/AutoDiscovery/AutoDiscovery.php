<?php

namespace Amirhshokri\LaravelFilterable\Main\AutoDiscovery;

use Amirhshokri\LaravelFilterable\Main\Config\FilterConfig;
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;
use Exception;
use Illuminate\Database\Eloquent\Model;

class AutoDiscovery
{
    /**
     * @param Model $model
     * @return CustomFilter
     * @throws Exception
     */
    public static function discoverFilter(Model $model): CustomFilter
    {
        return new (self::getFilterClass($model));
    }

    /**
     * @param Model $model
     * @return string
     * @throws Exception
     */
    private static function getFilterClass(Model $model): string
    {
        $filterNamespace = FilterConfig::getNamespace();

        $filterFileName = class_basename($model) . FilterConfig::getSuffix();

        $filterNamespace = $filterNamespace . "\\" . $filterFileName;

        $filterExists = class_exists($filterNamespace);

        if ($filterExists === false) {
            throw new Exception("\"$filterNamespace\": Class not found.");
        }

        return $filterNamespace;
    }
}
