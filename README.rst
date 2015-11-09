===============================
Lipisha Payments PHP SDK
===============================


This package provides bindings for the Lipisha Payments API (http://developer.lipisha.com/)

* Free software: MIT license
* Documentation: http://developer.lipisha.com

Features
--------

* Send money
* Acknowledge transactions
* Send SMS
* Get Float
* Get Balance
* Charge card transactions
* Search transactions
* Search customers
* Add users
* Add payment accounts and withdrawal accounts

Installation
------------

This package can be installed using composer

    composer require lipisha/lipisha-sdk
    
Or added to your composer dependencies:

.. code-block:: json

    {
        "require": {
            "lipisha/lipisha-sdk": "^0.0.0"
        },
    }


Examples
--------

IPN callback examples are in the examples directory:

https://github.com/lipisha/lipisha-php-sdk/tree/master/examples

Quick start
-----------

.. code-block:: php

    $lipisha = new \Lipisha\Lipisha("<YOUR API KEY>", "<YOUR API SIGNATURE>", "LIVE");
    // To connect to the sandbox, pass the environment asm ``TEST`` instead.
    
    // Get balance
    $response = $lipisha.getBalance();
    print_r($response)
    $balance = $response->content["balance"];

    // Send money
    $payout_account = "033111";
    $response = $lipisha->send_money($payout_account, "0722123456", 500);
    $status = $response->status; //SUCCCESS or FAIL

    // Acknowledge a transaction
    $response = $lipisha->confirm_transaction("TX98089890");
    $status = $response->status; //SUCCESS or FAIL
    $content = $response->content; //Transaction details

Running Tests
-------------

Running tests requires php unit and setting up environment variables for authentication:

First, run composer to install dependencies:

.. code-block:: shell

    composer install

Then set up environmental variables for authentication:

.. code-block:: shell

    export LIPISHA_API_KEY="<YOUR LIPISHA API KEY>"
    export LIPISHA_API_SIGNATURE="<YOUR LIPISHA API SIGNATURE>"

Run the tests:

.. code-block:: shell

    phpunit tests/LipishaTest.php

Majority of tests require setting up parameters in the test suite.

See class documentation for detailed API.
Refer to Lipisha API for parameters required for each method.
