<?php
namespace Axado\Formatter;

class JsonFormatter implements FormatterInterface
{

    /**
     * Axado\Shipping instance.
     * @var Axado\Shipping
     */
    protected $instance;

    /**
     * Setter for instance.
     * @param VolumeInterface $instance
     */
    public function setInstance(\Axado\Shipping $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Format the attributes for instance given.
     * @return string
     */
    public function format()
    {
        $attributes = $this->instance->getAttributes();
        $volumes    = $this->instance->allVolumes();

        foreach ($volumes as $index => $volume) {
            $attributes["volumes"][$index] = $volume->volumeToArray();
        }

        return json_encode($attributes);
    }
}
