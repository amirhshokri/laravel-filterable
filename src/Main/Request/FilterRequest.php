<?php

namespace Amirhshokri\LaravelFilterable\Main\Request;

use Amirhshokri\LaravelFilterable\Main\Request\Rules\ValueRule;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Validator;
use Exception;

class FilterRequest
{
    /**
     * @var array|null
     */
    private static ?array $preparedRequestParameters = null;

    /**
     * @param array $requestParameters
     * @return void
     */
    public static function setRequestParameters(array $requestParameters): void
    {
        $preparedRequestParameters = [
            "filters" => []
        ];

        foreach ($requestParameters as $parameter) {
            $preparedRequestParameters["filters"][] = [
                "name" => $parameter[0],
                "operator" => $parameter[1],
                "value" => $parameter[2]
            ];
        }

        self::$preparedRequestParameters = $preparedRequestParameters;
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getRequestParameters(): array
    {
        $requestParameters =  self::$preparedRequestParameters ?? self::getRequestInstance()->all();

        self::validate($requestParameters);

        return $requestParameters;
    }

    /**
     * @param array $requestParameters
     * @return void
     * @throws Exception
     */
    public static function validate(array $requestParameters): void
    {
        $rules = [
            'filters' => 'array',
            'filters.*' => 'required|array',
            'filters.*.name' => 'required|string',
            'filters.*.operator' => 'required|string|in:isEqualTo,isNotEqualTo,greaterThan,lessThan,greaterThanOrEqualTo,lessThanOrEqualTo,between,in,contains',
            'filters.*.value' => [new ValueRule()]
        ];

        $validator = Validator::make($requestParameters, $rules);

        if ($validator->fails() === true) {
            throw new Exception($validator->messages()->first());
        }
    }

    /**
     * @return mixed
     * @throws BindingResolutionException
     */
    private static function getRequestInstance(): mixed
    {
        return Container::getInstance()
            ->make('request');
    }
}
