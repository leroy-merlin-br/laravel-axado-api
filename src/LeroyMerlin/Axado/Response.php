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
     * Array of Axado\Quotation.
     * @var array
     */
    protected $quotations;

    /**
     * Parse the response to this object.
     * @param  string $raw
     * @return null
     */
    public function parse($raw = null)
    {
        $this->isOk = ! is_null($raw);
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
