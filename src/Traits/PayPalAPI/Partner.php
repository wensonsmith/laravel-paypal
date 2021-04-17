<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

trait Partner
{
    /**
     * Set partner require header parameters
     *
     * @param  string  $merchantId
     *
     * @see https://developer.paypal.com/docs/api/reference/api-requests/#paypal-auth-assertion
     */
    public function actingAs(string $merchantId)
    {
        $assertion = '{"iss" : "'.$this->config['client_id'].'","payer_id":"'.$merchantId.'"}';
        $this->options['headers']['PayPal-Auth-Assertion'] = base64_encode('{"alg":"none"}').".".base64_encode($assertion).".";
    }


    /**
     * Create partner referral url
     *
     * @param  array  $data
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/partner-referrals/v2/
     */
    public function partnerReferrals(array $data)
    {
        $this->apiEndPoint = 'v2/customer/partner-referrals';
        $this->apiUrl = collect([$this->config['api_url'], $this->apiEndPoint])->implode('/');

        $this->options['json'] = (object)$data;

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
