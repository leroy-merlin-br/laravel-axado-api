<?php
namespace Axado\Formatter;

class JsonFormatter implements FormatterInterface
{

    /**
     * Axado\Shipping instance.
     *
     * @var Axado\Shipping
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    public function setInstance(\Axado\Shipping $instance)
    {
        $this->instance = $instance;
    }

    /**
     * {@inheritdoc}
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
