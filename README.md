# Nilfraud PHP Library
Official PHP library to interact with the Nilfraud API. This library still supports PHP 7.4, due to some compatibility we need in the WHMCS module.

## Requirements
- PHP 7.4 or newer
- cURL extension
- JSON extension
- OpenSSL extension

## Installation
You may install it using Composer:
```
composer require nilfraud/php-library
```

## Examples
```php
<?php

require __DIR__.'/vendor/autoload.php';

use Nilfraud\API;
use Nilfraud\Helpers;

// Make API calls;
$client = new Client();
$client->setAuthCredentials('apiKey', 'apiToken');
$apiCall = $client->apiCall('GET', 'reports/hash');
$apiCall = $client->apiCall('POST', 'reports', ['param' => 'value']);
var_dump($apiCall);

// Generate hashes;
Hash::generate('email@example.com');
Hash::generateBulk(['email@example.com', 'email2@example.com']);

```

or simply load the class directly if you are not using Composer:
```php
require __DIR__.'/src/Nilfraud/API/Client.php';
$client = new \Nilfraud\API\Client();
```

## Contribute
Contributions are welcome in a form of a pull request (PR).

## License
Mozilla Public License Version 2.0
