<?php

namespace lipisha;


require_once __DIR__  . "/../vendor/autoload.php";

class LipishaTest extends \PHPUnit_Framework_TestCase
{

    private $STATUS_SUCCESS = "SUCCESS";
    private $api_key;
    private $api_signature;
    protected $lipisha;
    private $admin_account_login = "";
    private $test_user_role = "";
    private $transaction_id_confirm = "";
    private $transaction_id_reverse = "";
    //Send money config
    private $test_mobile_number = "";
    private $test_payout_account = "";
    private $test_float_account = "";
    private $test_airtime_account = "";
    private $test_airtime_amount = 10;
    private $test_airtime_network = "SAF";
    private $test_payout_amount = 10;
    //Card Tests
    private $test_card_info = array("account_number"=>"",
                                    "name"=>"",
                                    "email"=>"",
                                    "mobile_number"=>"",
                                    "address1"=>"",
                                    "address2"=>"",
                                    "country"=>"",
                                    "state"=>"",
                                    "zip"=>"",
                                    "card_number"=>"",
                                    "expiry"=>"",
                                    "security_code"=>"",
                                    "amount"=>100.0,
                                    "currency"=>"KES");
    private $test_card_void_index = "";
    private $test_card_void_reference = "";
    private $test_card_complete_index = "";
    private $test_card_complete_reference = "";
    private $test_card_reverse_index = "";
    private $test_card_reverse_reference = "";

    protected function setUp()
    {
        $this->api_key = getenv("LIPISHA_API_KEY");
        $this->api_signature = getenv("LIPISHA_API_SIGNATURE");
        if ($this->api_key && $this->api_signature) {
            print("API Credentials Found: Proceeding with Tests\n");
        } else {
            $this->markTestSkipped("Tests Require LIPISHA_API_KEY " . 
                                   " and LIPISHA_API_SIGNATURE set up");
        }
        $this->lipisha = new \Lipisha\Lipisha($this->api_key,
                                              $this->api_signature,
                                              "live");
    }
    
    public function testGetBalance()
    {
        $response = $this->lipisha->get_balance();
        print_r($response);
        $this->assertNotNull($response);
        $this->assertNotNull($response->status);
        $this->assertNotNull($response->status_code);
        $this->assertNotNull($response->status_description);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }

    public function testGetFloat()
    {
        if (!$this->test_float_account) {
            $this->markTestSkipped("test_float_account must be defined");
        }
        $response = $this->lipisha->get_float($this->test_float_account);
        print_r($response);
        $this->assertNotNull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
        $this->assertEquals($this->test_float_account,
                            $response->content["account_number"]);
    }

    
    public function testGetTransactions()
    {
        $response = $this->lipisha->get_transactions($transaction_amount_minimum=10.0,
                                                     $transaction_amount_maximum=200.0);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }
    
    public function testGetCustomers()
    {
        $response = $this->lipisha->get_customers($customer_payments_minimum=20.0);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }
    
    public function testCreatePaymentAccount()
    {
        if (!$this->admin_account_login) {
            $this->markTestSkipped("admin_account_login must be defined");
        }
        $response = $this->lipisha->create_payment_account(1, "TEST A/C", $this->admin_account_login);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }
    
    public function testCreateUser()
    {
        if (!$this->test_user_role) {
            $this->markTestSkipped("test_user_role must be defined");
        }
        $response = $this->lipisha->create_user("TEST USER",
                                                $this->test_user_role,
                                                "0722123456",
                                                "test-user-9999@lipisha.com",
                                                "test_user_airtime",
                                                "password");
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }
    
    public function testCreateWithdrawalAccount()
    {
        if (!$this->admin_account_login) {
            $this->markTestSkipped("admin_account_login must be defined");
        }
        $response = $this->lipisha->create_withdrawal_account(1,
                                                              "Main A/C",
                                                              "0100526986000000099",
                                                              "Gyro Bank", 
                                                              "HQ",
                                                              "002000 Gyro Street, DY-CD01",
                                                              "LPSHKEXXXX",
                                                              $this->admin_account_login);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
        $this->assertEquals("0100526986000000099",
                            $response->content["transaction_account_number"]);
    }
    
