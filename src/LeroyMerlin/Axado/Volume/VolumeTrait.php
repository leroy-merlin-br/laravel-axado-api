<?php
namespace Axado\Volume;

trait VolumeTrait {

    /**
     * Returns the instance parsed to Json.
     * @return string
     */
    public function volumeToJson()
    {
        $attributes = [];

        $attributes['sku']         = $this->getSku();
        $attributes['quantidade']  = $this->getQuantity();
        $attributes['preco']       = $this->getPriceUnit();
        $attributes['altura']      = $this->getHeight();
        $attributes['comprimento'] = $this->getLength();
        $attributes['largura']     = $this->getWidth();
        $attributes['peso']        = $this->getWeight();

        return json_encode($attributes);
    }
}
