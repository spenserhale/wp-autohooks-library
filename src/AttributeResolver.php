<?php

namespace SH\AutoHook;

use ReflectionClass;
use ReflectionMethod;
use Throwable;

class AttributeResolver
{
    /**
     * @param  array<string>  $classes
     *
     * @return array{
     *    0: string,
     *    1: list<Throwable>
     *  }
     */
    public static function processClassesToString(array $classes): array
    {
        [$list, $errors] = static::processClassesToList($classes);

        usort($list, [self::class, 'sortHooksList']);

        $output = implode('', array_map('strval', $list));

        return [$output, $errors];
    }


    /**
     * @param  array<string>  $classes
     *
     * @return array{
     *    0: list<Hook|Shortcode>,
     *    1: list<Throwable>
     *  }
     */
    public static function processClassesToList(array $classes): array
    {
        $hooks  = [];
        $errors = [];

        foreach ($classes as $class) {
            try {
                $reflector = new ReflectionClass($class);
                self::processClassAttributes($hooks, $reflector);

                foreach ($reflector->getMethods() as $method) {
                    self::processMethodAttributes($hooks, $method);
                }
            } catch (Throwable $e) {
                $errors[] = $e;
            }
        }

        return [$hooks, $errors];
    }

    private static function processClassAttributes(array &$hooks, ReflectionClass $reflector): void
    {
        foreach (self::getAttributeInstances($reflector, Hook::class) as $hook) {
            $hooks[] = $hook->setByClass($reflector);
        }

        foreach (self::getAttributeInstances($reflector, Shortcode::class) as $shortcode) {
            $hooks[] = $shortcode->setByClass($reflector);
        }
    }

    private static function processMethodAttributes(array &$hooks, ReflectionMethod $reflector): void
    {
        foreach (self::getAttributeInstances($reflector, Hook::class) as $hook) {
            $hooks[] = $hook->setByMethod($reflector);
        }

        foreach (self::getAttributeInstances($reflector, Shortcode::class) as $shortcode) {
            $hooks[] = $shortcode->setByMethod($reflector);
        }
    }

    /**
     * @template T
     * @param ReflectionClass|ReflectionMethod $reflector
     * @param class-string<T> $attribute
     *
     * @return T[]
     */
    private static function getAttributeInstances(ReflectionClass|ReflectionMethod $reflector, string $attribute): array
    {
        return array_map(static fn($a) => $a->newInstance(), $reflector->getAttributes($attribute));
    }

    /**
     * Sorts the list of hooks and shortcodes
     * - Hooks are sorted before shortcodes
     * - Hooks and shortcodes are sorted by tag name ascending
     * - Hooks and shortcodes are sorted by priority descending
     */
    public static function sortHooksList($a, $b): int
    {
        if ($a instanceof Shortcode && $b instanceof Hook) {
            return 1;
        }

        if ($a instanceof Hook && $b instanceof Shortcode) {
            return -1;
        }

        if ($a->tag === $b->tag) {
            return $b->priority <=> $a->priority;
        }

        return $a->tag <=> $b->tag;
    }

}