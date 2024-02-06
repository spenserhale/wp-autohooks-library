<?php

namespace SH\AutoHook\Tests\Examples;

use SH\AutoHook\AutoHooks;
use SH\AutoHook\Hook;
use SH\AutoHook\Shortcode;

#[Hook('facade_example_hook', callback: 'facadeMethod', arguments: 3)]
#[Shortcode('other_shortcode', 'otherShortcode')]
class ExamplesClass
{
    #[Hook('cli_init', priority: 9)]
    public static function registerCommand(): void {}

    #[Hook('rest_api_init')]
    public static function registerController(): void {}

    #[Hook('wp_init', 99)]
    public static function boot(): void {}

    #[Hook(priority: 100, tag: 'pre_update_option')]
    public static function filterOption(string $value): string { return ''; }

    #[Hook('wp_insert_blog_meta')]
    #[Hook('wp_insert_post_meta')]
    public static function logInsert(array $data, array $postarr): array { return []; }

    #[Hook('update_post_meta'), Hook('update_option_meta')]
    public static function logChange(mixed $old_value, mixed $value): void {}

    #[Shortcode('example_shortcode')]
    public static function shortcode() {}
}