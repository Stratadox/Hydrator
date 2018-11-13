<?php declare(strict_types=1);

namespace Stratadox\Hydrator;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionObject;
use function sprintf;
use function strlen;
use function strpos;
use function substr;
use Throwable;

/**
 * Hydrates properties in a specific scope by deconstructing the input.
 *
 * Useful in the specific case where a child class has a private property, while
 * its parent(s) also have a private property by that very same name.
 * In those edge cases, the reflective hydrator cannot correctly determine the
 * property scope, nor would client code be able to pass both properties in the
 * same map.
 * This hydrator avoids that problem by requiring an explicit scope in the form
 * of one or more subsequent prefixes, for example:
 * `{"property": "foo", "parent.property": "bar"}`
 *
 * @package Stratadox\Hydrate
 * @author  Stratadox
 */
final class ScopedHydrator implements Hydrates
{
    private $prefix;
    private $prefixLength;

    private function __construct(string $prefix)
    {
        $this->prefix = $prefix;
        $this->prefixLength = strlen($prefix);
    }

    /**
     * Produce a scoped hydrator.
     *
     * @return Hydrates A hydrator that uses prefixes to determine the scopes.
     */
    public static function default(): Hydrates
    {
        return new self('parent.');
    }

    /** @inheritdoc */
    public function writeTo(object $target, array $data): void
    {
        $object = new ReflectionObject($target);
        foreach ($data as $name => $value) {
            try {
                $this->write($object, $target, $name, $value);
            } catch (Throwable $exception) {
                throw HydrationFailed::encountered($exception, $target);
            }
        }
    }

    private function write(
        ReflectionClass $class,
        object $target,
        string $propertyName,
        $value
    ): void {
        $name = $propertyName;
        while (true) {
            if (strpos($name, $this->prefix) !== 0) {
                break;
            }
            $name = substr($name, $this->prefixLength);
            $class = $class->getParentClass();
        }
        if (!$class) {
            throw new InvalidArgumentException(sprintf(
                'It has no %s.',
                $propertyName
            ));
        }
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($target, $value);
    }
}
