<?php
namespace Axado\Formatter;

use Axado\Shipping;

interface FormatterInterface
{
    /**
     * Setter for Shipping instance inside Formatter.
     *
     * @param Shipping $instance
     */
    public function setInstance(Shipping $instance);

    /**
     * Format given instance.
     *
     * @return string
     */
    public function format(): string;
}
