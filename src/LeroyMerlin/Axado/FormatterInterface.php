<?php
namespace Axado;

interface FormatterInterface {

    /**
     * Set Axado\Shipping instance.
     */
    public function setInstance(Shipping $instance);

    /**
     * Formats the instance given.
     * @return a data format
     */
    public function format();
}
