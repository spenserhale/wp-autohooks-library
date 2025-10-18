<?php

namespace SH\AutoHook\Tests\Examples;

use SH\AutoHook\Hook;

#[Hook('admin_menu', callback: 'register')]
abstract class AbstractAdminPage
{
    public static function register(): void {}
}

