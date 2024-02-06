<?php

namespace SH\AutoHook;

use JsonException;
use RuntimeException;

class NamespaceClassMapper
{
    /**
     * @return array<string, array<string>>
     *      Array of namespaces and their classes
     * @throws JsonException
     * @throws RuntimeException
     */
    public static function fromComposerJson(string $file): array
    {
        ['namespaces' => $namespaces, 'classes' => $classes] = ComposerJsonParser::getNamespacesAndClasses($file);

        return self::filterClasses(array_keys($namespaces), array_keys($classes));
    }

    /**
     * @param  string[]  $namespaces
     * @param  string[]  $classes
     *
     * @return array<string, array<string>>
     *     Array of namespaces and their classes
     */
    public static function filterClasses(array $namespaces, array $classes): array
    {
        // Sort by longest namespace depth first ie ACME\Module\Service before ACME\Service
        usort($namespaces, static fn($a, $b) => substr_count($b, '\\') <=> substr_count($a, '\\'));

        $results = [];
        foreach ($namespaces as $namespace) {
            $results[$namespace] = [];

            foreach ($classes as $key => $class) {
                if (str_starts_with($class, $namespace)) {
                    $results[$namespace][] = $class;

                    unset($classes[$key]);
                }
            }
        }

        return $results;
    }
}
