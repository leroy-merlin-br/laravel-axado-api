<?php
namespace Axado\Formatter;

interface FormatterInterface {

    /**
     * Set Axado\Shipping instance.
     */
    public function setInstance(\Axado\Shipping $instance);

    /**
     * Formats the instance given.
     * @return a data format
     */
    public function format();
}
