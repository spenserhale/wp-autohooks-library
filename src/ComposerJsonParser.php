<?php

namespace SH\AutoHook;

use InvalidArgumentException;
use JsonException;

class ComposerJsonParser
{
    /**
     * Returns an array of classes in namespaces defined in the composer.json file.
     *
     * @return array<string>
     *
     * @throws JsonException
     * @throws InvalidArgumentException
     */
    public static function getClasses(string $file): array
    {
        ['namespaces' => $namespaces, 'classes' => $classes] = self::getNamespacesAndClasses($file);

        $classes = array_keys($classes);

        $matches = [];
        foreach (array_keys($namespaces) as $namespace) {
            foreach ($classes as $key => $class) {
                if (str_starts_with($class, $namespace)) {
                    $matches[] = $class;

                    unset($classes[$key]);
                }
            }
        }

        return $matches;
    }

    /**
     * Returns an array with detailed information about namespaces and classes.
     *
     * Namespaces(k:namespace, v:path): map of namespaces and their relative path.
     * Classes(k:class, v:path): map of classes and their relative path.
     *
     * @return array{
     * namespaces: array<string, string>,
     * classes: array<string, string>
     * }
     *
     * Example:
     * [
     * 'namespaces' => [
     * 'MyApp\\Controllers' => 'src/Controllers/',
     * 'MyApp\\Models' => 'src/Models/'
     * ],
     * 'classes' => [
     * 'MyApp\\Controllers\\HomeController' => 'src/Controllers/HomeController.php',
     * 'MyApp\\Models\\UserModel' => 'src/Models/UserModel.php'
     * ]
     * ]
     *
     * @throws JsonException
     * @throws InvalidArgumentException
     */
    public static function getNamespacesAndClasses(string $file): array
    {
        $composerJson = self::parseFile($file);

        $folder = dirname($file);
        $vendorDir = $composerJson['config']['vendor-dir'] ?? 'vendor';

        return [
            'namespaces' => $composerJson['autoload']['psr-4'] ?? [],
            'classes'    => require "$folder/$vendorDir/composer/autoload_classmap.php"
        ];
    }

    /**
     * @return array{
     *  autoload: array{
     *      "psr-4": array<string, string>
     *  },
     *  config: array{
     *     vendor-dir: string
     *  }
     * }
     *
     * @throws JsonException
     * @throws InvalidArgumentException
     */
    public static function parseFile(string $file): array
    {
        if ( ! is_file($file)) {
            throw new InvalidArgumentException("File not found: $file");
        }

        $contents = file_get_contents($file);
        if ( ! $contents) {
            throw new InvalidArgumentException("Could not read file: $file");
        }

        return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    }
}