    public function testConfirmTransaction()
    {
        if (!$this->transaction_id_confirm) {
            $this->markTestSkipped("transaction_id_confirm must be defined");
        }
        $response = $this->lipisha->confirm_transaction($this->transaction_id_confirm);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
        $this->assertEquals("Completed", $response->content["transaction_status"]);
    }

    public function testReverseTransaction()
    {
        if (!$this->transaction_id_confirm) {
            $this->markTestSkipped("transaction_id_reverse must be defined");
        }
        $response = $this->lipisha->reverse_transaction($this->transaction_id_reverse);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }
    
    public function testSendMoney() 
    {
        if (!($this->test_payout_account && $this->test_mobile_number)){
            $this->markTestSkipped("test_payout_account and test_mobile_number must be defined");
        }
        $response = $this->lipisha->send_money($this->test_payout_account,
                                               $this->test_mobile_number,
                                               $this->test_payout_amount);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }
    
    public function testSendAirtime()
    {
        if (!($this->test_airtime_account && $this->test_mobile_number)){
            $this->markTestSkipped("test_airtime_account and test_mobile_number must be defined");
        }
        $response = $this->lipisha->send_airtime($this->test_airtime_account,
                                                 $this->test_mobile_number,
                                                 $this->test_airtime_amount,
                                                 $this->test_airtime_network);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
        $this->assertEquals($this->test_mobile_number, $response->content["mobile_number"]);
        $this->assertEquals($this->test_airtime_amount, $response->content["amount"]);
    }
    
    public function testAuthorizeCard()
    {
        if(!($this->test_card_info["card_number"] && 
             $this->test_card_info["expiry"])){
                  $this->markTestSkipped("Please define card info for tests");
        }
        $response = call_user_func_array(array($this->lipisha,
                                               "authorize_card_transaction"),
                                         $this->test_card_info);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
    }
    
    public function testCompleteCard()
    {
        if (!($this->test_card_complete_index && $this->test_card_complete_reference)){
            $this->markTestSkipped("test_card_complete_index and test_card_complete_reference must be defined");
        }
        $response = $this->lipisha->complete_card_transaction($this->test_card_complete_index,
                                                              $this->test_card_complete_reference);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
        $this->assertEquals($this->test_card_complete_index,
                            $response->content["transaction_index"]);
        $this->assertEquals($this->test_card_complete_reference,
                            $response->content["transaction_reference"]);
    }

    public function testReverseCard()
    {
        if (!($this->test_card_reverse_index && $this->test_card_reverse_reference)){
            $this->markTestSkipped("test_card_reverse_index and test_card_reverse_reference must be defined");
        }
        $response = $this->lipisha->reverse_card_authorization($this->test_card_reverse_index,
                                                               $this->test_card_reverse_reference);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
        $this->assertEquals($this->test_card_reverse_index,
                            $response->content["transaction_index"]);
        $this->assertEquals($this->test_card_reverse_reference,
                            $response->content["transaction_reference"]);
    }

    public function testVoidCard()
    {
        if (!($this->test_card_void_index && $this->test_card_void_reference)){
            $this->markTestSkipped("test_card_void_index and test_card_void_reference must be defined");
        }
        $response = $this->lipisha->void_card_transaction($this->test_card_void_index,
                                                          $this->test_card_void_reference);
        print_r($response);
        $this->assertNotnull($response);
        $this->assertNotNull($response->content);
        $this->assertNotNull($response->json);
        $this->assertEquals($this->STATUS_SUCCESS, $response->status);
        $this->assertEquals($this->test_card_void_index,
                            $response->content["transaction_index"]);
        $this->assertEquals($this->test_card_void_reference,
                            $response->content["transaction_reference"]);
    }
}
?>
