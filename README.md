Laravel Axado API
=================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/?branch=master)
[![Coverage Status](https://coveralls.io/repos/leroy-merlin-br/laravel-axado-api/badge.png?branch=master)](https://coveralls.io/r/leroy-merlin-br/laravel-axado-api?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/build-status/master)
[![Build Status](https://travis-ci.org/leroy-merlin-br/laravel-axado-api.svg)](https://travis-ci.org/leroy-merlin-br/laravel-axado-api)

A wrapper to Axado API.

## Get started

Add this package into your `composer.json`.

> "leroy-merlin-br/laravel-axado-api" : "~1.0"

Run `composer update`

> composer update

## Setup

### Uses in your class VolumeTrait and VolumeInterface

For example:

    class Stub implements Axado\Volume\VolumeInterface {
        use Axado\Volume\VolumeTrait;

        public function getSku()       { return "123"; }
        public function getQuantity()  { return 10; }
        public function getPriceUnit() { return 10.5; }
        public function getHeight()    { return 10; }
        public function getLength()    { return 10; }
        public function getWidth()     { return 10; }
        public function getWeight()    { return 10; }
    }


### Setting the Token API.

    \Axado\Shipping::$token = "your-token";

### Creating a new Shipping

    $shipping = new Axado\Shipping;

    $shipping->setPostalCodeOrigin('04661100');
    $shipping->setPostalCodeDestination('13301430');
    $shipping->setTotalPrice('40');
    $shipping->setAditionalDays('10');
    $shipping->setAditionalPrice('12.6');

### Adding Volume

    $volume = new Stub;
    $shipping->addVolume($volume);

### Getting all quotations

    $shipping->quotations();

### Get costs and deadline

    $shipping->getCosts();     // in reais
    $shipping->getDeadline();  // in days
