# Laravel filterable

## Introduction

Laravel Filterable helps you add efficient filtering logic to the Laravel query builder. You can use the default filter logic, define custom filter logic manually, or create it using the provided command with any suffix in any path you choose. By enabling auto-discovery mode, the package will automatically locate the desired filter class for you.

## Installation

You can install the package via composer:

```bash
composer require amirhshokri/laravel-filterable
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-filterable-config"
```

This is the contents of the published config file:

```php
return [
    'auto_discovery' => false,
    'namespace' => 'App\\Filterable\\Custom',
    'suffix' => 'Filter',
];
```

## Basic usage

1 - Add the Filterable trait to your model.

```php
use Amirhshokri\LaravelFilterable\Main\Filterable;

class User extends Authenticatable
{
    use Filterable;
}
```

2 - Add the `filter()` method to your query builder.

```php
$users = \App\Models\User::query()
   ->filter()
   ->get();
```

3 - All set! Make a request:

```json
{
  "filters": [
    {
      "name": "id",
      "operator": "isEqualTo",
      "value": 1
    },
    {
      "name": "email",
      "operator": "contains",
      "value": "@gmail"
    }
  ]
}
```

## How to use `filter()` method?

### Method 1: Using default filter

If you don’t provide a custom filter class for the `filter()` method and `auto-discovery` is turned off in the config file, the package will use the default filter functionality for your model, as explained previously in the [Basic usage](#basic-usage) section.

### Method 2: Passing a custom filter class

You can pass a custom filter class to the `filter()` method to **enforce** specific filtering logic for your model:

```php
$users = \App\Models\User::query()
   ->filter(new UserFilter())
   ->get();
```

- Create a custom filter class manually, or generate one using the following command:

```bash
php artisan make:filter <FilterName> --path=Path\To\Filter\Class
```

- This command uses the `suffix` parameter from the config file to create the filter class. The file name must end with the specified suffix.
- If the `--path` option is not provided, the `namespace` parameter from the config file will be used as the default path.

Once you have created a custom filter class, you can extend the filtering logic for each field you wish to filter. Add a function in the custom filter class that corresponds to the `name` field in your request body:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;

class UserFilter extends CustomFilter
{
    public function id($value, string $operator): void
    {
        //Custom filter logic here
    }
    
    public function email($value, string $operator): void
    {
        //Custom filter logic here
    }
}
```

- Each function can accept `$value` and `$operator` arguments. `$value` represents the value in the request, and `$operator` represents the operator.

### Method 3: Using auto-discovery

When `auto-discovery` is enabled, this package will search for a filter class named `{ModelName}{Suffix}.php` using the `namespace` and `suffix` parameters defined in the config file. If the custom filter class is not found in the expected location, an exception will be thrown.

- If you don't want to use auto-discovery for a specific `filter()` call, you can use `setFilterAutoDiscovery(false)` before calling `filter()`:

```php
$users = \App\Models\User::query()
   ->setFilterAutoDiscovery(false)
   ->filter()
   ->get();
```

### Method 4: Nested filters

In more complex scenarios, you can nest another `filter()` within your current filter logic to apply multiple conditions, such as filtering users based on their post titles. Make sure the Filterable trait is added to all relevant models:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;

class UserFilter extends CustomFilter
{
    public function title($value, string $operator): void
    {
        $this->eloquentBuilder->whereHas('posts', function ($query) use ($value, $operator) {
            $query->setFilterParameters([
                    ['title', $operator, $value]
                ])->filter();
        });
    }
}
```

## Additional Notes

1 - You can optionally specify which fields are allowed for filtering using the `$allowedFilterParameters` array. Leave it empty or omit it entirely to allow filtering on all fields:

```php
use Amirhshokri\LaravelFilterable\Main\Filterable;

class User extends Authenticatable
{
    use Filterable;
    
    protected array $allowedFilterParameters = ['id', 'email'];
}
```

2 - Use the `operatorMapper()` method to map operators to their database equivalents:

|     **Supported Operator**     |  **Mapped Version** |
|:-------------------------------|---------------------|
| isEqualTo                      | `=`                 |
| isNotEqualTo                   | `!=`                |
| greaterThan                    | `>`                 |
| lessThan                       | `<`                 |
| greaterThanOrEqualTo           | `>=`                |
| lessThanOrEqualTo              | `<=`                |
| between                        |                     |
| in                             |                     |
| contains                       |                     |

For example:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;

class UserFilter extends CustomFilter
{
    public function mobile($value, string $operator): void
    {
        $this->eloquentBuilder->where('mobile', $this->operatorMapper($operator), $value);
    }
}
```

3 - You can also use `OperatorEnum`:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;
use Amirhshokri\LaravelFilterable\Main\Filter\Enum\OperatorEnum;

class UserFilter extends CustomFilter
{
    public function title($value, string $operator): void
    {
        $this->eloquentBuilder->whereHas('posts', function ($query) use ($value, $operator) {
            $query->setFilterParameters([
                    ['id', OperatorEnum::IS_NOT_EQUAL_TO, 10],
                    ['title', $operator, $value],
                    ['slug', OperatorEnum::CONTAINS, 'another pizza']
                ])->filter();
        });
    }
}
```

## Credits

- [Amir Hossein Shokri](https://github.com/amirhshokri)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
