<?php

namespace SH\AutoHook;

use Attribute;

/**
 * Hook Attribute
 *
 * Used to hook a method to an WordPress hook at a specific priority.
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Hook
{
    public function __construct(
        public string $tag,
        public int $priority = 10,
        public ?string $callback = null,
        public ?int $arguments = 1,
        public string $type = 'add_filter'
    ) {}

    public function setByMethod(\ReflectionClass $class, \ReflectionMethod $method): static
    {
        $this->callback = "{$class->getName()}::{$method->getName()}";
        $this->arguments = $method->getNumberOfParameters();
        if($method->getReturnType()?->getName() === 'void') {
            $this->type = 'add_action';
        }

        return $this;
    }

    public function setByClass(\ReflectionClass $class): static
    {
        $this->callback = $this->callback ? "{$class->getName()}::$this->callback" : $class->getName();

        return $this;
    }

    public function __toString(): string
    {
        return "$this->type('$this->tag', '$this->callback', $this->priority, $this->arguments);\n";
    }
}