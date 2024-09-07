<?php

namespace Amirhshokri\LaravelFilterable\Console\Commands;

use Amirhshokri\LaravelFilterable\Main\Config\FilterConfig;
use Amirhshokri\LaravelFilterable\Main\Filter\Custom\CustomFilter;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Exception;
use ReflectionClass;

class CreateCustomFilter extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $signature = 'make:filter {name} {--path=default : Specifies where to create the file}';

    /**
     * @var string
     */
    protected $description = 'Creates a custom filter class for Filterable trait.';

    /**
     * @var string
     *
     */
    protected $type = 'Custom filter class';

    /**
     * @return string
     * @throws Exception
     */
    protected function getNameInput(): string
    {
        $this->validateFileName();

        return ucfirst(trim($this->argument('name')));
    }

    /**
     * @param string $stub
     * @param $name
     * @return CreateCustomFilter
     * @throws Exception
     */
    protected function replaceNamespace(&$stub, $name): CreateCustomFilter
    {
        $reflection = new ReflectionClass(CustomFilter::class);

        $replace = [
            '{{ namespace }}' => $this->getNamespaceByPathOption(),
            '{{ className }}' => $this->getNameInput(),
            '{{ mustExtendClassNamespace }}' => $reflection->getName(),
            '{{ mustExtendClass }}' => $reflection->getShortName(),
        ];

        $stub = str_replace(array_keys($replace), array_values($replace), $stub);

        return $this;
    }

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/CustomFilter.stub';
    }

    /**
     * Where to create file
     * @param string $rootNamespace
     * @return string
     * @throws Exception
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $this->getNamespaceByPathOption();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function validateFileName(): void
    {
        if (strlen(FilterConfig::getSuffix()) > 0) {
            if (Str::endsWith($this->argument('name'), FilterConfig::getSuffix()) === false) {
                $this->error('Custom filter file name must end with "' . FilterConfig::getSuffix() . '".');
                exit;
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getNamespaceByPathOption(): string
    {
        if ($this->option('path') !== 'default') {
            if (strlen($this->option('path')) === 0 || str_contains($this->option('path'), "\\") === false) {
                $this->error('"path": Invalid namespace was set.');
                exit;
            }

            return $this->option('path');
        } else {
            if (strlen(FilterConfig::getNamespace()) === 0) {
                $this->error('"' . FilterConfig::getConfigFilePath() . '": Invalid namespace was set.');
                exit;
            }

            return FilterConfig::getNamespace();
        }
    }
}
