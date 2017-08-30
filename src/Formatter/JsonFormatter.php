<?php
namespace Axado\Formatter;

use Axado\Shipping;

class JsonFormatter implements FormatterInterface
{
    /**
     * Axado\Shipping instance.
     *
     * @var Shipping
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    public function setInstance(Shipping $instance)
    {
        $this->instance = $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function format(): string
    {
        $attributes = $this->instance->getAttributes();
        $volumes = $this->instance->allVolumes();

        foreach ($volumes as $index => $volume) {
            $attributes['volumes'][$index] = $volume->volumeToArray();
        }

        return json_encode($attributes);
    }
}
