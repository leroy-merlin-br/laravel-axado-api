<?php
namespace Axado;

class Response
{
    /**
     * If the request is ok.
     *
     * @var bool
     */
    protected $isOk;

    /**
     * Error_id sent by Axado.
     *
     * @var bool
     */
    protected $errorId;

    /**
     * The error message sent by Axado.
     *
     * @var bool
     */
    protected $errorMessage;

    /**
     * Array of Axado\Quotation.
     *
     * @var array
     */
    protected $quotations = [];

    /**
     * The token sent by Axado.
     *
     * @var string
     */
    protected $quotationToken;

    /**
     * Getter for quotations.
     *
     * @return array
     */
    public function quotations(): array
    {
        return $this->quotations;
    }

    /**
     * Returns if the response was Ok.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return (bool) $this->isOk;
    }

    /**
     * Parse the raw response to this object.
     *
     * @param array|null $raw
     */
    public function parse($raw = null)
    {
        $arrayResponse = (array) $raw;
        $this->isOk = ! $this->isError($arrayResponse);

        if ($this->isOk) {
            $this->parseQuotations($arrayResponse);
        }
    }

    /**
     * Verify if this Response has an error.
     *
     * @param array $arrayResponse
     *
     * @return bool
     */
    protected function isError($arrayResponse): bool
    {
        if (isset($arrayResponse['erro_id'])) {
            $this->errorId = $arrayResponse['erro_id'];
            $this->errorMessage = $arrayResponse['erro_msg'];

            return true;
        }

        return ! $arrayResponse;
    }

    /**
     * Parse the response into Quotation objects.
     *
     * @param array $arrayResponse
     */
    protected function parseQuotations(array $arrayResponse)
    {
        $quotationsArray = $arrayResponse['cotacoes'] ?? [];
        $this->quotationToken = $arrayResponse['consulta_token'] ?? null;

        foreach ($quotationsArray as $quotationArray) {
            $quotation = new Quotation();
            $quotation->fill($quotationArray);
            $this->quotations[] = $quotation;
        }
    }

    /**
     * Returns the quotation token.
     *
     * @return string|null
     */
    public function getQuotationToken()
    {
        return $this->quotationToken;
    }
}
