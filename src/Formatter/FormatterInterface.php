<?php
namespace Axado\Formatter;

interface FormatterInterface {

    /**
     * Setter for Shipping instance inside Formatter.
     *
     * @param \Axado\Shipping $instance
     * @return null
     */
    public function setInstance(\Axado\Shipping $instance);

    /**
     * Format the instance given.
     *
     * @return string
     */
    public function format();
}
