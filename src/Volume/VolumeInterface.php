<?php
namespace Axado\Volume;

interface VolumeInterface
{
    /**
     * Returns the SKU id.
     *
     * @return string
     */
    public function getSku();

    /**
     * Returns the quantity of this product.
     *
     * @return int
     */
    public function getQuantity();

    /**
     * Return the price unit.
     *
     * @return float
     */
    public function getPriceUnit();

    /**
     * Get height for a volume.
     *
     * @return int
     */
    public function getHeight();

    /**
     * Get Length for a volume in centimeters.
     *
     * @return int
     */
    public function getLength();

    /**
     * Get the Width in centimeters.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Returns the weight for a volume in Kilogram.
     *
     * @return float
     */
    public function getWeight();

    /**
     * Json for volume
     *
     * @return array
     */
    public function volumeToArray();
}
