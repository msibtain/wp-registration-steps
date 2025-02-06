<?php
class wpRegistrationSteps
{
    function __construct()
    {
        add_shortcode('ilab-steps-script', [$this, 'ilab_steps_script']);
        add_shortcode('ilab-sel-states', [$this, 'ilab_sel_states']);
        add_shortcode('ilab-user-detail-form', [$this, 'ilab_user_detail_form']);

        add_action( 'wp_ajax_ilab_user_detail_submit', [$this, 'ilab_user_detail_submit'] );
        add_action( 'wp_ajax_nopriv_ilab_user_detail_submit', [$this, 'ilab_user_detail_submit'] );
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
            do_action("ilab_user_registered_step1", $user_id);

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


            echo json_encode([
                'success' => true
            ]);
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


}

global $wpRegistrationSteps;
$wpRegistrationSteps = new wpRegistrationSteps();