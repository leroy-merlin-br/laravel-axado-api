<?php

include "vendor/autoload.php";

/**
 * Implements the Axado\Volume\VolumeInterface and uses Axado\Volume\VolumeTrait.
 */
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

/**
 * Set the token api.
 */
\Axado\Shipping::$token = "your-token";

/**
 * Create a Axado Shipping.
 */
$shipping = new Axado\Shipping;

/**
 * Setting a sale order infos
 */
$shipping->setPostalCodeOrigin('04661100');
$shipping->setPostalCodeDestination('13301430');
$shipping->setTotalPrice('40');
$shipping->setAditionalDays('10');
$shipping->setAditionalPrice('12.6');

/**
 * Add volumes to shipping
 */
$volume = new Stub;
$shipping->addVolume($volume);

/**
 * Getting all quotations
 */
var_dump($shipping->quotations());

/**
 * Get the costs and dealine to the first Quotation.
 */
var_dump($shipping->getCosts());     // in reais
var_dump($shipping->getDeadline());  // in days

$shipping->flagAsContracted();
