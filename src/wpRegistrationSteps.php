<?php
class wpRegistrationSteps
{
    function __construct()
    {
        add_shortcode('ilab-steps-script', [$this, 'ilab_steps_script']);
        add_shortcode('ilab-sel-states', [$this, 'ilab_sel_states']);
        add_shortcode('ilab-user-detail-form', [$this, 'ilab_user_detail_form']);
        add_shortcode('ilab-checkout-autofill', [$this, 'ilab_checkout_autofill']);
        add_action('woocommerce_thankyou', [$this, 'ilab_wc_thankyou_scripts'], 10, 1);

        add_action( 'wp_ajax_ilab_user_detail_submit', [$this, 'ilab_user_detail_submit'] );
        add_action( 'wp_ajax_nopriv_ilab_user_detail_submit', [$this, 'ilab_user_detail_submit'] );

        add_action( 'wp_ajax_ilab_get_user_data', [$this, 'ilab_get_user_data'] );
        add_action( 'wp_ajax_nopriv_ilab_get_user_data', [$this, 'ilab_get_user_data'] );
    }

    function ilab_steps_script()
    {
        ob_start();
        include('views/steps_script.php');
        return ob_get_clean();
    }

    function ilab_sel_states()
    {
        ob_start();
        include('views/states_dropdowns.php');
        return ob_get_clean();
    }

    function ilab_user_detail_form()
    {
        ob_start();
        include('views/user_detail_form.php');
        return ob_get_clean();
    }

    function ilab_user_detail_submit()
    {
        $form_data = $_REQUEST['form_data'];
        parse_str($form_data, $data);

        $user_id = $this->check_and_register_user($data['email'], $data['username'], $data['password']);

        if ($user_id > 0)
        {
            update_user_meta($user_id, "first_name", $data['first_name']);
            update_user_meta($user_id, "last_name", $data['last_name']);
            update_user_meta($user_id, "vehicle_type", $data['vehicle_type']);
            update_user_meta($user_id, "state", $data['state']);
            update_user_meta($user_id, "phone_number", $data['phone_number']);
            update_user_meta($user_id, "address", $data['address']);
            update_user_meta($user_id, "llc_name", $data['llc_name']);
            update_user_meta($user_id, "vehicle", $data['vehicle']);
            update_user_meta($user_id, "year", $data['year']);
            update_user_meta($user_id, "engine_type", $data['engine_type']);
            update_user_meta($user_id, "vin", $data['vin']);
            update_user_meta($user_id, "color", $data['color']);
            update_user_meta($user_id, "miles", $data['miles']);
            update_user_meta($user_id, "paying_cash", $data['paying_cash']);
            update_user_meta($user_id, "bank_name", $data['bank_name']);
            update_user_meta($user_id, "bank_address", $data['bank_address']);
            update_user_meta($user_id, "bank_phone", $data['bank_phone']);
            update_user_meta($user_id, "bank_contact", $data['bank_contact']);
            update_user_meta($user_id, "have_turn_signal", $data['have_turn_signal']);

            do_action("ilab_user_registered_step1", $user_id);

            if ( $this->loginWPUser( $user_id ) )
            {
                $this->addProductToCart( $data['vehicle_type'] );

                echo json_encode([
                    'success' => true,
                    'user_data' => [
                        'email' => $data['email'],
                        'phone' => $data['phone_number'],
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name']
                    ]
                ]);
            }
            else
            {
                echo json_encode([
                    'success' => false,
                    'message' => 'Something wrong with registration.'
                ]);    
            }

            
        }
        else
        {
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists.'
            ]);
        }
        
        exit();
    }

    private function loginWPUser( $user_id )
    {
        $user = get_user_by('ID', $user_id);
    
        if ($user) 
        {
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login, $user);
            
            return true;
        } 
        else 
        {
            return false;
        }
    }
    
    private function addProductToCart( $vType )
    {
        $product_id = "";

        switch($vType)
        {
            case "standard_vehicle";
                $product_id = 1107;
                break;
            case "utv";
                $product_id = 1109;
                break;
            case "military_vehicle";
                $product_id = 1110;
                break;
            case "boats";
                $product_id = 1111;
                break;
            case "rv";
                $product_id = 1112;
                break;
            case "motorcycle";
                $product_id = 1113;
                break;
            case "trailer";
                $product_id = 1114;
                break;
            case "commercial";
                $product_id = 1115;
                break;
        }

        if ($product_id)
        {
            $found = false;

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) 
            {
                if ($cart_item['product_id'] == $product_id) {
                    $found = true;
                    break;
                }
            }

            if (!$found) 
            {
                WC()->cart->add_to_cart($product_id);
            }
        }
    }

    private function check_and_register_user($email, $username, $password): int
    {
        // Check if the email already exists
        if (email_exists($email)) {
            return 0;
        }

        // Register the user if the email does not exist
        $user_id = wp_create_user($username, $password, $email);

        // Check if user creation was successful
        if (is_wp_error($user_id)) {
            return 0;
        }

        return $user_id;
    }

    function ilab_checkout_autofill()
    {
        ob_start();
        include('views/checkout_form_autofill.php');
        return ob_get_clean();
    }

    function ilab_get_user_data()
    {
        $user_id = get_current_user_id();

        if ($user_id > 0)
        {
            echo json_encode([
                'success' => true,
                'user' => [
                    'first_name' => get_user_meta($user_id, "first_name", true),
                    'last_name' => get_user_meta($user_id, "last_name", true),
                    'address' => get_user_meta($user_id, "address", true),
                    'phone_number' => get_user_meta($user_id, "phone_number", true),
                ]
            ]);
        }
        else
        {
            echo json_encode([
                'success' => false,
                'message' => 'No user is logged in.'
            ]);
        }
        

        exit;
    }

    function ilab_wc_thankyou_scripts( $order_id )
    {
        $order = wc_get_order($order_id);
        $email = $order->get_billing_email();

        ob_start();
        ?>
        <script>
            fbq('track', 'Purchase', {
                value: "<?php echo ($order->get_total()); ?>",
                currency: "USD",
                user_data: {
                    email: "<?php echo $email; ?>"
                }
            });
        </script>
        <?php
        echo ob_get_clean();
    }

}

global $wpRegistrationSteps;
$wpRegistrationSteps = new wpRegistrationSteps();