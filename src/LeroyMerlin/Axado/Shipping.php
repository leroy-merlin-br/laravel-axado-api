<?php
namespace Axado;

use Axado\Formatter\JsonFormatter;
use Axado\Volume\VolumeInterface;
use Axado\Formatter\FormatterInterface;

class Shipping
{
    /**
     * All attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Token string to Axado.
     *
     * @var string
     */
    public static $token;

    /**
     * All volumes objects.
     *
     * @var array
     */
    protected $volumes = [];

    /**
     * Axado\Formatter instance.
     *
     * @var Axado\Formatter
     */
    protected $formatter;

    /**
     * Axado\Request instance.
     *
     * @var Axado\Request
     */
    protected $request;

    /**
     * Axado\Response instance.
     *
     * @var Axado\Request
     */
    protected $response;

    /**
     * Constructor
     *
     * @param Request $request
     */
    public function __construct(FormatterInterface $formatter = null)
    {
        if ($formatter) {
            $this->formatter = $formatter;
        } else {
            $this->formatter = new JsonFormatter;
        }
    }

    /**
     * Consult this shipping through api.
     *
     * @return boolean
     */
    public function quotations()
    {
        if (! $this->response) {
            $request = $this->newRequest(static::$token);
            $this->response = $request->consultShipping($this->toJson());
        }

        return $this->response->quotations();
    }

    /**
     * Get the first quotation and return the price.
     *
     * @return integer
     */
    public function getCosts()
    {
        $quotation = $this->firstQuotation();

        if ($quotation) {
            return $quotation->quotation_price;
        }

        return null;
    }

    /**
     * Get the first quotation and return the price.
     *
     * @return integer
     */
    public function getDeadline()
    {
        $quotation = $this->firstQuotation();

        if ($quotation) {
            return $quotation->deadline;
        }

        return null;
    }

    /**
     * Return the attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Setter to Postal Code origin.
     *
     * @param strin $cep
     */
    public function setPostalCodeOrigin($cep)
    {
        $this->attributes["cep_origem"] = $cep;
    }

    /**
     * Setter to Postal Code destination.
     *
     * @param strin $cep
     */
    public function setPostalCodeDestination($cep)
    {
        $this->attributes["cep_destino"] = $cep;
    }

    /**
     * Setter to Total price of sale.
     *
     * @param float $price
     */
    public function setTotalPrice($price)
    {
        $this->attributes["valor_notafiscal"] = (float)$price;
    }

    /**
     * Setter to additional days.
     *
     * @param int $days
     */
    public function setAditionalDays($days)
    {
        $this->attributes["prazo_adicional"] = (int)$days;
    }

    /**
     * Setter to Additional price to add to shipping costs.
     *
     * @param float $cep
     */
    public function setAditionalPrice($price)
    {
        $this->attributes["preco_adicional"] = (float)$price;
    }

    /**
     * Add a volume object to send through Axado api.
     *
     * @param VolumeInterface $volume
     */
    public function addVolume(VolumeInterface $volume)
    {
        $this->volumes[] = $volume;
    }

    /**
     * Return all volumes at this instance.
     *
     * @return array
     */
    public function allVolumes()
    {
        return $this->volumes;
    }

    /**
     * Clean all volumes.
     *
     * @return null
     */
    public function clearVolumes()
    {
        $this->volumes = [];
    }

    /**
     * Returns a new instance of Request.
     *
     * @param  string $token
     * @return Axado\Request
     */
    protected function newRequest($token)
    {
        return new Request($token);
    }

    /**
     * Returns the first quotation.
     *
     * @return Quotation
     */
    protected function firstQuotation()
    {
        $quotations = (array)$this->quotations();

        if (isset($quotations[0])) {
            return $quotations[0];
        }

        return null;
    }

    /**
     * Return this object in json format.
     *
     * @return string
     */
    protected function toJson()
    {
        $this->formatter->setInstance($this);

        return $this->formatter->format();
    }
}
