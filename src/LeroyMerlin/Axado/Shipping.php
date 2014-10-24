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
     * Quotation used.
     *
     * @var Axado\Quotation
     */
    protected $quotationElected;

    /**
     * Quotation string.
     * @var string
     */
    protected $quotation_token;

    /**
     * Requires fields in Shipping.
     * @var array
     */
    public static $requiredFields = [
        'preco_adicional',
        'cep_origem',
        'cep_destino',
        'valor_notafiscal',
        'prazo_adicional'
    ];

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
     * @var Axado\Response
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Axado\Formatter\FormatterInterface $formatter
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
     * @return array
     */
    public function quotations()
    {
        if (! $this->isValid()) {
            throw new ShippingException("This shipping was not filled correctly", 1);
        }

        if (! $this->response) {
            $request               = $this->newRequest(static::$token);
            $this->response        = $request->consultShipping($this->toJson());
            $this->quotation_token = $this->response->getQuotationToken();
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
            return $quotation->getCosts();
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
            return $quotation->getDeadline();
        }

        return null;
    }

    /**
     * Marking this shipping quotation as contracted to Axado API.
     *
     * @return null
     */
    public function flagAsContracted()
    {
        $request = $this->newRequest(static::$token);
        $token   = $this->quotation_token;

        $request->flagAsContracted($this, $token);
    }

    /**
     * Getter for quotation elected.
     *
     * @return Axado\Quotation
     */
    public function getQuotationElected()
    {
        return $this->quotationElected;
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
        $this->attributes["cep_origem"] = (string)$cep;
    }

    /**
     * Setter to Postal Code destination.
     *
     * @param strin $cep
     */
    public function setPostalCodeDestination($cep)
    {
        $this->attributes["cep_destino"] = (string)$cep;
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
     * @param float $price
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
     * @return Request
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
    public function firstQuotation()
    {
        $quotations = (array)$this->quotations();

        if (isset($quotations[0])) {
            $this->quotationElected = $quotations[0];
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

    /**
     * Verify is this instance is Valid.
     *
     * @return boolean
     */
    protected function isValid()
    {
        foreach (static::$requiredFields as $field) {
            if (! isset($this->attributes[$field]) || ! $this->attributes[$field] ) {
                return false;
            }
        }

        if (! $this->volumes) {
            return false;
        }

        return true;
    }
}
