Laravel Axado API
=================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/?branch=master) [![Coverage Status](https://coveralls.io/repos/leroy-merlin-br/laravel-axado-api/badge.png?branch=master)](https://coveralls.io/r/leroy-merlin-br/laravel-axado-api?branch=master) [![Build Status](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/leroy-merlin-br/laravel-axado-api/build-status/master) [![Build Status](https://travis-ci.org/leroy-merlin-br/laravel-axado-api.svg)](https://travis-ci.org/leroy-merlin-br/laravel-axado-api)

A wrapper to Axado API.

## Instalation

To get started, install Laravel Axado API via the Composer package manager:

```bash
composer require leroy-merlin-br/laravel-axado-api
```

## Setup

Make your `Product` class implement `VolumeInterface`.
Optionally, you can use `VolumeTrait` too. For example:
    
```php
class Product implements Axado\Volume\VolumeInterface {
    use Axado\Volume\VolumeTrait;

    public function getSku()       { return "123"; }
    public function getQuantity()  { return 10; }
    public function getPriceUnit() { return 10.5; }
    public function getHeight()    { return 10; }
    public function getLength()    { return 10; }
    public function getWidth()     { return 10; }
    public function getWeight()    { return 10; }
}
```

## Usage

- Setting the Token API.
    ```php
    \Axado\Shipping::$token = "your-token";
    ```

- Creating a new Shipping
    ```php
    $shipping = new Axado\Shipping();
    
    $shipping->setPostalCodeOrigin('04661100');
    $shipping->setPostalCodeDestination('13301430');
    $shipping->setTotalPrice('40');
    $shipping->setAditionalDays('10');
    $shipping->setAditionalPrice('12.6');
    ```

- Adding Volume
    ```php
    $volume = new Product();
    $shipping->addVolume($volume);
    ```

- Getting all quotations
    ```php
    $shipping->quotations();
    ```

- Getting costs and deadline
    ```php
    $shipping->getCosts();      // in reais
    $shipping->getDeadline();   // in days
    ```

- Marking the last quotation as contracted
```php
$shipping->flagAsContracted();
```
