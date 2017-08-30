<?php
namespace Axado;

use Axado\Exception\QuotationNotFoundException;
use Axado\Exception\ShippingException;
use Axado\Formatter\FormatterInterface;
use Axado\Formatter\JsonFormatter;
use Axado\Volume\VolumeInterface;

class Shipping
{
    /**
     * Requires fields in Shipping.
     *
     * @var array
     */
    public static $requiredFields = [
        'cep_origem',
        'cep_destino',
        'valor_notafiscal',
    ];

    /**
     * Token string to Axado.
     *
     * @var string
     */
    public static $token;

    /**
     * All attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Quotation used.
     *
     * @var Quotation
     */
    protected $electedQuotation;

    /**
     * Quotation string.
     *
     * @var string
     */
    protected $quotationToken;

    /**
     * All volumes objects.
     *
     * @var array
     */
    protected $volumes = [];

    /**
     * FormatterInterface instance.
     *
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * Axado\Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Axado\Response instance.
     *
     * @var Response
     */
    protected $response;

    /**
     * Constructor
     *
     * @param \Axado\Formatter\FormatterInterface $formatter
     */
    public function __construct(FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?: new JsonFormatter();
    }

    /**
     * Get the first quotation and return the price.
     *
     * @return string|null
     */
    public function getCosts()
    {
        return $this->firstQuotation()->getCosts();
    }

    /**
     * Returns the first quotation.
     *
     * @throws QuotationNotFoundException
     *
     * @return Quotation
     */
    public function firstQuotation(): Quotation
    {
        $quotations = (array) $this->quotations();

        if (! isset($quotations[0])) {
            throw new QuotationNotFoundException(
                sprintf(
                    'No quotations were found to the given CEP: %s',
                    $this->getPostalCodeDestination()
                )
            );
        }

        $this->electedQuotation = $quotations[0];

        return $quotations[0];
    }

    /**
     * Consult this shipping through api.
     *
     * @throws ShippingException
     *
     * @return array
     */
    public function quotations()
    {
        if (! $this->isValid()) {
            throw new ShippingException(
                'This shipping was not filled correctly',
                1
            );
        }

        if (! $this->response) {
            $request = $this->newRequest(static::$token);
            $this->response = $request->consultShipping($this->toJson());
            $this->quotationToken = $this->response->getQuotationToken();
        }

        return $this->response->quotations();
    }

    /**
     * Verify is this instance is Valid.
     *
     * @return bool
     */
    protected function isValid(): bool
    {
        foreach (static::$requiredFields as $field) {
            if (! isset($this->attributes[$field]) || ! $this->attributes[$field]) {
                return false;
            }
        }

        return (bool) $this->volumes;
    }

    /**
     * Returns a new instance of Request.
     *
     * @param string $token
     *
     * @return Request
     */
    protected function newRequest(string $token): Request
    {
        return new Request($token);
    }

    /**
     * Return this object in json format.
     *
     * @return string
     */
    protected function toJson(): string
    {
        $this->formatter->setInstance($this);

        return $this->formatter->format();
    }

    /**
     * Getter of postal code destination
     *
     * @return string|null
     */
    protected function getPostalCodeDestination()
    {
        return $this->attributes['cep_destino'] ?? null;
    }

    /**
     * Get the first quotation and return the price.
     *
     * @return string|null
     */
    public function getDeadline()
    {
        return $this->firstQuotation()->getDeadline();
    }

    /**
     * Getter for quotation elected.
     *
     * @return Quotation
     */
    public function getElectedQuotation()
    {
        return $this->electedQuotation;
    }

    /**
     * Return the attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Setter to Postal Code origin.
     *
     * @param string $cep
     */
    public function setPostalCodeOrigin($cep)
    {
        $this->attributes['cep_origem'] = (string) $cep;
    }

    /**
     * Setter to Postal Code destination.
     *
     * @param string $cep
     */
    public function setPostalCodeDestination($cep)
    {
        $this->attributes['cep_destino'] = (string) $cep;
    }

    /**
     * Setter to Total price of sale.
     *
     * @param float $price
     */
    public function setTotalPrice($price)
    {
        $this->attributes['valor_notafiscal'] = (float) $price;

        $this->setAdditionalPrice($this->getAdditionalPrice());
    }

    /**
     * Setter to Additional price to add to shipping costs.
     *
     * @param float $price
     */
    public function setAdditionalPrice($price)
    {
        $additionalPrice = $this->calculateAdditionalPrice($price);

        $this->attributes['preco_adicional'] = $additionalPrice;
    }

    /**
     * Calculate the additional price.
     *
     * @param  string $price
     *
     * @return float
     */
    public function calculateAdditionalPrice($price)
    {
        $tempPrice = $price;
        $totalPrice = $this->getTotalPrice();

        if (preg_match('/%/', $price) && (float) $price && $totalPrice) {
            if ($price = (float) $price) {
                $price = $totalPrice * $price / 100;
            } else {
                $price = $tempPrice;
            }
        } else {
            $price = $tempPrice;
        }

        return $price;
    }

    /**
     * Getter to Total price of sale.
     *
     * @return float|null
     */
    public function getTotalPrice()
    {
        return $this->attributes['valor_notafiscal'] ?? null;
    }

    /**
     * Getter to of Additional price.
     *
     * @return float|null
     */
    public function getAdditionalPrice()
    {
        return $this->attributes['preco_adicional'] ?? null;
    }

    /**
     * Setter to additional days.
     *
     * @param int $days
     */
    public function setAdditionalDays($days)
    {
        $this->attributes['prazo_adicional'] = (int) $days;
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
    public function allVolumes(): array
    {
        return $this->volumes;
    }

    /**
     * Clean all volumes.
     */
    public function clearVolumes()
    {
        $this->volumes = [];
    }
}
