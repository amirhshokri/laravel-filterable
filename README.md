# Laravel filterable

## Installation

You can install the package via composer:

```bash
composer require amirhshokri/laravel-filterable:dev-master
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

The usage of this parameters will be discussed in continue.

## Usage

### Step 1: Add the Filterable trait to your model

```php
use Amirhshokri\LaravelFilterable\Main\Filterable;

class User extends Authenticatable
{
    use Filterable;
}
```

You can also specify which fields are allowed to be filtered by using the `$allowedFilterParameters` array:

```php
use Amirhshokri\LaravelFilterable\Main\Filterable;

class User extends Authenticatable
{
    use Filterable;
    
    protected array $allowedFilterParameters = ["id", "email"];
}
```

If `$allowedFilterParameters` is not defined, all parameters will be allowed for filtering.

### Step 2: Add the `filter()` method to your query builder

```php
$users = \App\Models\User::query()
   ->filter()
   ->get();
```

### Step 3: Make a request

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

#### Supported operators:

The available operators are:

* isEqualTo
* isNotEqualTo
* greaterThan
* lessThan
* greaterThanOrEqualTo
* lessThanOrEqualTo
* between
* in
* contains

## How to use `filter()` method?

There are several approaches for using the `filter()` method:

### Method 1: Passing a custom filter class

You can pass a custom filter class to the `filter()` method to specify the filtering logic for your model:

```php
$users = \App\Models\User::query()
   ->filter(new UserFilter())
   ->get();
```

#### How to create a custom filter class?

You can either create a custom filter class manually or generate one using the following command:

```bash
php artisan make:filter <FilterName> --path=Path\To\Filter\Class
```

* This command uses the `suffix` parameter from the config file to create the filter class. The file name must end with the specified suffix (e.g., MyNewUserFilter).

* If the `--path` option is not provided, the `namespace` parameter from the config file will be used as the default path.

Example of a generated custom filter class:

```php
namespace App\Custom;

use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;

class UserFilter extends CustomFilter
{
    //TODO: Add your custom filter logic here.
}
```

#### Extending your custom filter logic

Once you have created a custom filter class, you can extend the filtering logic for each field you wish to filter. Add a function in the custom filter class that corresponds to the `name` field in your request body:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;

class UserFilter extends CustomFilter
{
    public function id($value, string $operator): void
    {
        //Custom filter logic for 'id' column
    }
    
    public function email($value, string $operator): void
    {
        //Custom filter logic for 'email' column
    }
}
```

* Each function can accept `$value` and `$operator` arguments (both, one, or none). `$value` corresponds to the value in the request, and `$operator` corresponds to the operator.

* Use the `operatorMapper()` method to map operators to database equivalents, such as:

|     **Operator**     | **Mapped version** |
|:--------------------:|--------------------|
|      isEqualTo       | `=`                |
|     isNotEqualTo     | `!=`               |
|     greaterThan      | `>`                |
|       lessThan       | `<`                |
| greaterThanOrEqualTo | `>=`               |
|  lessThanOrEqualTo   | `<=`               |

* The `$eloquentBuilder` property is provided in custom filter classes, allowing for more readable queries. It is essentially a copy of Laravel's default query builder.

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;

class UserFilter extends CustomFilter
{
    public function id($value): void
    {
        $this->eloquentBuilder->where('id', $value);
    }
    
    public function mobile($value, string $operator): void
    {
        $this->eloquentBuilder->where('mobile', $this->operatorMapper($operator), $value);
    }
}
```

#### Nested filters

For more complex filtering scenarios, you can call `filter()` within another `filter()` to apply multiple conditions. For example, to filter users based on post titles:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;
use Amirhshokri\LaravelFilterable\Main\Filter\Enum\OperatorEnum;

class UserFilter extends CustomFilter
{
    public function title($value, string $operator): void
    {
        $this->eloquentBuilder->whereHas('posts', function ($query) use ($value, $operator) {
            $query->setFilterParameters([
                    ["title", $operator, $value]
                ])->filter();
        });
    }
}
```

Finally, make a request:

```json
{
  "filters": [
    {
      "name": "title",
      "operator": "contains",
      "value": "pizza"
    }
  ]
}
```

* You can add multiple conditions using `OperatorEnum` like this:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;
use Amirhshokri\LaravelFilterable\Main\Filter\Enum\OperatorEnum;

class UserFilter extends CustomFilter
{
    public function title($value, string $operator): void
    {
        $this->eloquentBuilder->whereHas('posts', function ($query) use ($value, $operator) {
            $query->setFilterParameters([
                    ["title", $operator, $value],
                    ["slug", OperatorEnum::CONTAINS, 'another pizza'],
                    ["id", OperatorEnum::IS_NOT_EQUAL_TO, 10]
                ])->filter();
        });
    }
}
```

### Method 2: Using auto-discovery

The package includes an auto-discovery feature that automatically detects custom filter classes based on the `namespace` and `suffix` parameters defined in the config file.

When `auto-discovery` is enabled, it will search for a filter class named `{model}{suffix}.php` (e.g., UserFilter.php in App\Filterable\Custom). If the custom filter class is not found in the expected location, an exception will be thrown.

#### Notes

* Enable or disable auto-discovery locally: imagine a scenario where auto-discovery is enabled globally, but you don't want to use it for certain `filter()` call, or vice versa. In such cases, you can simply call the `setFilterDiscovery()` method, passing a boolean value, before invoking the `filter()` method:

```php
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;
use Amirhshokri\LaravelFilterable\Main\Filter\Enum\OperatorEnum;

class UserFilter extends CustomFilter
{
    public function title($value, string $operator): void
    {
        $this->eloquentBuilder->whereHas('posts', function ($query) use ($value, $operator) {
            $query->setFilterParameters([
                    ["title", $operator, $value]
                ])
                ->setFilterDiscovery(false)
                ->filter();
        });
    }
}
```

* If you prefer to create a custom filter class using the `make:filter` command, and that class is discoverable in auto-discovery mode, you can skip the `--path` option to automatically generate the file in the specified `namespace`.

### Method 3: Using default filter

If you don’t provide a custom filter class for the `filter()` method and auto-discovery is turned off, the package will use the default filter functionality for your model, as explained in the "Usage" section above.

## Credits

- [Amir Hossein Shokri](https://github.com/amirhshokri)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
