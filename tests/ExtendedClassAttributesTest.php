<?php

namespace SH\AutoHook\Tests;

use PHPUnit\Framework\TestCase;
use SH\AutoHook\AttributeResolver;
use SH\AutoHook\Tests\Examples\ExamplePage;
use SH\AutoHook\Tests\Examples\AbstractAdminPage;

class ExtendedClassAttributesTest extends TestCase
{
    public function testInheritedClassHookAppliedToConcreteChild(): void
    {
        [$output, $errors] = AttributeResolver::processClassesToString([ExamplePage::class]);

        self::assertEmpty($errors);

        self::assertStringContainsString("add_action('admin_menu', 'SH\\AutoHook\\Tests\\Examples\\ExamplePage::register');", $output);

        // Ensure abstract parent does not produce a hook entry when processed alone
        [$outputParent, $errorsParent] = AttributeResolver::processClassesToString([AbstractAdminPage::class]);
        self::assertEmpty($errorsParent);
        self::assertStringNotContainsString('ExamplePage::register', $outputParent);
    }
}

