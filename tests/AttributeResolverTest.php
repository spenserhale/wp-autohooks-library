<?php

namespace SH\AutoHook\Tests;

use PHPUnit\Framework\TestCase;
use SH\AutoHook\AttributeResolver;
use SH\AutoHook\Hook;
use SH\AutoHook\Shortcode;
use SH\AutoHook\Tests\Examples\ExamplesClass;

class AttributeResolverTest extends TestCase
{
    public function testExamplesClassToString(): void
    {
        [$output, $errors] = AttributeResolver::processClassesToString([ExamplesClass::class]);

        self::assertEmpty($errors);

        self::assertStringContainsString("add_filter('facade_example_hook', 'SH\AutoHook\Tests\Examples\ExamplesClass::facadeMethod', 10, 3);", $output);
        self::assertStringContainsString("add_action('cli_init', 'SH\AutoHook\Tests\Examples\ExamplesClass::registerCommand', 9, 0);", $output);
        self::assertStringContainsString("add_action('rest_api_init', 'SH\AutoHook\Tests\Examples\ExamplesClass::registerController', 10, 0);", $output);
        self::assertStringContainsString("add_action('wp_init', 'SH\AutoHook\Tests\Examples\ExamplesClass::boot', 99, 0);", $output);
        self::assertStringContainsString("add_filter('pre_update_option', 'SH\AutoHook\Tests\Examples\ExamplesClass::filterOption', 100);", $output);
        self::assertStringContainsString("add_filter('wp_insert_blog_meta', 'SH\AutoHook\Tests\Examples\ExamplesClass::logInsert', 10, 2);", $output);
        self::assertStringContainsString("add_filter('wp_insert_post_meta', 'SH\AutoHook\Tests\Examples\ExamplesClass::logInsert', 10, 2);", $output);
        self::assertStringContainsString("add_action('update_post_meta', 'SH\AutoHook\Tests\Examples\ExamplesClass::logChange', 10, 2);", $output);
        self::assertStringContainsString("add_action('update_option_meta', 'SH\AutoHook\Tests\Examples\ExamplesClass::logChange', 10, 2);", $output);
        self::assertStringContainsString("add_action('wp_ajax_pbhs_users_add_client', 'SH\AutoHook\Tests\Examples\ExamplesClass::handleUsersAddClientAjax', 10, 0);", $output);
        self::assertStringContainsString("add_shortcode('other_shortcode', 'SH\AutoHook\Tests\Examples\ExamplesClass::otherShortcode');", $output);
        self::assertStringContainsString("add_shortcode('example_shortcode', 'SH\AutoHook\Tests\Examples\ExamplesClass::shortcode');", $output);
    }

    public function testExamplesClassToList(): void
    {
        [$hooks, $errors] = AttributeResolver::processClassesToList([ExamplesClass::class]);

        self::assertEmpty($errors);

        self::assertCount(10, array_filter($hooks, static fn($hook) => $hook instanceof Hook));
        self::assertCount(2, array_filter($hooks, static fn($hook) => $hook instanceof Shortcode));
    }

}
