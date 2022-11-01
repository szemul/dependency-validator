<?php

declare(strict_types=1);

namespace Szemul\DependencyValidator;

class Validator
{
    private string $rootPath;
    private Config $config;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/') . '/';
        $this->config = new Config($this->rootPath);
    }

    public function getUnRequiredClasses(): array
    {
        $composerParser    = new ComposerJsonParser($this->rootPath);
        $packageNamespaces = $this->getPackageNamespacesFromComposer();
        $projectNamespaces = $composerParser->getNamespaces();
        $importedClasses   = $this->getImportedClasses(...$projectNamespaces);

        foreach ($importedClasses as $index => $importedClass) {
            if (
                $this->isClassDefinedInProject($importedClass, ...$projectNamespaces)
                || $this->isNamespaceExcluded($importedClass)
                || $this->isPackageRequired($importedClass, ...$packageNamespaces)
            ) {
                unset($importedClasses[$index]);
            }
        }

        sort($importedClasses);

        return $importedClasses;
    }

    /**
     * @return NamespaceDefinition[]
     */
    private function getPackageNamespacesFromComposer(): array
    {
        $composerParser    = new ComposerJsonParser($this->rootPath);
        $packageNamespaces = [];

        foreach ($composerParser->getRequiredPackages() as $requiredPackage => $version) {
            if (str_starts_with($requiredPackage, 'ext-') || $requiredPackage === 'php') {
                continue;
            }

            $packageComposerParser = new ComposerJsonParser($this->rootPath . '/vendor/' . $requiredPackage . '/');
            $packageNamespaces     = array_merge($packageNamespaces, $packageComposerParser->getNamespaces());
        }

        return $packageNamespaces;
    }

    private function getImportedClasses(NamespaceDefinition ...$projectNamespaces): array
    {
        $importGatherer  = new ImportGatherer();
        $importedClasses = [];

        foreach ($projectNamespaces as $projectNamespace) {
            $importedClasses = array_merge(
                $importedClasses,
                $importGatherer->getImportedClasses($projectNamespace->getPath(), $projectNamespace->getName())
            );
        }

        return array_unique($importedClasses);
    }

    private function isClassDefinedInProject(string $importedClass, NamespaceDefinition ...$projectNamespaces): bool
    {
        foreach ($projectNamespaces as $projectNamespace) {
            if (str_starts_with($importedClass, $projectNamespace->getName())) {
                return true;
            }
        }

        return false;
    }

    private function isNamespaceExcluded(string $importedClass): bool
    {
        foreach ($this->config->getExcludedNamespaces() as $excludedNamespace) {
            if (str_starts_with($importedClass, $excludedNamespace)) {
                return true;
            }
        }

        return false;
    }

    private function isPackageRequired(string $importedClass, NamespaceDefinition ...$packageNamespaces): bool
    {
        foreach ($packageNamespaces as $packageNamespace) {
            if (str_starts_with($importedClass, $packageNamespace->getName())) {
                return true;
            }
        }

        return false;
    }
}
