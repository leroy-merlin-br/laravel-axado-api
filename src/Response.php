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
     * Error Id sent by Axado.
     *
     * @var int
     */
    protected $errorId;

    /**
     * Error message sent by Axado.
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Array of Axado\Quotation.
     *
     * @var array
     */
    protected $quotations = [];

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

        foreach ($quotationsArray as $quotationData) {
            $quotation = new Quotation();
            $quotation->fill($quotationData);
            $this->quotations[] = $quotation;
        }
    }
}
