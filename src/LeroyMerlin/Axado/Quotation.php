<?php
namespace Axado;

class Quotation {

    /**
     * Fields with be parsed after the response is returned.
     *
     * @var array
     */
    protected $possibleAttr = [
        "transportadora_metaname" => "name",
        "servico_metaname"        => "service_metaname",
        "servico_nome"            => "service_name",
        "cotacao_preco"           => "quotation_price",
        "cotacao_custo"           => "quotation_costs",
        "cotacao_prazo"           => "deadline"
    ];

    /**
     * Attributes parsed.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Fill this object with raw attributes given by Response.
     *
     * @param  array $rawAttributes
     * @return null
     */
    public function fill($rawAttributes)
    {
        foreach ($rawAttributes as $key => $value) {
            if (array_key_exists($key, $this->possibleAttr)) {
                $keyParsed = $this->possibleAttr[$key];

                $this->attributes[$keyParsed] = $value;
            }
        }
    }

    /**
     * Return all attributes parsed.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }
}
