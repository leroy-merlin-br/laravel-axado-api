<?php
namespace Axado;

class Quotation
{
    /**
     * Fields with be parsed after the response is returned.
     *
     * @var array
     */
    protected $possibleAttributes = [
        'transportadora_metaname' => 'name',
        'servico_metaname' => 'service_metaname',
        'servico_nome' => 'service_name',
        'cotacao_preco' => 'quotation_price',
        'cotacao_custo' => 'quotation_costs',
        'cotacao_prazo' => 'deadline',
        'cotacao_codigo' => 'quotation_id',
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
     * @param array $rawAttributes
     */
    public function fill(array $rawAttributes = [])
    {
        foreach ($rawAttributes as $key => $value) {
            if (array_key_exists($key, $this->possibleAttributes)) {
                $keyParsed = $this->possibleAttributes[$key];

                $this->attributes[$keyParsed] = $value;
            }
        }
    }

    /**
     * Return all attributes parsed.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Returns the quotation code.
     *
     * @return string|null
     */
    public function getQuotationCode()
    {
        return $this->attributes['quotation_id'] ?? null;
    }

    /**
     * Returns the cost.
     *
     * @return string|null
     */
    public function getCosts()
    {
        return $this->attributes['quotation_price'] ?? null;
    }

    /**
     * Returns the deadline in days
     *
     * @return string|null
     */
    public function getDeadline()
    {
        return $this->attributes['deadline'] ?? null;
    }

    /**
     * Magic attribute getter
     *
     * @param string $attributeName
     *
     * @return mixed
     */
    public function __get(string $attributeName)
    {
        return $this->attributes[$attributeName] ?? null;
    }
}
