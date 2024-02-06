<?php

namespace SH\AutoHook\Tests;

use PHPUnit\Framework\TestCase;
use SH\AutoHook\NamespaceClassMapper;

class NamespaceClassesMapperTest extends TestCase
{
    public function testFromComposerJson(): void
    {
        $file = __DIR__ . '/../composer.json';

        $namespaceClassesMap = NamespaceClassMapper::fromComposerJson($file);

        self::assertContains(NamespaceClassMapper::class, $namespaceClassesMap['SH\\AutoHook\\']);
    }

    public function testFilterClasses(): void
    {
        $namespaces = ['SH\\AutoHook\\'];

        $classes = require __DIR__ . '/../vendor/composer/autoload_classmap.php';

        $namespaceClassesMap = NamespaceClassMapper::filterClasses($namespaces, array_keys($classes));

        self::assertContains(NamespaceClassMapper::class, $namespaceClassesMap['SH\\AutoHook\\']);
    }
}
