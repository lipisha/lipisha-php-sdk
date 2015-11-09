<?php
    
    // Check if IPN server has made a callback
    if(isset($_POST)) 
    {
        // Your API details for authentication
        $my_api_key = 'YOUR API KEY';
        $my_api_signature = 'YOUR API SIGNATURE';

        // Check if authentication details are genuine
        if (($_POST["api_key"] == $my_api_key) && ($_POST["api_signature"] == $my_api_signature)) 
        {

            // Process Initiate
            if($_POST["api_type"] == "Initiate")
            {
                
                    // Perform action e.g save data to database or log file
                    $myFile = "transaction.log";
                    $fh = fopen($myFile, 'a') or die("can't open file");
                    fwrite($fh, "\r\n============= Initiate ================\r\n");
                    fwrite($fh, "Logged at " . date("Y-m-d H:i:s") . "\r\n");

                    foreach ($_POST as $key => $value) 
                    {
                        fwrite($fh, "$key = $value\r\n");
                    }
                    fclose($fh);        
                    
                    // Acknowledge API call
                    $response= array();
                    $response["api_key"] = $_POST["api_key"];
                    $response["api_signature"] = $_POST["api_signature"];
                    $response["api_version"] = $_POST["api_version"];
                    $response["api_type"] = "Receipt";
                    $response["transaction_reference"] = $_POST["transaction_reference"];
                    $response["transaction_status_code"] = "001";
                    $response["transaction_status"] = "Success";
                    $response["transaction_status_description"] = "Transaction received successfully.";
                    $response["transaction_custom_sms"] = "Payout completed. Asante sana sand box!"; // Send a custom sms

                    $myFile = "lipisha.log";
                    $fh = fopen($myFile, 'a') or die("can't open file");
                    fwrite($fh, "\r\n============= Response ================\r\n");
                    fwrite($fh, "Logged at " . date("Y-m-d H:i:s") . "\r\n");

                    foreach ($response as $key => $value) 
                    {
                        fwrite($fh, "$key = $value\r\n");
                    }
                    fclose($fh);

                    $json_response = json_encode($response);
                    header("Content-Type: application/json"); 
                    echo $json_response; // This should be the only printed out put of this page else session already sent error will occur                
            }
            
            // Process Acknowledge
            if($_POST["api_type"] == "Acknowledge")
            {
                // Perform action e.g update data to database or log file
                $myFile = "transaction.log";
                $fh = fopen($myFile, 'a') or die("can't open file");
                fwrite($fh, "\r\n============= Acknowledge ================\r\n");
                fwrite($fh, "Logged at " . date("Y-m-d H:i:s") . "\r\n");

                foreach ($_POST as $key => $value) 
                {
                    fwrite($fh, "$key = $value\r\n");
                }
                fclose($fh);                        
            }
            
        }
        else
        {
            // Log attempted fraud or hacking
            $myFile = "fraud.log";
            $fh = fopen($myFile, 'a') or die("can't open file");
            fwrite($fh, "\r\n============= Possible Fraud ================\r\n");
            fwrite($fh, "Logged at " . date("Y-m-d H:i:s") . "\r\n");

            foreach ($_POST as $key => $value) 
            {
                fwrite($fh, "$key = $value\r\n");
            }
            fclose($fh);
        }            
    }
?>
