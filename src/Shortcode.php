<?php

namespace SH\AutoHook;

use Attribute;

/**
 * Shortcode Attribute
 *
 * Used to hook a method to an WordPress shortcode
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Shortcode
{
    public function __construct(
        public string $tag,
        public ?string $callback = null,
    ) {}

    public function setByMethod(\ReflectionMethod $method): static
    {
        $this->callback = "{$method->getDeclaringClass()->getName()}::{$method->getName()}";

        return $this;
    }

    public function setByClass(\ReflectionClass $class): static
    {
        $this->callback = $this->callback ? "{$class->getName()}::$this->callback" : $class->getName();

        return $this;
    }

    public function __toString(): string
    {
        return "add_shortcode('$this->tag', '$this->callback');\n";
    }
}