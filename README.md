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

## What is this?

The `Hydrator` package exists in the context of object deserialization.
It is useful when loading objects from a data source, such as a database or an API. 

To *hydrate* an object, means to assign values to its properties.

An object that [`Hydrates`](https://github.com/Stratadox/HydratorContracts/blob/master/src/Hydrates.php)
can populate the fields of other objects.

Hydration generally works in tandem with [`Instantiation`](https://github.com/Stratadox/Instantiator);
the process of creating empty objects.

## Basic Usage

```php
<?php
use Stratadox\Hydrator\ObjectHydrator;

$hydrator = ObjectHydrator::default();
$thing = new Thing;

$hydrator->writeTo($thing, [
    'foo' => 'Bar.',
    'bar' => 'BAZ?',
]);

assert('Bar.' === $thing->foo);
assert('BAZ?' === $thing->getBar());
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

For collection objects, the `CollectionHydrator` should be used:
```php
<?php
use Stratadox\Hydrator\CollectionHydrator;

$hydrator = CollectionHydrator::default();
$collection = new SplFixedArray;

$hydrator->writeTo($collection, ['foo', 'bar']);

assert(2 === count($collection));
```
