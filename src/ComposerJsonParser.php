<?php

declare(strict_types=1);

namespace Szemul\DependencyValidator;

class ComposerJsonParser
{
    public function __construct(private string $rootPath)
    {
    }

    public function getRequiredPackages(): array
    {
        return $this->getContent()['require'];
    }

    /**
     * @return NamespaceDefinition[]
     */
    public function getNamespaces(): array
    {
        $namespaces = [];
        if (empty($this->getContent()['autoload'])) {
            return [];
        }

        $autoloadSection = $this->getContent()['autoload'];
        $definitions     = empty($autoloadSection['psr-4'])
            ? $autoloadSection['psr-0']
            : $autoloadSection['psr-4'];

        foreach ($definitions as $name => $path) {
            $path         = is_array($path) ? $path[0] : $path;
            $namespaces[] = new NamespaceDefinition($name, $path);
        }

        return $namespaces;
    }

    private function getContent(): array
    {
        $json = file_get_contents($this->rootPath . 'composer.json');

        return json_decode($json, true);
    }
}
