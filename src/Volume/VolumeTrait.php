<?php
namespace Axado\Volume;

trait VolumeTrait
{
    /**
     * Returns the instance as array.
     *
     * @return array
     */
    public function volumeToArray(): array
    {
        return [
            'sku' => $this->getSku(),
            'quantidade' => $this->getQuantity(),
            'preco' => $this->getPriceUnit(),
            'altura' => $this->getHeight(),
            'comprimento' => $this->getLength(),
            'largura' => $this->getWidth(),
            'peso' => $this->getWeight(),
        ];
    }
}
