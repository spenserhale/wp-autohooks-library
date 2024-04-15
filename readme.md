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
   ```php
    #[Hook('cli_init')]
    public static function register(): void {...}
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
