<?php

namespace SH\AutoHook;

use \Composer\Autoload\ClassLoader;

class ComposerClassLoaderParser
{
    /**
     * Returns an array of classes in namespaces defined in the composer.json file.
     *
     * @param ClassLoader $classLoader
     * @param array<string> $namespaces Since the classloader does not know which are project vs vendor namespaces, you can pass in an array of namespaces to filter by.
     *
     * @return array<string>
     */
    public static function getClasses(ClassLoader $classLoader, array $namespaces = []): array
    {
        ['namespaces' => $allNamespaces, 'classes' => $classes] = self::getNamespacesAndClasses($classLoader);

        $namespaces = $namespaces ?: array_keys($allNamespaces);
        $classes = array_keys($classes);

        $matches = [];
        foreach ($namespaces as $namespace) {
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
     * Namespaces(k:namespace, v:paths array): map of namespaces and their absolute paths.
     * Classes(k:class, v:path): map of classes and their absolute path.
     *
     * @return array{
     * namespaces: array<string, array<string>>,
     * classes: array<string, string>
     * }
     *
     * Example:
     * [
     * 'namespaces' => [
     * 'MyApp\\Controllers' => ['/Users/jphndpe/Project/src/Controllers/'],
     * 'MyApp\\Models' => ['/Users/jphndpe/Project/src/Models/'],
     * ],
     * 'classes' => [
     * 'MyApp\\Controllers\\HomeController' => '/Users/jphndpe/Project/src/Controllers/HomeController.php',
     * 'MyApp\\Models\\UserModel' => '/Users/jphndpe/Project/src/Models/UserModel.php',
     * ]
     * ]
     */
    public static function getNamespacesAndClasses(ClassLoader $classLoader): array
    {
        return [
            'namespaces' => $classLoader->getPrefixesPsr4(),
            'classes'    => $classLoader->getClassMap()
        ];
    }
}