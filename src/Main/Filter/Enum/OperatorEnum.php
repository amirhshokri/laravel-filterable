<?php

namespace Amirhshokri\LaravelFilterable\Main\Filter\Enum;

class OperatorEnum
{
    public const IS_EQUAL_TO = 'isEqualTo';
    public const IS_NOT_EQUAL_TO = 'isNotEqualTo';
    public const GREATER_THAN = 'greaterThan';
    public const LESS_THAN = 'lessThan';
    public const GREATER_THAN_OR_EQUAL_TO = 'greaterThanOrEqualTo';
    public const LESS_THAN_OR_EQUAL_TO = 'lessThanOrEqualTo';
    public const BETWEEN = 'between';
    public const IN = 'in';
    public const CONTAINS = 'contains';
}
