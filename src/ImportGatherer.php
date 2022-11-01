<?php

declare(strict_types=1);

namespace Szemul\DependencyValidator;

use PhpToken;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImportGatherer
{
    public function getImportedClasses(string $rootPath, string $rootNamespace): array
    {
        $importedClasses = [];

        foreach ($this->getPhpFiles($rootPath) as $phpFile) {
            $importedClasses = array_merge($importedClasses, $this->getImportedClassesFromFile($phpFile));
        }

        $importedClasses = array_unique($importedClasses);

        foreach ($importedClasses as $index => $importedClass) {
            if (
                str_starts_with($importedClass, $rootNamespace)
                || !str_contains($importedClass, '\\')
            ) {
                unset($importedClasses[$index]);
            }
        }

        return $importedClasses;
    }

    private function getPhpFiles(string $rootPath): array
    {
        $pathIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath));

        $files = [];

        foreach ($pathIterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            if (pathinfo($file->getPathname(), PATHINFO_EXTENSION) === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function getImportedClassesFromFile(string $filePath): array
    {
        $classes = [];
        $tokens  = PhpToken::tokenize(file_get_contents($filePath));

        foreach ($tokens as $index => $token) {
            if ($token->id === T_USE) {
                $classes[] = $tokens[$index + 2]->text;
            }
        }

        return $classes;
    }
}
