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
        "cotacao_prazo"           => "deadline",
        "cotacao_codigo"          => "quotation_id"
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

    /**
     * Returns the quotation code.
     * @return null|string
     */
    public function getQuotationCode()
    {
        if (isset($this->attributes['quotation_id'])) {
            return $this->attributes['quotation_id'];
        }

        return null;
    }

    /**
     * Returns the cost.
     * @return null|string
     */
    public function getCosts()
    {
        if (isset($this->attributes['quotation_price'])) {
            return $this->attributes['quotation_price'];
        }

        return null;
    }

     /**
     * Returns the deadline in days
     * @return null|string
     */
    public function getDeadline()
    {
        if (isset($this->attributes['deadline'])) {
            return $this->attributes['deadline'];
        }

        return null;
    }

    /**
     * Magic attribute getter
     *
     * @param  string $attrName Attribute name
     * @return mixed
     */
    public function __get($attrName)
    {
        return isset($this->attributes[$attrName]) ? $this->attributes[$attrName] : null;
    }
}
