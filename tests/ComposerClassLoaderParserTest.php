<?php

namespace SH\AutoHook\Tests;

use PHPUnit\Framework\TestCase;
use \Composer\Autoload\ClassLoader;
use SH\AutoHook\ComposerClassLoaderParser;

class ComposerClassLoaderParserTest extends TestCase
{
    public function testGetClasses(): void
    {
        $classes = ComposerClassLoaderParser::getClasses(self::getClassLoader());

        self::assertContains(ComposerClassLoaderParser::class, $classes);

        $classes = ComposerClassLoaderParser::getClasses(self::getClassLoader(), ['SH\\AutoHook\\']);

        self::assertContains(ComposerClassLoaderParser::class, $classes);
    }

    public function testGetNamespacesAndClasses(): void
    {
        ['namespaces' => $namespaces, 'classes' => $classes] = ComposerClassLoaderParser::getNamespacesAndClasses(self::getClassLoader());

        self::assertArrayHasKey('SH\\AutoHook\\', $namespaces);
        self::assertArrayHasKey(ComposerClassLoaderParser::class, $classes);
    }

    private static function getClassLoader(): ClassLoader
    {
        static $classLoader;

        if (null === $classLoader) {
            $classLoader = require __DIR__.'/../vendor/autoload.php';
        }

        return $classLoader;
    }
}