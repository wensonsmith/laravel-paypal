<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

trait Identity
{
    /**
     * @return mixed
     */
    public function userinfo()
    {
        $this->apiEndPoint = "v1/identity/oauth2/userinfo?schema=paypalv1.1";
        $this->apiUrl = collect([$this->config['api_url'], $this->apiEndPoint])->implode('/');

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }
}
