<?php

namespace Lipisha;

/**
 * Contains the response of a Lipisha API call.
 */
class LipishaResponse {
    // API responses
    public $status;
    public $status_code;
    public $status_description;
    public $content;
    public $json;
    
    /**
     * @param   string  $status Status from api call e.g. "Success"
     * @param   string  $status_code    Numeric status code from api call
     * @param   string  $status_description Status code description (verbose)
     * @param   string  $content  content section of the api call
     * @param   string  $json   JSON Response
     */
    public function __construct($status, $status_code, $status_description, $content, $json) {
        $this->status = $status;
        $this->status_code = $status_code;
        $this->status_description = $status_description;
        $this->content = $content;
        $this->json = $json;
    }
    
}

/**
 * 
 * Object that exposes methods for interacting with the Lipisha API.
 * 
 */
class Lipisha {
    
    // API Endpoint Settings
    private $api_base;
    //private $api_endpoint;
    //private $api_parameters;
    
    // API Environment Settings
    private $api_environment;
    private $api_key;
    private $api_signature;

   
    /**
     * Instantiates Lipisha instance
     * 
     * @param   string  $api_key    Your Lipisha API Key
     * @param   string  $api_signature  Your Lipisha API Signature
     * @param   string  $api_environment Environment to launch either.
     *                                   "live" or "test"
     */
    public function __construct($api_key, $api_signature, $api_environment="live")
    {
        $this->api_environment = strtoupper($api_environment);
        $prod_base = "https://www.lipisha.com/payments/accounts/index.php/v2/api/";
        $sandbox_base = "http://developer.lipisha.com/index.php/v2/api/";
        if ($this->api_environment == "LIVE") {
            $this->api_base = $prod_base;
        } else if ($this->api_environment == "TEST") {
            $this->api_base = $sandbox_base;
        } else {
            throw new Exception("api_evironment must be either `live` or `test` with no quotes");
        }
        $this->api_key = $api_key;
        $this->api_signature = $api_signature;
    }

    /**
     * Calls a Lipisha API endpoint with provided parameters.
     * 
     * @param   String  $api_endpoint    An api endpoint for current operation
     * @param   String  $api_parameters  Parameters for API call
     * @return  LipishaResponse
     */
    function execute($api_endpoint, $api_parameters)
    {        
        $api_url = $this->api_base . $api_endpoint; 
        
        $parameters = ('api_key=' . urlencode($this->api_key) .
                       '&api_signature=' . urlencode($this->api_signature) .
                       $api_parameters);    
        
        // execute post
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
        $results = curl_exec($ch);
        curl_close($ch);
                    
        // process json
        $results = trim($results);
        $response = json_decode($results, TRUE);
        
        $status = urldecode($response["status"]["status"]);
        $status_code = urldecode($response["status"]["status_code"]);
        $status_description = urldecode($response["status"]["status_description"]);
        $content =$response["content"];
        $json = urldecode($results);
        
        return new LipishaResponse($status,
                                   $status_code,
                                   $status_description,
                                   $content,
                                   $json);
    }

