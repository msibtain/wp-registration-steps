<?php
class clsGoHighLevelV2
{
    private string $ghl_api_url;
    private string $ghl_api_key;
    private string $ghl_workflow_id;
    private string $ghl_location_id;

    function __construct()
    {
        $this->ghl_api_url = "https://rest.gohighlevel.com/v1";
        $this->ghl_api_key = get_option("ghl_api_key");
        $this->ghl_workflow_id = get_option("ghl_workflow_id");
        $this->ghl_location_id = get_option("ghl_location_id");

        //add_action("ilab_user_registered_step1", [$this, "ilab_create_contact_ghl"]);
        add_action('woocommerce_thankyou', [$this, "ilab_create_contact_ghl"], 10, 1);

        add_action("init", [$this, 'ilab_test_ghl']);
    }

    function ilab_create_contact_ghl( $order_id )
    {
        if (!$order_id) {
            return;
        }
        
        $order = wc_get_order($order_id);

        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $customer_email = $order->get_billing_email();
        $customer_phone = $order->get_billing_phone();
        $address1 = $order->get_billing_address_1();
        $city = $order->get_billing_city();
        $state = $order->get_billing_state();
        $postcode = $order->get_billing_postcode();

        $strContactId = $this->createContact(
            $first_name, 
            $last_name, 
            $customer_email, 
            $customer_phone,
            $address1,
            $city,
            $state,
            $postcode
        );
        $workflowId = ($this->ghl_workflow_id) ?? null;
        if ($strContactId !== false && $workflowId !== null)
        {
            $this->executeWorkFlow( $strContactId, $workflowId );
        }


    }

    function ilab_test_ghl()
    {
        if ( isset($_GET['ilab_test']) && $_GET['ilab_test'] === "1" )
        {
            $api_url = $this->ghl_api_url . "/contacts/";
            $api_key = $this->ghl_api_key;
    
            $phone = str_replace("+1", "", $phone);
    
            $data = [
                "firstName" => "Aamir",
                "lastName" => "Nazar",
                "email" => "ilab123@test.com",
                "phone" => "+11231231234",
                "locationId" => $this->ghl_location_id,
                "address1" => "Test Address",
                "city" => "Dolomite",
                "state" => "AL",
                "postalCode" => "35061"
            ];
    
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $api_key",
                "Content-Type: application/json"
            ]);
    
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
    
            
            if ($response === false) 
            {
                echo "error";
            } 
            else 
            {
                $response_data = json_decode($response, true);
                if (isset($response_data['contact']['id'])) 
                {
                    echo $response_data['contact']['id'];
                } 
                else 
                {
                    echo "error 2";
                }
            }
    
            curl_close($ch);
            exit;
        }

    }

    function ilab_create_contact_ghl_old( $user_id )
    {
        $objUser        = get_user_by("id", $user_id);
        $user_email     = $objUser->user_email;
        $display_name   = $objUser->display_name;
        $phone_number   = get_user_meta($user_id, "phone_number", true);
        $first_name     = get_user_meta($user_id, "first_name", true);
        $last_name      = get_user_meta($user_id, "last_name", true);

        $strContactId = $this->createContact($first_name, $last_name, $user_email, $phone_number);
        $workflowId = ($this->ghl_workflow_id) ?? null;
        if ($strContactId !== false && $workflowId !== null)
        {
            $this->executeWorkFlow( $strContactId, $workflowId );
        }

    }

    function createContact(string $first_name, string $last_name, string $email, string $phone, string $address1 = '', string $city = '', string $state = '', string $postcode = '')
    {
        $api_url = $this->ghl_api_url . "/contacts/";
        $api_key = $this->ghl_api_key;

        $phone = str_replace("+1", "", $phone);

        $data = [
            "firstName" => $first_name,
            "lastName" => $last_name,
            "email" => $email,
            "phone" => $phone,
            "locationId" => $this->ghl_location_id,
            "address1" => $address1,
            "city" => $city,
            "state" => $state,
            "postalCode" => $postcode
        ];

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_key",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        
        if ($response === false) 
        {
            return false;
        } 
        else 
        {
            $response_data = json_decode($response, true);
            if (isset($response_data['contact']['id'])) 
            {
                return $response_data['contact']['id'];
            } 
            else 
            {
                return false;
            }
        }

        curl_close($ch);

        return false;
    }

    function executeWorkFlow(string $contactId, string $workflowId)
    {

        $apiKey = $this->ghl_api_key;
        $url =  $this->ghl_api_url . "/contacts/$contactId/workflow/{$workflowId}";

        $data = [
            //'contact_id' => $contactId,
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        if ($httpCode === 200) 
        {
            return true;
        } 
        else 
        {
            return false;
        }

    }

    
}

new clsGoHighLevelV2();

if (!function_exists('p_r')){function p_r($s){echo "<pre>";print_r($s);echo "</pre>";}}