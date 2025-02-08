<?php
class clsStripePayments
{
    private string $stripeMode = "test";

    private string $stripe_key;
    private string $stripe_secret;

    function __construct()
    {

        add_action("init", [$this, "ilab_setup_stripe_keys"]);

        add_action('rest_api_init', function () {
            register_rest_route('stripe', '/ilabwebhook/', array(
                'methods' => 'POST',
                'callback' => [$this, 'es_stripe_wewbhook'],
                'permission_callback' => '__return_true'
            ));
        });

        add_action('rest_api_init', function () {
            register_rest_route('stripe', '/pi/', array(
                'methods' => 'POST',
                'callback' => [$this, 'stripe_payment_intent'],
                'permission_callback' => '__return_true'
            ));
        });

        add_action('rest_api_init', function () {
            register_rest_route('customer', '/update/', array(
                'methods' => 'POST',
                'callback' => [$this, 'wp_updte_user'],
                'permission_callback' => '__return_true'
            ));
        });

        add_action('wp_enqueue_scripts', [$this, 'ilab_wp_scripts']);
        add_shortcode('ilab_stripe_payment_form', [$this, 'ilab_stripe_payment_form']);
        
    }

    function ilab_setup_stripe_keys()
    {
        //$stripe = new Forminator_Gateway_Stripe();

        if (get_option("ilab_stripe_mode") === "test")
        {
            $this->stripe_key       = get_option("ilab_stripe_key_test");
            $this->stripe_secret    = get_option("ilab_stripe_secret_test");
        }
        else
        {
            $this->stripe_key       = get_option("ilab_stripe_key_live");
            $this->stripe_secret    = get_option("ilab_stripe_secret_live");
        }
    }

    function ilab_wp_scripts()
    {
        wp_register_style( 'ilab_css', plugins_url( 'css/ilab.css', __FILE__ ), array(), time() );
        wp_register_style( 'stripe_css', plugins_url( 'css/stripe.css', __FILE__ ), array(), time() );

        wp_register_script( 'stripe_library', 'https://js.stripe.com/v3/' );
        wp_register_script( 'stripe_js', plugins_url( 'js/stripe.js', __FILE__ ), array('jquery'), time() );
    }

    function ilab_stripe_payment_form()
    {
        ob_start();
        
        wp_enqueue_style( 'stripe_css' );
        wp_enqueue_script( 'stripe_library' );
        wp_enqueue_script( 'stripe_js' );        
        wp_add_inline_script( 'stripe_js', 'const stripe_config = ' . json_encode( array(
            'wp_json' => home_url() . "/wp-json",
            'stripe_key' => $this->stripe_key,
            'stripe_secret' => $this->stripe_secret,
        ) ), 'before' );

        include("views/stripe_form.php");
        return ob_get_clean();
    }

    function es_stripe_wewbhook()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        error_log("stripe webhook for event " . $data['type']);        

        if ( 
            $data['type'] === "charge.captured" && 
            $data['data']['object']['amount_captured'] === 99
        )
        {
            if ( isset($data['data']['object']['metadata']['Email Address']) )
            {
                $email = $data['data']['object']['metadata']['Email Address'];
                $name = $data['data']['object']['metadata']['Name on Card'];
                $objUser = get_user_by("email", $email);

                if ($objUser->ID)
                {

                    //$stripe = new Forminator_Gateway_Stripe();
                    //$secret_key = ($data['livemode']) ? $stripe->get_live_secret( true ) : $stripe->get_test_secret( true );

                    if ($data['livemode'])
                    {
                        $secret_key    = get_option("ilab_stripe_secret_live");
                    }
                    else
                    {
                        $secret_key    = get_option("ilab_stripe_secret_test");
                    }

                    $customer_id = $this->createStripeCustomer( $secret_key, $email, $name, $data['data']['object']['payment_method'] );

                    update_user_meta($objUser->ID, "stripe_livemode", $data['livemode']);
                    update_user_meta($objUser->ID, "stripe_customer_id", $customer_id);
                    update_user_meta($objUser->ID, "stripe_payment_intent", $data['data']['object']['payment_intent']);
                    update_user_meta($objUser->ID, "stripe_payment_method", $data['data']['object']['payment_method']);
                }
            }
            
        }
    }

    function stripe_payment_intent( $data )
    {
        $input = file_get_contents("php://input");
        //parse_str($input, $data);
        $data = json_decode($input, true);

        \Stripe\Stripe::setApiKey( $this->stripe_secret  );

        $customerData = [
            'name' => @$data['name'],
            'email' => $data['email']
        ];
        
        // Create a new customer in Stripe (optional)
        $customer = \Stripe\Customer::create([
            'name' => $customerData['name'],
            'email' => $customerData['email']
        ]);
        

        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $data['amount'] * 100,
                'currency' => $data['currency'],
                'customer' => $customer->id,
                'automatic_payment_methods' => [
                    'enabled' => 'true',
                ],
                'payment_method_options' => [
                    'card' => [
                        'setup_future_usage' => 'off_session',
                    ],
                ],
            ]);
        
            $output = [
                'clientSecret' => $paymentIntent->client_secret,
                'customer_id' => $paymentIntent->customer
            ];
        
            // Save the customer ID in your database if needed
            // $customer_id = $paymentIntent->customer;
            // Save $customer_id in your database.
            return new WP_REST_Response($output, 200);

        } catch (Error $e) {
            return new WP_REST_Response(['error' => $e->getMessage()], 404);
        }
    }

    function wp_updte_user()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $username          = $data['username'];
        $email          = $data['email'];
        $customer_id    = $data['customer_id'];
        $payment_method = $data['payment_method'];

        if (email_exists($email)) 
        {
            # update password and customer id;
            $objUser = get_user_by("email", $email);

            wp_set_password( $password, $objUser->ID );
            update_user_meta($objUser->ID, "stripe_customer_id", $customer_id);
            update_user_meta($objUser->ID, "stripe_payment_method", $payment_method);

            //$this->updateCustomerPaymentMethod( $customer_id, $payment_method );

            return new WP_REST_Response([
                'success' => true, 
                'message' => 'User added',
                'success_url' => get_permalink( get_option('ilab_stripe_payment_thankyoupage') )
            ], 200);   

        }
        
        
    }

    function stripe_customer_charge( $amount, $customer_id, $payment_method_id )
    {
        \Stripe\Stripe::setApiKey( $this->stripe_secret  );

        try {

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => "usd",
                'customer' => $customer_id,
                'payment_method' => $payment_method_id,
                'off_session' => true,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => 'true',
                ],
                
            ]);
        
            return "Charge successful!";
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}

global $clsStripePayments;
$clsStripePayments = new clsStripePayments();