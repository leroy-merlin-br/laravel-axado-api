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
     * Return this object in json format.
     *
     * @return string
     */
    public function toJson()
    {
        $this->formatter->setInstance($this);

        return $this->formatter->format();
    }

    /**
     * Consult this shipping through api.
     *
     * @return boolean
     */
    public function quotations()
    {
        if ($this->response) {
            return $this->response->quotations();
        } else {
            $this->response = $this->request->consultShipping($this->toJson());
        }

        return $this->response;
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
}