    /**
     * Get transactions based on provided filters (All fields optional - see defaults).
     * 
     * @param   string  $transaction    Transaction ids to look up e.g.
     *                                  TX0099000,TX004220 or empty string ""
     * @param   string  $transaction_type Transaction types to look up e.g.
     *                                    Payment,Fees (Separated by commas) or empty string
     * @param   string  $transaction_method Transaction methods to filter against e.g.
     *                                      Paybill (M-Pesa)
     * @param   string  $transaction_date_start First date of transaction in this format
     *                                          yyyy-mm-dd
     * @param   string  $transaction_date_end   Last date of transaction in this format
     *                                          yyyy-mm-dd
     * @param   string  $transaction_account_name   Account name to filter against e.g. Transaction Account
     * @param   string  $transaction_account_number Account number to filter againast e.g. 00155,002444
     * @param   string  $transaction_reference  Transaction referece to filter against e.g. MPJ89003
     * @param   float   $transaction_amount_minimum  Minimum transaction amount
     * @param   float   $transaction_amount_maximum  Maximum transaction amount
     * @param   string  $transaction_status Transaction statuses to filter against e.g. Reversed,Completed
     * @param   string  $transaction_name   Customer name to filter against e.g. "John Doe"
     * @param   string  $transaction_mobile_number  Customer mobile number to filter against e.g. 0722123456
     * @param   string  $transaction_email  Customer email to filter against
     * @param   int     $limit  Number of records to return
     * @param   int     $offset Index of first transaction in response (e.g. 0 start at beginning or 9 - start at 10th record)
     * @return  LipishaResponse
     */
    public function get_transactions($transaction="",
                                     $transaction_type="",
                                     $transaction_method="",
                                     $transaction_date_start="",
                                     $transaction_date_end="",
                                     $transaction_account_name="",
                                     $transaction_account_number="",
                                     $transaction_reference="",
                                     $transaction_amount_minimum="",
                                     $transaction_amount_maximum="",
                                     $transaction_status="",
                                     $transaction_name="",
                                     $transaction_mobile_number="",
                                     $transaction_email="", 
                                     $limit=1000,
                                     $offset=0)
    {
        $api_endpoint = "get_transactions";    
        $api_parameters  = '&transaction=' . urlencode($transaction);
        $api_parameters .= '&transaction_type=' . urlencode($transaction_type);
        $api_parameters .= '&transaction_method=' . urlencode($transaction_method);
        $api_parameters .= '&transaction_date_start=' . urlencode($transaction_date_start);
        $api_parameters .= '&transaction_date_end=' . urlencode($transaction_date_end);
        $api_parameters .= '&transaction_account_name=' . urlencode($transaction_account_name);
        $api_parameters .= '&transaction_account_number=' . urlencode($transaction_account_number);
        $api_parameters .= '&transaction_reference=' . urlencode($transaction_reference);
        $api_parameters .= '&transaction_amount_minimum=' . urlencode($transaction_amount_minimum);
        $api_parameters .= '&transaction_amount_maximum=' . urlencode($transaction_amount_maximum);
        $api_parameters .= '&transaction_status=' . urlencode($transaction_status);
        $api_parameters .= '&transaction_name=' . urlencode($transaction_name);
        $api_parameters .= '&transaction_mobile_number=' . urlencode($transaction_mobile_number);
        $api_parameters .= '&transaction_email=' . urlencode($transaction_email);
        $api_parameters .= '&limit=' . urlencode($limit);
        $api_parameters .= '&offset=' . urlencode($offset);
        return $this->execute($api_endpoint, $api_parameters);
    }


