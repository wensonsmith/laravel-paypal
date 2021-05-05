<?php

namespace Srmklive\PayPal\Traits;

trait PayPalAPI
{
    use PayPalAPI\Trackers;
    use PayPalAPI\CatalogProducts;
    use PayPalAPI\Disputes;
    use PayPalAPI\DisputesActions;
    use PayPalAPI\Invoices;
    use PayPalAPI\InvoicesSearch;
    use PayPalAPI\InvoicesTemplates;
    use PayPalAPI\Orders;
    use PayPalAPI\PaymentAuthorizations;
    use PayPalAPI\PaymentCaptures;
    use PayPalAPI\PaymentRefunds;
    use PayPalAPI\BillingPlans;
    use PayPalAPI\Subscriptions;
    use PayPalAPI\Reporting;
    use PayPalAPI\WebHooks;
    use PayPalAPI\WebHooksVerification;
    use PayPalAPI\WebHooksEvents;
    use PayPalAPI\Partner;
    use PayPalAPI\Identity;

    /**
     * Login through PayPal API to get access token.
     *
     * @param  srting|null  $code
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @throws \Throwable
     *
     * @see https://developer.paypal.com/docs/api/get-an-access-token-curl/
     * @see https://developer.paypal.com/docs/api/get-an-access-token-postman/
     */
    public function getAccessToken($code = null)
    {
        $this->apiEndPoint = 'v1/oauth2/token';
        $this->apiUrl = collect([$this->config['api_url'], $this->apiEndPoint])->implode('/');

        $this->options['auth'] = [$this->config['client_id'], $this->config['client_secret']];

        if ($code !== null) {
            $this->options[$this->httpBodyParam] = [
                'grant_type' => 'authorization_code',
                'code' => $code
            ];
        } else {
            $this->options[$this->httpBodyParam] = [
                'grant_type' => 'client_credentials',
            ];
        }

        $response = $this->doPayPalRequest();

        $this->setAccessToken($response);

        return $response;
    }

    /**
     * Refresh access token by refresh token
     *
     * @param $refreshToken
     * @return array|\Psr\Http\Message\StreamInterface|string
     */
    public function refreshAccessToken(string $refreshToken)
    {
        $this->apiEndPoint = 'v1/oauth2/token';
        $this->apiUrl = collect([$this->config['api_url'], $this->apiEndPoint])->implode('/');

        $this->options['auth'] = [$this->config['client_id'], $this->config['client_secret']];

        $this->options[$this->httpBodyParam] = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ];

        $response = $this->doPayPalRequest();

        $this->setAccessToken($response);

        return $response;
    }

    /**
     * Set PayPal Rest API access token.
     *
     * @param  array  $response
     *
     * @return void
     */
    public function setAccessToken(array $response)
    {
        if (isset($response['access_token'])) {
            $this->access_token = $response['access_token'];

            $this->options['auth'] = [];
            $this->options['headers']['Authorization'] = "{$response['token_type']} {$this->access_token}";

            $this->setPayPalAppId($response);
        }
    }

    /**
     * Set PayPal App ID.
     *
     * @param  array  $response
     *
     * @return void
     */
    private function setPayPalAppId($response)
    {
        if (empty($this->config['app_id']) && isset($response['app_id'])) {
            $this->config['app_id'] = $response['app_id'];
        }
    }
}
