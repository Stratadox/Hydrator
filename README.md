# Hydrator

[![Build Status](https://travis-ci.org/Stratadox/Hydrator.svg?branch=master)](https://travis-ci.org/Stratadox/Hydrator)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/Hydrator/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/Hydrator?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/Hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/Hydrator/?branch=master)

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

