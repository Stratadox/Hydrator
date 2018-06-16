<?php
declare(strict_types=1);

namespace Stratadox\Hydrator\Test\Unit;

use Exception;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Stratadox\Hydrator\CannotHydrate;
use Stratadox\Hydrator\Test\Fixture\Colour;
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\Test\Fixture\Popo;
use function substr;

/**
 * @covers \Stratadox\Hydrator\ObjectHydrator
 * @covers \Stratadox\Hydrator\HydrationFailed
 */
class ObjectHydrator_writes_array_values_to_properties extends TestCase
{
    private const TESTS = 25;

    /**
     * @test
     * @dataProvider colours
     */
    function hydrating_the_hexCode_property_into_a_colour_object(
        string $code,
        Colour $expectedColour,
        Colour $unexpectedColour
    ) {
        /** @var Colour $actualColour */
        $actualColour = (new ReflectionClass(Colour::class))
            ->newInstanceWithoutConstructor();

        ObjectHydrator::default()->writeTo($actualColour, ['hexCode' => $code]);

        $this->assertTrue($actualColour->equals($expectedColour));
        $this->assertFalse($actualColour->equals($unexpectedColour));
    }

    /**
     * @test
     * @dataProvider namesAndValues
     */
    function converting_hydration_exceptions_to_CannotHydrate(
        string $name,
        string $value
    ) {
        $object = new Popo;
        $popo = Popo::class;

        $hydrator = ObjectHydrator::using(function (string $name, $value) {
            throw new Exception("Not setting `$name` to `$value`.");
        });

        $this->expectException(CannotHydrate::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            "Could not hydrate the `$popo`: Not setting `$name` to `$value`."
        );

        $hydrator->writeTo($object, [$name => $value]);
    }

    public function colours(): array
    {
        $random = Factory::create()->unique();
        $sets = [];
        for ($i = self::TESTS; $i > 0; --$i) {
            $hex = $random->hexColor;
            $otherHex = $random->hexColor;
            $sets["$hex / $otherHex"] = [
                substr($hex, 1),
                Colour::withCode($hex),
                Colour::withCode($otherHex)
            ];
        }
        return $sets;
    }

    public function namesAndValues(): array
    {
        $random = Factory::create()->unique();
        $sets = [];
        for ($i = self::TESTS; $i > 0; --$i) {
            $name = $random->word;
            $value = $random->randomElement([
                $random->word,
                $random->sentence,
                $random->email,
                $random->name
            ]);
            $sets["$name => $value"] = [$name, $value];
        }
        return $sets;
    }
}