    /**
     * Get customer records (All fields optional)
     * 
     * @param   string  $customer_name
     * @param   string  $customer_mobile_phone
     * @param   string  $customer_email
     * @param   string  $customer_first_payment_from 
     * @param   string  $customer_first_payment_to
     * @param   string  $customer_last_payment_from 
     * @param   string  $customer_last_payment_to
     * @param   string  $customer_payments_minimum
     * @param   string  $customer_payments_maximum
     * @param   float   $customer_total_spent_minimum
     * @param   float   $customer_total_spent_maximum
     * @param   string  $customer_average_spent_minimum
     * @param   string  $customer_average_spent_maximum
     * @param   int     $limit  Number of records to return
     * @param   int     $offset Start index in response records (0-first record)
     * @return  LipishaResponse
     */
    public function get_customers($customer_name="",
                                  $customer_mobile_number="",
                                  $customer_email="",
                                  $customer_first_payment_from="",
                                  $customer_first_payment_to="",
                                  $customer_last_payment_from="",
                                  $customer_last_payment_to="",
                                  $customer_payments_minimum="",
                                  $customer_payments_maximum="",
                                  $customer_total_spent_minimum="",
                                  $customer_total_spent_maximum="",
                                  $customer_average_spent_minimum="",
                                  $customer_average_spent_maximum="",
                                  $limit=1000,
                                  $offset=0)
    {    
        $api_endpoint = "get_customers";    
        $api_parameters  = '&customer_name=' . urlencode($customer_name);
        $api_parameters .= '&customer_mobile_number=' . urlencode($customer_mobile_number);
        $api_parameters .= '&customer_email=' . urlencode($customer_email);
        $api_parameters .= '&customer_first_payment_from=' . urlencode($customer_first_payment_from);
        $api_parameters .= '&customer_first_payment_to=' . urlencode($customer_first_payment_to);
        $api_parameters .= '&customer_last_payment_from=' . urlencode($customer_last_payment_from);
        $api_parameters .= '&customer_last_payment_to=' . urlencode($customer_last_payment_to);
        $api_parameters .= '&customer_payments_minimum=' . urlencode($customer_payments_minimum);
        $api_parameters .= '&customer_payments_maximum=' . urlencode($customer_payments_maximum);
        $api_parameters .= '&customer_total_spent_minimum=' . urlencode($customer_total_spent_minimum);
        $api_parameters .= '&customer_total_spent_maximum=' . urlencode($customer_total_spent_maximum);
        $api_parameters .= '&customer_average_spent_minimum=' . urlencode($customer_average_spent_minimum);
        $api_parameters .= '&customer_average_spent_maximum=' . urlencode($customer_average_spent_maximum);
        $api_parameters .= '&limit=' . urlencode($limit);
        $api_parameters .= '&offset=' . urlencode($offset);
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Creates a payment account
     * 
     * @param   int     $transaction_account_type   e.g. 1 for Mpesa/Airtel Money
     * @param   string  $transaction_account_name   Name for this account
     * @param   string  $transaction_account_manager    Login id of the account managing this new account
     * @return  LipishaResponse
     */
    public function create_payment_account($transaction_account_type,
                                           $transaction_account_name,
                                           $transaction_account_manager)
    {
        $api_endpoint = "create_payment_account";    
        $api_parameters  = '&transaction_account_type=' . urlencode($transaction_account_type);
        $api_parameters .= '&transaction_account_name=' . urlencode($transaction_account_name);
        $api_parameters .= '&transaction_account_manager=' . urlencode($transaction_account_manager);
        return $this->execute($api_endpoint, $api_parameters);
    }


    /**
     * Creates a user with a specific role.
     * 
     * @param   string  $full_name
     * @param   string  $role       Role for the new user. Role must exist in Lipisha account.
     * @param   string  $email
     * @param   string  $user_name
     * @param   string  $password
     * @return  LipishaResponse
     */
    public function create_user($full_name, $role, $mobile_number, $email, $user_name, $password)
    {
        $api_endpoint = "create_user";    
        $api_parameters  = '&full_name=' . urlencode($full_name);
        $api_parameters .= '&role=' . urlencode($role);
        $api_parameters .= '&mobile_number=' . urlencode($mobile_number);
        $api_parameters .= '&email=' . urlencode($email);
        $api_parameters .= '&user_name=' . urlencode($user_name);
        $api_parameters .= '&password=' . urlencode($password);
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Creates a withdrawal account (Bank account used for settlement).
     * @param   int     $transaction_account_type   e.g. 1 for bank account
     * @param   string  $transaction_account_name
     * @param   string  $transaction_account_number Full bank account number
     * @param   string  $transaction_account_bank_name
     * @param   string  $transaction_account_bank_branch
     * @param   string  $transaction_account_bank_address
     * @param   string  $transaction_account_swift_code
     * @param   string  $transaction_account_manager
     * @return  LipishaResponse
     */
    public function create_withdrawal_account($transaction_account_type,
                                              $transaction_account_name,
                                              $transaction_account_number,
                                              $transaction_account_bank_name, 
                                              $transaction_account_bank_branch,
                                              $transaction_account_bank_address,
                                              $transaction_account_swift_code,
                                              $transaction_account_manager)
    {
        $api_endpoint = "create_withdrawal_account";    
        $api_parameters  = '&transaction_account_type=' . urlencode($transaction_account_type);
        $api_parameters .= '&transaction_account_name=' . urlencode($transaction_account_name);
        $api_parameters .= '&transaction_account_number=' . urlencode($transaction_account_number);
        $api_parameters .= '&transaction_account_bank_name=' . urlencode($transaction_account_bank_name);
        $api_parameters .= '&transaction_account_bank_branch=' . urlencode($transaction_account_bank_branch);
        $api_parameters .= '&transaction_account_bank_address=' . urlencode($transaction_account_bank_address);
        $api_parameters .= '&transaction_account_swift_code=' . urlencode($transaction_account_swift_code);
        $api_parameters .= '&transaction_account_manager=' . urlencode($transaction_account_manager);
        return $this->execute($api_endpoint, $api_parameters);
    }


    /**
     * Acknowleges a transaction. This confirms a payment as received.
     * 
     * @param   string  $transaction    Transaction id to acknowledge e.g. TX099090
     *                                  Or comma separated list of transactions e.g.
     *                                  "TX099090,TX099091"
     * @return  LipishaResponse
     */
    public function confirm_transaction($transaction)
    {
        $api_endpoint = "confirm_transaction";    
        $api_parameters = '&transaction=' . urlencode($transaction);
        return $this->execute($api_endpoint, $api_parameters);
    }
    
    /**
     * @see Lipisha::confirm_transaction
     */
    public function acknowledge_transaction($transaction) {
       return $this->confirm_transaction($transaction);
    }

    /**
     * Reverses a transaction(s). This confirms a payment as received.
     * 
     * @param   string  $transaction    Transaction id to reverse e.g. TX099090
     *                                  Or comma separated list of transactions e.g.
     *                                  "TX099090,TX099091"
     * @return  LipishaResponse
     */
    public function reverse_transaction($transaction)
    {
        $api_endpoint = "reverse_transaction";    
        $api_parameters = '&transaction=' . urlencode($transaction);
        return $this->execute($api_endpoint, $api_parameters);
    }
    
    /**
     * Sends money to provided mobile number from specified account
     * 
     * @param   string  $account_number Payout account from which to send money
     * @param   string  @mobile_number  Mobile number to send money to e.g. 0722123456
     * @param   int     @amount         Amount of money to send.
     * @return  LipishaResponse
     */
    public function send_money($account_number, $mobile_number, $amount)
    {
        $api_endpoint = "send_money";
        $api_parameters  = '&account_number=' . urlencode($account_number);
        $api_parameters .= '&mobile_number=' . urlencode($mobile_number);
        $api_parameters .= '&amount=' . urlencode($amount);
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Sends airtime to provided mobile number from specified account
     * 
     * @param   string  $account_number Airtime account to charge
     * @param   string  @mobile_number  Mobile number to send airtime to e.g. 0722123456
     * @param   int     @amount         Amount of airtime to send.
     * @param   string  $mobile_network Mobile network e.g. SAF
     * @return  LipishaResponse
     */
    public function send_airtime($account_number, $mobile_number, $amount, $mobile_network)
    {
        $api_endpoint = "send_airtime";
        $api_parameters  = '&account_number=' . urlencode($account_number);
        $api_parameters .= '&mobile_number=' . urlencode($mobile_number);
        $api_parameters .= '&mobile_network=' . urlencode($mobile_network);
        $api_parameters .= '&amount=' . urlencode($amount);
        return $this->execute($api_endpoint, $api_parameters);
    }        


    /**
     * Gets available balance in float account.
     * 
     * @param   string  $account_number Float account number e.g. 035400
     * @return  LipishaResponse
     */
    public function get_float($account_number)
    {
        $api_endpoint = "get_float";    
        $api_parameters = '&account_number=' . urlencode($account_number);
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Gets available balance in Lipisha main account.
     * 
     * @return  LipishaResponse
     */
    public function get_balance()
    {
        $api_endpoint = "get_balance";    
        $api_parameters = '';
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Authorizes a card transaction - captures cardholder funds.
     * @see Lipisha::complete_card_transaction on how to complete transaction.
     * 
     * @param   string  $account_number Account to credit with funds.
     * @param   string  $name
     * @param   string  $email
     * @param   string  $mobile_number
     * @param   string  $address1
     * @param   string  $address2
     * @param   string  $country
     * @param   string  $state
     * @param   string  $zip
     * @param   string  $card_number    Full 16 digit card number with no spaces.
     * @param   string  $expiry         Card expiry date in format MMYYYYY
     * @param   string  $security_code  Card security code
     * @param   float   $amount         Amount to authorize
     * @param   string  $currency       Three character currency code to charge
     *                                  transaction in e.g. KES, USD
     * @return  LipishaResponse
     */
    public function authorize_card_transaction($account_number="",
                                               $name="",
                                               $email="",
                                               $mobile_number="",
                                               $address1="",
                                               $address2="",
                                               $country="",
                                               $state="",
                                               $zip="",
                                               $card_number="",
                                               $expiry="",
                                               $security_code="",
                                               $amount="",
                                               $currency="")
    {
        $api_endpoint = "authorize_card_transaction";
        $api_parameters  = '&account_number=' . urlencode($account_number);
        $api_parameters .= '&name=' . urlencode($name);
        $api_parameters .= '&email=' . urlencode($email);
        $api_parameters .= '&mobile_number=' . urlencode($mobile_number);
        $api_parameters .= '&address1=' . urlencode($address1);
        $api_parameters .= '&address2=' . urlencode($address2);
        $api_parameters .= '&country=' . urlencode($country);
        $api_parameters .= '&state=' . urlencode($state);
        $api_parameters .= '&zip=' . urlencode($zip);
        $api_parameters .= '&card_number=' . urlencode($card_number);
        $api_parameters .= '&expiry=' . urlencode($expiry);
        $api_parameters .= '&security_code=' . urlencode($security_code);
        $api_parameters .= '&amount=' . urlencode($amount);
        $api_parameters .= '&currency=' . urlencode($currency);
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Completes transfer of funds captured by authorization api call.
     * @see Lipisha::authorize_card_transaction
     * 
     * @param   string  $transaction_index  Transaction index received after
     *                                      authorize_card_transaction call
     * @param   string  $transaction_reference  Transaction reference received
     *                                          after authorize_card_transaction
     *                                          call
     * @return  LipishaResponse
     */
    public function complete_card_transaction($transaction_index="", $transaction_reference="")
    {
        $api_endpoint = "complete_card_transaction";
        $api_parameters  = '&transaction_index=' . urlencode($transaction_index);
        $api_parameters .= '&transaction_reference=' . urlencode($transaction_reference);
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Reverses a card transaction that has been authorized.
     * 
     * @param   string  $transaction_index  Transaction index received after
     *                                      authorize_card_transaction call
     * @param   string  $transaction_reference  Transaction reference received
     *                                          after authorize_card_transaction
     *                                          call
     * @return  LipishaResponse
     */
    public function reverse_card_authorization($transaction_index="", $transaction_reference="")
    {
        $api_endpoint = "reverse_card_authorization";    
        $api_parameters  = '&transaction_index=' . urlencode($transaction_index);
        $api_parameters .= '&transaction_reference=' . urlencode($transaction_reference);
        return $this->execute($api_endpoint, $api_parameters);
    }

    /**
     * Cancels a card transaction for which funds have already been charged.
     * 
     * @param   string  $transaction_index  Transaction index received after
     *                                      complete_card_transaction call
     * @param   string  $transaction_reference  Transaction reference received
     *                                          after complete_card_transaction
     *                                          call
     * @return LipishaResponse
     */
    public function void_card_transaction($transaction_index="", $transaction_reference="")
    {
        $api_endpoint = "void_card_transaction";
        $api_parameters  = '&transaction_index=' . urlencode($transaction_index);
        $api_parameters .= '&transaction_reference=' . urlencode($transaction_reference);
        return $this->execute($api_endpoint, $api_parameters);
    }
    
}
?>
