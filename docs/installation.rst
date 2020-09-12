============
Installation
============

* Use following command to install:

```
composer require srmklive/paypal:~2.0|~3.0
```

If you wish to use PayPal Express Checkout API, please use the following command:

```
composer require srmklive/paypal:~1.0
```

Perform the following steps if you are using Laravel 5.4 or less.

* Add the service provider to your `providers[]` array in `config/app.php` file like:

```
Srmklive\PayPal\Providers\PayPalServiceProvider::class
```

* Add the alias to your `aliases[]` array in `config/app.php` file like:

```
'PayPal' => Srmklive\PayPal\Facades\PayPal::class
```

* Run the following command to publish configuration:

```
php artisan vendor:publish --provider "Srmklive\PayPal\Providers\PayPalServiceProvider"
```

## Configuration

* After installation, you will need to add your paypal settings. Following is the code you will find in **config/paypal.php**, which you should update accordingly.


```
return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.

    'sandbox' => [
        'client_id'         => env('PAYPAL_SANDBOX_CLIENT_ID', ''),

        'client_secret'     => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),

        'app_id'            => 'APP-80W284485P519543T',
    ],

    'live' => [
        'client_id'         => env('PAYPAL_LIVE_CLIENT_ID', ''),

        'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET', ''),

        'app_id'            => '',
    ],

    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'), // Can only be 'Sale', 'Authorization' or 'Order'

    'currency'       => env('PAYPAL_CURRENCY', 'USD'),

    'notify_url'     => env('PAYPAL_NOTIFY_URL', ''), // Change this accordingly for your application.

    'locale'         => env('PAYPAL_LOCALE', 'en_US'), // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)

    'validate_ssl'   => env('PAYPAL_VALIDATE_SSL', true), // Validate SSL when creating api client.

];

```


* Add the following configuration to `.env`

    * Sandbox

        * `PAYPAL_SANDBOX_CLIENT_ID`
        * `PAYPAL_SANDBOX_CLIENT_SECRET`

    * Live

        * `PAYPAL_LIVE_CLIENT_ID`
        * `PAYPAL_LIVE_CLIENT_SECRET`
