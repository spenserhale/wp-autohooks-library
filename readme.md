## WP AutoHooks

![Screen Shot][product-screenshot]

WP AutoHooks is a PHP Library that allows you to define Attributes on Classes and Methods to
document and register WordPress Hooks.

## Features

- **Developer Experience**: Document hooks next to the method, giving you context and a better developer experience.
- **Performance**: Hooks are registered without loading classes or instantiating objects until needed.
- **Flexibility**: The underlying PHP Library can be used for both simple and complex projects.
- **Modularity**: Ability to add and remove standalone classes quickly and easily.

<!-- GETTING STARTED -->

## Getting Started

### Prerequisites

By default, the library is geared toward Composer, but there is flexibility, and you can
integrate the library with your class loading system.

### Installation

   ```sh
   composer require spenserhale/wp-autohook-library
   ```

### Basic Usage

Attribute your class and methods with the `Hook` and `Shortcode` attributes.

#### Method-Level Attributes

Apply attributes directly to methods. The method name is automatically used as the callback:

```php
#[Hook('cli_init', priority: 9)]
public static function registerCommand(): void {}

#[Hook('rest_api_init')]
public static function registerController(): void {}

#[Hook('wp_init', 99)]
public static function boot(): void {}
```

#### Named Parameters

Use named parameters for flexibility in argument order:

```php
#[Hook(priority: 100, tag: 'pre_update_option')]
public static function filterOption(string $value): string {}
```

#### Multiple Hooks on a Single Method

Apply multiple hooks to the same method using stacked attributes or comma-separated syntax:

```php
// Stacked attributes
#[Hook('wp_insert_blog_meta')]
#[Hook('wp_insert_post_meta')]
public static function logInsert(array $data, array $postarr): array {}

// Comma-separated (same line)
#[Hook('update_post_meta'), Hook('update_option_meta')]
public static function logChange(mixed $old_value, mixed $new_value): void {}
```

#### Shortcodes

Register shortcodes using the `Shortcode` attribute:

```php
#[Shortcode('example_shortcode')]
public static function shortcode(): string {}
```

#### Class-Level Attributes (Inherited Methods)

Apply attributes at the class level when you need to hook methods inherited from a parent class. Since attributes on parent methods aren't scanned in child classes, class-level attributes let you register hooks for inherited methods:

```php
abstract class AbstractAdminPage
{
    public static function registerPage(): void {}
    
    public static function renderPage(): void {}
}

#[Hook('admin_menu', callback: 'registerPage')]
#[Hook('admin_init', callback: 'renderPage')]
class SettingsPage extends AbstractAdminPage
{
    // Inherited methods are hooked via class-level attributes
}
```

#### Complete Example

```php
namespace ACME;

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
    public static function filterOption(string $value): string {}

    #[Hook('wp_insert_blog_meta')]
    #[Hook('wp_insert_post_meta')]
    public static function logInsert(array $data, array $postarr): array {}

    #[Hook('update_post_meta'), Hook('update_option_meta')]
    public static function logChange(mixed $old_value, mixed $new_value): void {}

    #[Shortcode('example_shortcode')]
    public static function shortcode(): string {}
    
    public static function facadeMethod($arg1, $arg2, $arg3): mixed {}
    
    public static function otherShortcode(): string {}
}
```

#### Generated Output

The above example generates:

```php
//=== Start AutoHooks Generated Section ===
add_action('cli_init', 'ACME\ExamplesClass::registerCommand', 9, 0);
add_filter('facade_example_hook', 'ACME\ExamplesClass::facadeMethod', 10, 3);
add_filter('pre_update_option', 'ACME\ExamplesClass::filterOption', 100, 1);
add_action('rest_api_init', 'ACME\ExamplesClass::registerController', 10, 0);
add_action('update_option_meta', 'ACME\ExamplesClass::logChange', 10, 2);
add_action('update_post_meta', 'ACME\ExamplesClass::logChange', 10, 2);
add_action('wp_init', 'ACME\ExamplesClass::boot', 99, 0);
add_filter('wp_insert_blog_meta', 'ACME\ExamplesClass::logInsert', 10, 2);
add_filter('wp_insert_post_meta', 'ACME\ExamplesClass::logInsert', 10, 2);
add_shortcode('example_shortcode', 'ACME\ExamplesClass::shortcode');
add_shortcode('other_shortcode', 'ACME\ExamplesClass::otherShortcode');
//=== End AutoHooks Generated Section ===
```

#### Build/Wire Up

  ```php
// Get the list of classes
$classes = \SH\AutoHook\ComposerJsonParser::getClasses($composerJsonPath);

// Process classes to string
[$output] = \SH\AutoHook\AttributeResolver::processClassesToString($classes);

// Write to file
(bool) $written = \SH\AutoHook\FileWriter::write($output, $outputPath);
   ```

### Advanced Usage

Some projects may not have the composer.json available at runtime, so you can use the class loader object.

#### Build/Wire Up

  ```php
// Get Classloader object
$loader = require 'vendor/autoload.php';

// Get the list of classes
$classes = \SH\AutoHook\ComposerClassLoaderParser::getClasses($loader, ['App\\', 'MyNamespace\\']);

// Process classes to string
[$output] = \SH\AutoHook\AttributeResolver::processClassesToString($classes);

// Write to file
(bool) $written = \SH\AutoHook\FileWriter::write($output, $outputPath);
   ```

## Tests

To run tests, make sure to create class list through composer:
   ```sh
   composer du -o
   ```

Then run the tests:
   ```sh
    composer test
   ```

## License
The WordPress AutoHooks Library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[product-screenshot]: images/explainer.png
