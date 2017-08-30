<?php
namespace Axado;

class Request
{
    /**
     * The de API url.
     *
     * @var string
     */
    protected static $consultURL = 'http://api.axado.com.br/v2/consulta/?token=';

    /**
     * The de API url.
     *
     * @var string
     */
    protected static $quotationURL = 'http://api.axado.com.br/v2/cotacao/';

    /**
     * Token for consult quotations.
     *
     * @var string
     */
    protected $token;

    /**
     * Constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Runs the request to Axado API and return a Response Object.
     *
     * @param string $jsonString
     *
     * @return Response
     */
    public function consultShipping($jsonString): Response
    {
        $raw = $this->doRequest(
            'POST',
            static::$consultURL . $this->token,
            $jsonString
        );

        return $this->createResponse($raw);
    }

    /**
     * Request to Axado API.
     *
     * @codeCoverageIgnore
     *
     * @param string $method
     * @param string $path
     * @param string $data
     *
     * @return array
     */
    protected function doRequest(string $method, string $path, string $data): array
    {
        $conn = curl_init();

        curl_setopt_array(
            $conn,
            [
                CURLOPT_URL => $path,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_POSTFIELDS => $data,
            ]
        );

        $response = curl_exec($conn);
        $data = null;

        if (false !== $response) {
            $data = json_decode($response, true);
        }

        curl_close($conn);

        return $data ?? [];
    }

    /**
     * Return the Response instance.
     *
     * @param  string $raw
     *
     * @return Response
     */
    protected function createResponse($raw): Response
    {
        $response = new Response();
        $response->parse($raw);

        return $response;
    }
}
