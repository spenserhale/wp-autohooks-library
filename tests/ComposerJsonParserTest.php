<?php

namespace SH\AutoHook\Tests;

use PHPUnit\Framework\TestCase;
use SH\AutoHook\ComposerJsonParser;

class ComposerJsonParserTest extends TestCase
{
    private const COMPOSER_JSON_PATH = __DIR__.'/../composer.json';

    public function testGetClasses(): void
    {
        $classes = ComposerJsonParser::getClasses(self::COMPOSER_JSON_PATH);

        self::assertContains(ComposerJsonParser::class, $classes);
    }

    public function testGetNamespacesAndClasses(): void
    {
        ['namespaces' => $namespaces, 'classes' => $classes] = ComposerJsonParser::getNamespacesAndClasses(self::COMPOSER_JSON_PATH);

        self::assertArrayHasKey('SH\\AutoHook\\', $namespaces);
        self::assertArrayHasKey(ComposerJsonParser::class, $classes);
    }

    public function testParseFile(): void
    {
        $composer = ComposerJsonParser::parseFile(self::COMPOSER_JSON_PATH);

        self::assertArrayHasKey('autoload', $composer);
        self::assertArrayHasKey('psr-4', $composer['autoload']);
        self::assertArrayHasKey('config', $composer);
        self::assertArrayHasKey('vendor-dir', $composer['config']);
    }
}