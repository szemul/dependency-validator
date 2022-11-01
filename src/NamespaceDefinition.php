<?php

declare(strict_types=1);

namespace Szemul\DependencyValidator;

class NamespaceDefinition
{
    public function __construct(
        private string $name,
        private string $path
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

}
