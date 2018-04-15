# Hydrator

[![Build Status](https://travis-ci.org/Stratadox/Hydrator.svg?branch=master)](https://travis-ci.org/Stratadox/Hydrator)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/Hydrator/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/Hydrator?branch=master)
[![Infection Minimum](https://img.shields.io/badge/msi-100-brightgreen.svg)](https://travis-ci.org/Stratadox/Hydrator)
[![PhpStan Level](https://img.shields.io/badge/phpstan-7-brightgreen.svg)](https://travis-ci.org/Stratadox/Hydrator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/Hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/Hydrator/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/d257cc1d20eeeba2a95c/maintainability)](https://codeclimate.com/github/Stratadox/Hydrator/maintainability)
[![Latest Stable Version](https://poser.pugx.org/stratadox/hydrator/v/stable)](https://packagist.org/packages/stratadox/hydrator)
[![License](https://poser.pugx.org/stratadox/hydrator/license)](https://packagist.org/packages/stratadox/hydrator)

[![Implements](https://img.shields.io/badge/interfaces-github-blue.svg)](https://github.com/Stratadox/HydratorContracts)
[![Latest Stable Version](https://poser.pugx.org/stratadox/hydrator-contracts/v/stable)](https://packagist.org/packages/stratadox/hydrator-contracts)
[![License](https://poser.pugx.org/stratadox/hydrator-contracts/license)](https://packagist.org/packages/stratadox/hydrator-contracts)

Lightweight hydrators, usable for various hydration purposes.
Hydrate away!


## Installation

Install with composer:

`composer require stratadox/hydrator`

## Basic Usage

```php
<?php
use Stratadox\Hydrator\SimpleHydrator;

$hydrator = SimpleHydrator::forThe(Foo::class);
$foo = $hydrator->fromArray([
    'bar' => 'Bar.',
    'baz' => 'BAZ?',
]);
assert($foo instanceof Foo);
assert('Bar.' === $foo->bar);
assert('BAZ?' === $foo->getBaz());
```

[(It can do much more!)](https://github.com/Stratadox/Hydrate)

## What is this?

The `Hydrator` package exists in the context of object deserialization.
It is useful when loading objects from a data source, such as a database or an API. 

To *hydrate* an object, means to assign values to its properties.

An object that [`Hydrates`](https://github.com/Stratadox/HydratorContracts/blob/master/src/Hydrates.php)
can populate the fields of other objects.

Hydration works in tandem with [`Instantiation`](https://github.com/Stratadox/Instantiator);
the process of creating empty objects.

### How does it work?

Rather than relying on reflection, this package leverages the speed and elegance 
of [Closure binding](http://php.net/manual/en/closure.call.php) to assign values.

### What does it do?

It takes an input array, and transforms it into a fully hydrated object.

This can be done in several ways:

#### Simple hydration

The most basic hydrator, `SimpleHydrator`, will simply assign each key/value pair
in the input array to a property/value in the object.

#### Mapped hydration

When the input array and the property values are not a 1-to-1 match, the 
`MappedHydrator` can be used instead. This more advanced hydrator takes a list
of [`Property Mappings`](https://github.com/Stratadox/HydrationMapping) to 
determine what values are assigned to which properties.

#### Collections

Collections can be hydrated with either the `ArrayHydrator`, when the items are 
contained in an array, or by using the `VariadicContructor`, for items that are 
contained in, for instance, an [`ImmutableCollection`](https://github.com/Stratadox/ImmutableCollection).

#### Abstractions

The `OneOfTheseHydrators` class is practical when mapping abstract classes or 
interfaces. It defers hydration to one of several hydrators based on the value 
of one of the keys in the input array. This functionality is useful when 
implementing, for example, a [single table inheritance](https://www.martinfowler.com/eaaCatalog/singleTableInheritance.html) 
mapping.

# Hydrate

This package is part of the [Hydrate Module](https://github.com/Stratadox/Hydrate).

The `Hydrate` module is an umbrella for several hydration-related packages.
Together, they form a powerful toolset for converting input data into an object structure.

Although these packages are designed to work together, they can also be used independently.
The only hard dependencies of this `Hydrator` module are an instantiator and a set of packages dedicated only to interfaces.
