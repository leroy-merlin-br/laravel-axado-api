<?php
namespace Axado;

class Response
{
    /**
     * If the request is ok.
     * @var boolean
     */
    protected $isOk;

    /**
     * Error id sended by Axado.
     * @var boolean
     */
    protected $errorId;

    /**
     * The message sended by Axado.
     * @var boolean
     */
    protected $errorMessage;

    /**
     * Array of Axado\Quotation.
     * @var array
     */
    protected $quotations = [];

    /**
     * Getter for quotations.
     * @return array
     */
    public function quotations()
    {
        return $this->quotations;
    }

    /**
     * Parse the response to this object.
     * @param  string $raw
     * @return null
     */
    public function parse($raw = null)
    {
        $arrayResponse = json_decode($raw);

        if (! $this->isError($arrayResponse)) {
            $this->parseQuotations($arrayResponse);
            $this->isOk = true;
        } else {
            $this->isOk = false;
        }
    }

    /**
     * Parse the response into Quotation objects
     * @param  array $arrayResponse
     * @return null
     */
    public function parseQuotations($arrayResponse)
    {
        $quotationsArray = isset($arrayResponse['cotacoes']) ? $arrayResponse['cotacoes'] : [];

        foreach ($quotationsArray as $quotationArray) {
            $quotation = new Quotation;
            $quotation->fill($quotationArray);
            $this->quotations[] = $quotation;
        }
    }

    /**
     * Verify if this Response has a error.
     *
     * @param  array  $arrayResponse
     * @return boolean
     */
    public function isError($arrayResponse)
    {
        if (! $arrayResponse || isset($arrayResponse['erro_id'])) {
            $this->errorId      = $arrayResponse['erro_id'];
            $this->errorMessage = $arrayResponse['erro_msg'];

            return true;
        }

        return false;
    }

    /**
     * Returns if the response was Ok.
     * @return boolean
     */
    public function isOk()
    {
        return (boolean)$this->isOk;
    }
}
