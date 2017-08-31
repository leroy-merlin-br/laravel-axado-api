<?php
namespace Axado;

use Axado\Exception\DestinationNotFoundException;
use Axado\Exception\OriginNotFoundException;
use Axado\Exception\ShippingException;

class Response
{
    /**
     * Relevant errors from Axado API that are mapped to exceptions.
     *
     * @var array
     */
    private static $translatedErrors = [
        101 => OriginNotFoundException::class,
        102 => DestinationNotFoundException::class,
    ];

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
     * Parse the raw response to this object.
     *
     * @param array|null $raw
     */
    public function parse($raw = null)
    {
        $arrayResponse = (array) $raw;
        $this->checkForErrors($arrayResponse);

        $this->parseQuotations($arrayResponse);
    }

    /**
     * Verify if this Response has an error.
     *
     * @throws ShippingException
     *
     * @param array $arrayResponse
     */
    protected function checkForErrors($arrayResponse)
    {
        if ($errorId = $arrayResponse['erro_id'] ?? null) {
            $exceptionClass = static::$translatedErrors[$errorId] ?? ShippingException::class;

            throw new $exceptionClass($arrayResponse['erro_msg'], $errorId);
        }
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
