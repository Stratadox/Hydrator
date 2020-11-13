# Hydrator

[![Build Status](https://circleci.com/gh/Stratadox/Hydrator.svg?style=shield)](https://app.circleci.com/pipelines/github/Stratadox/Hydrator)
[![codecov](https://codecov.io/gh/Stratadox/Hydrator/branch/master/graph/badge.svg)](https://codecov.io/gh/Stratadox/Hydrator)
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

## What is this?

The `Hydrator` package exists in the context of object deserialization.
It is useful when loading objects from a data source.

To *hydrate* an object, means to assign values to its properties.

A [`Hydrator`](https://github.com/Stratadox/HydratorContracts) populates the 
fields of other objects.

Hydration generally works in tandem with [`Instantiation`](https://github.com/Stratadox/Instantiator);
the process of creating empty objects.

## How to use this?

### Basic Objects
The most basic usage looks like this:
```php
<?php
use Stratadox\Hydrator\ObjectHydrator;

$hydrator = ObjectHydrator::default();
$thing = new Thing();

$hydrator->writeTo($thing, [
    'foo'      => 'Bar.',
    'property' => 'value',
]);

assert($thing->foo === 'Bar.');
assert($thing->getProperty() === 'value');
```

The default hydrator requires the hydrated object to have access to all of its 
own properties.

When that's not the case, for instance when some properties are private to the 
parent, a `reflective` hydrator is available:
```php
<?php
use Stratadox\Hydrator\ReflectiveHydrator;

$hydrator = ReflectiveHydrator::default();
```

### Collection Objects
To hydrate collection objects, the `Hydrator` package provides either a 
`MutableCollectionHydrator`, suitable for most collection classes:
```php
<?php
use Stratadox\Hydrator\MutableCollectionHydrator;

$hydrator = MutableCollectionHydrator::default();
$collection = new SplFixedArray;

$hydrator->writeTo($collection, ['foo', 'bar']);

assert(2 === count($collection));
```

The `MutableCollectionHydrator` hydrates by mutating the collection object.
Naturally, this will not work when your collections are [immutable](https://github.com/Stratadox/ImmutableCollection),
in which case the `ImmutableCollectionHydrator` should be used instead.

## What else can it do?

The hydrators can be decorated to extend their capabilities.

### Mapping

To transform the input data with [hydration mapping](https://github.com/Stratadox/HydrationMapping),
the `Mapping` decorator can be used:
```php
<?php
use Stratadox\HydrationMapping\IntegerValue;
use Stratadox\HydrationMapping\StringValue;
use Stratadox\Hydrator\MappedHydrator;
use Stratadox\Hydrator\ObjectHydrator;

$hydrator = MappedHydrator::using(
    ObjectHydrator::default(), 
    StringValue::inProperty('title'),
    IntegerValue::inProperty('rating'),
    StringValue::inPropertyWithDifferentKey('isbn', 'id')
);

$book = new Book;
$hydrator->writeTo($book, [
    'title'  => 'This is a book.',
    'rating' => 3,
    'isbn'   => '0000000001'
]);
```

### Observing

The hydration process can be observed in two ways: before or after hydrating.

To observe the hydration process right before hydration begins, use:
```php
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ObserveBefore;

$hydrator = ObserveBefore::hydrating(ObjectHydrator::default(), new MyCustomObserver());
```
To observe the hydration process right after hydration is done, use:
```php
use Stratadox\Hydrator\ObjectHydrator;
use Stratadox\Hydrator\ObserveAfter;

$hydrator = ObserveAfter::hydrating(ObjectHydrator::default(), new MyCustomObserver());
```

The observer must be a [`HydrationObserver`](https://github.com/Stratadox/HydratorContracts/blob/master/src/HydrationObserver.php).
It will receive both the object instance and the input data.
