# Hydrator

[![Implements](https://img.shields.io/badge/interfaces-github-blue.svg)](https://github.com/Stratadox/HydratorContracts)
[![Build Status](https://travis-ci.org/Stratadox/Hydrator.svg?branch=master)](https://travis-ci.org/Stratadox/Hydrator)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/Hydrator/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/Hydrator?branch=master)
[![Infection Minimum](https://img.shields.io/badge/msi-100-brightgreen.svg)](https://travis-ci.org/Stratadox/HydrationMapping)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/Hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/Hydrator/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/d257cc1d20eeeba2a95c/maintainability)](https://codeclimate.com/github/Stratadox/Hydrator/maintainability)
[![Latest Stable Version](https://poser.pugx.org/stratadox/hydrator/v/stable)](https://packagist.org/packages/stratadox/hydrator)
[![License](https://poser.pugx.org/stratadox/hydrator/license)](https://packagist.org/packages/stratadox/hydrator)

Lightweight hydrators, usable for various hydration purposes.
Hydrate away!


## Installation

Install with composer:

`composer require stratadox/hydrator`

## Basic Usage

```php
<?php

$hydrator = SimpleHydrator::forThe(Foo::class);
$foo = $hydrator->fromArray([
    'bar' => 'Bar.',
    'baz' => 'BAZ?',
]);
assert($foo instanceof Foo);
```
[(It can do much more!)](https://github.com/Stratadox/Hydrate)

## More information

For more information, view the [Hydrate repository](https://github.com/Stratadox/Hydrate).
