<?php

namespace Srmklive\PayPal\Traits;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as HttpClientException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

trait PayPalHttpClient
{
    /**
     * Http Client class object.
     *
     * @var HttpClient
     */
    private $client;

    /**
     * Http Client configuration.
     *
     * @var array
     */
    private $httpClientConfig;

    /**
     * PayPal API Endpoint.
     *
     * @var string
     */
    private $apiUrl;

    /**
     * PayPal API Endpoint.
     *
     * @var string
     */
    private $apiEndPoint;

    /**
     * IPN notification url for PayPal.
     *
     * @var string
     */
    private $notifyUrl;

    /**
     * Http Client request body parameter name.
     *
     * @var string
     */
    private $httpBodyParam;

    /**
     * Default payment action for PayPal.
     *
     * @var string
     */
    private $paymentAction;

    /**
     * Default locale for PayPal.
     *
     * @var string
     */
    private $locale;

    /**
     * Validate SSL details when creating HTTP client.
     *
     * @var bool
     */
    private $validateSSL;

    /**
     * Request type.
     *
     * @var string
     */
    protected $verb = 'post';

    /**
     * Set curl constants if not defined.
     *
     * @return void
     */
    protected function setCurlConstants()
    {
        $constants = [
            'CURLOPT_SSLVERSION' => 32,
            'CURL_SSLVERSION_TLSv1_2' => 6,
            'CURLOPT_SSL_VERIFYPEER' => 64,
            'CURLOPT_SSLCERT' => 10025,
        ];

        foreach ($constants as $key => $value) {
            if (!defined($key)) {
                define($key, $constants[$key]);
            }
        }
    }

    /**
     * Function to initialize/override Http Client.
     *
     * @param  \GuzzleHttp\Client|null  $client
     *
     * @return void
     */
    public function setClient($client = null)
    {
        if ($client instanceof HttpClient) {
            $this->client = $client;

            return;
        }

        $this->client = new HttpClient([
            'curl' => $this->httpClientConfig,
        ]);
    }

    /**
     * Function to set Http Client configuration.
     *
     * @return void
     */
    protected function setHttpClientConfiguration()
    {
        $this->setCurlConstants();

        $this->httpClientConfig = [
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_SSL_VERIFYPEER => $this->validateSSL,
        ];

        // Initialize Http Client
        $this->setClient();

        // Set default values.
        $this->setDefaultValues();

        // Set PayPal IPN Notification URL
        $this->notifyUrl = $this->config['notify_url'];
    }

    /**
     * Set default values for configuration.
     *
     * @return void
     */
    private function setDefaultValues()
    {
        $paymentAction = empty($this->paymentAction) ? 'Sale' : $this->paymentAction;
        $this->paymentAction = $paymentAction;

        $locale = empty($this->locale) ? 'en_US' : $this->locale;
        $this->locale = $locale;

        $validateSSL = empty($validateSSL) ? true : $this->validateSSL;
        $this->validateSSL = $validateSSL;
    }

    /**
     * Perform PayPal API request & return response.
     *
     * @return StreamInterface
     * @throws \Throwable
     *
     */
    private function makeHttpRequest()
    {
        try {
            return $this->client->{$this->verb}(
                $this->apiUrl,
                $this->options
            )->getBody();
        } catch (HttpClientException $exception) {
            throw new RuntimeException($exception->getResponse()->getBody(), $exception->getCode());
        }
    }

    /**
     * Function To Perform PayPal API Request.
     *
     * @param  bool  $decode
     *
     * @return array|StreamInterface|string
     * @throws \Throwable
     *
     */
    private function doPayPalRequest($decode = true)
    {
        try {
            // Perform PayPal HTTP API request.
            $response = $this->makeHttpRequest();

            return ($decode === false) ? $response->getContents() : \GuzzleHttp\json_decode($response, true);
        } catch (RuntimeException $exception) {
            $code = $exception->getCode();
            $message = \GuzzleHttp\Utils::jsonDecode($exception->getMessage(), true);
        }

        return [
            'type' => 'error',
            'code' => $code,
            'message' => $message,
        ];
    }
}
