<?php

declare(strict_types=1);

namespace Szemul\DependencyValidator;

use InvalidArgumentException;

class Config
{
    private array $config = [];

    public function __construct(string $rootPath)
    {
        $configPath = rtrim($rootPath, '/') . '/dependency-validator.json';

        if (!file_exists($configPath)) {
            return;
        }

        $json   = file_get_contents($configPath);
        $config = json_decode($json, true);

        if (!is_array($config)) {
            throw new InvalidArgumentException('Invalid configuration');
        }

        $this->config = $config;
    }

    public function getExcludedNamespaces(): array
    {
        return empty($this->config['excludedNamespaces'])
            ? []
            : $this->config['excludedNamespaces'];
    }

}
