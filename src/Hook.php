<?php

namespace SH\AutoHook;

use Attribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * Hook Attribute
 *
 * Used to hook a method to an WordPress hook at a specific priority.
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Hook
{
    /**
     * Return types that should use add_action instead of add_filter
     */
    private const ACTION_RETURN_TYPES = ['void', 'never'];

    public function __construct(
        public string $tag,
        public int $priority = 10,
        public ?string $callback = null,
        public ?int $arguments = 1,
        public string $type = 'add_filter'
    ) {}

    public function setByMethod(ReflectionClass $class, ReflectionMethod $method): static
    {
        $this->callback = "{$class->getName()}::{$method->getName()}";
        $this->arguments = $method->getNumberOfParameters();
        $type = $method->getReturnType();
        if($this->shouldUseAction($type)) {
            $this->type = 'add_action';
        }

        return $this;
    }

    public function setByClass(ReflectionClass $class): static
    {
        if($this->callback) {
            try {
                $type = $class->getMethod($this->callback)->getReturnType();
                if($this->shouldUseAction($type)) {
                    $this->type = 'add_action';
                }
            } catch (ReflectionException) {
            }

            $this->callback = "{$class->getName()}::$this->callback";
        } else {
            $this->callback = $class->getName();
        }

        return $this;
    }

    /**
     * Determine if the return type should use add_action instead of add_filter
     */
    private function shouldUseAction(mixed $type): bool
    {
        return $type instanceof ReflectionNamedType 
            && in_array($type->getName(), self::ACTION_RETURN_TYPES, true);
    }

    public function __toString(): string
    {
        $params = ["'$this->tag'", "'$this->callback'"];
        if ($this->priority !== 10 || $this->arguments !== 1) {
            $params[] = $this->priority;
            if ($this->arguments !== 1) {
                $params[] = $this->arguments;
            }
        }
        return "$this->type(" . implode(', ', $params) . ");\n";
    }
}
