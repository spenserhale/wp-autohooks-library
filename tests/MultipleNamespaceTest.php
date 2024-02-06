<?php

namespace SH\AutoHook\Tests;

use PHPUnit\Framework\TestCase;
use SH\AutoHook\ComposerJsonParser;
use SH\AutoHook\NamespaceClassMapper;
use SH\AutoHook\Tests\Examples\ExamplesClass;

class MultipleNamespaceTest extends TestCase
{
    public function testMultipleNamespaces(): void
    {
        $namespaces = ['SH\\AutoHook\\', 'SH\\AutoHook\\Tests\\', 'SH\\AutoHook\\Tests\\Examples\\'];

        $classes = ComposerJsonParser::getClasses(__DIR__ . '/../composer.json');

        $namespaceClassesMap = NamespaceClassMapper::filterClasses($namespaces, $classes);

        // Assert that the classes are in the correct namespaces
        self::assertContains(ComposerJsonParser::class, $namespaceClassesMap['SH\\AutoHook\\']);
        self::assertContains(ComposerJsonParserTest::class, $namespaceClassesMap['SH\\AutoHook\\Tests\\']);
        self::assertContains(ExamplesClass::class, $namespaceClassesMap['SH\\AutoHook\\Tests\\Examples\\']);

        // Assert that the classes are not in the wrong namespaces (no duplicates)
        self::assertNotContains(ComposerJsonParser::class, $namespaceClassesMap['SH\\AutoHook\\Tests\\']);
        self::assertNotContains(ComposerJsonParserTest::class, $namespaceClassesMap['SH\\AutoHook\\Tests\\Examples\\']);
        self::assertNotContains(ExamplesClass::class, $namespaceClassesMap['SH\\AutoHook\\']);
    }
}