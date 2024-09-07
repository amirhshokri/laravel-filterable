<?php

namespace Amirhshokri\LaravelFilterable\Main\Config;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Exception;

class FilterConfig
{
    /**
     * @var string
     */
    private static string $configFileName = 'filterable';

    /**
     * @var string
     */
    private static string $autoDiscoveryParameter = 'auto_discovery';

    /**
     * @var string
     */
    private static string $namespaceParameter = 'namespace';

    /**
     * @var string
     */
    private static string $suffixParameter = 'suffix';

    /**
     * @var bool|null
     */
    private static ?bool $switchAutoDiscovery = null;

    /**
     * @return bool
     * @throws Exception
     */
    public static function hasAutoDiscovery(): bool
    {
        return self::$switchAutoDiscovery ?? self::get(self::$autoDiscoveryParameter);
    }

    /**
     * @param bool $switchAutoDiscovery
     * @return void
     */
    public static function setAutoDiscovery(bool $switchAutoDiscovery): void
    {
        self::$switchAutoDiscovery = $switchAutoDiscovery;
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getNamespace(): string
    {
        return self::get(self::$namespaceParameter);
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function getSuffix(): string
    {
        return ucfirst(self::get(self::$suffixParameter));
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    private static function get(string $key): mixed
    {
        self::configFileExists();

        $config = self::getConfigInstance(self::$configFileName);

        self::validate($config);

        return $config[$key];
    }

    /**
     * @return string
     */
    private static function getConfigFileFullName(): string
    {
        return self::$configFileName . '.php';
    }

    /**
     * @return string
     */
    public static function getConfigFilePath(): string
    {
        return Str::afterLast(self::getConfigPathInstance(), "\\") . "\\" . self::getConfigFileFullName();
    }

    /**
     * @return void
     * @throws Exception
     */
    private static function configFileExists(): void
    {
        if (file_exists(self::getConfigPathInstance(self::getConfigFileFullName())) === false) {
            throw new Exception('"' . self::getConfigFilePath() . '": File not found.');
        }
    }

    /**
     * @param array $config
     * @return void
     * @throws Exception
     */
    private static function validate(array $config): void
    {
        $configFileParameters = [
            self::$autoDiscoveryParameter => 'boolean',
            self::$namespaceParameter => 'string',
            self::$suffixParameter => 'string'
        ];

        foreach ($configFileParameters as $parameter => $type) {
            if (isset($config[$parameter]) === false) {
                throw new Exception('"' . self::getConfigFilePath() . '": Required parameter "' . $parameter . '" not found.');
            }

            if (gettype($config[$parameter]) !== $type) {
                throw new Exception('"' . self::getConfigFilePath() . '": Parameter "' . $parameter . '" must have a ' . $type . ' value.');
            }
        }
    }

    /**
     * @param string $configFileName
     * @return mixed
     * @throws BindingResolutionException
     */
    private static function getConfigInstance(string $configFileName): mixed
    {
        return Container::getInstance()
            ->make('config')
            ->get($configFileName, null);
    }

    /**
     * @param string|null $configFileName
     * @return string
     */
    private static function getConfigPathInstance(string $configFileName = null): string
    {
        /** @var Application $app */
        $app = Container::getInstance();
        return $app->configPath($configFileName);
    }
}
