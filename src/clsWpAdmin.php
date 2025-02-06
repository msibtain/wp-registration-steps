<?php
class clsWpAdmin
{
    private string $tblUserPaymentHistory;

    function __construct()
    {
        global $wpdb;
        $this->tblUserPaymentHistory = $wpdb->prefix . "user_payments";

        add_action('admin_menu', [$this, 'ilab_stripe_settings_menu']);
        add_action('admin_menu', [$this, 'hide_stripe_payment_page'], 999);
        add_filter( 'user_row_actions', [$this, 'ilab_custom_user_action_link'], 10, 2 );

    }

    static function user_payment_history_install() 
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "user_payments";
        $query = "CREATE TABLE IF NOT EXISTS `{$table_name}`(
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `amount` TINYTEXT NOT NULL,
                    `status` TEXT NULL,
                    `user_id` INT(11) NOT NULL,
                    `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY(id)
                    );";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $result = dbDelta($query);

    }

    function ilab_stripe_settings_menu()
    {

        add_menu_page(
            'Stripe Settings',
            'Stripe Settings',
            'manage_options', 
            'ilab-stripe-settings',
            [$this, 'ilab_stripe_settings_page'],
            'dashicons-admin-generic', // Icon
            90 // Position
        );

        // Add a new hidden admin page
        add_menu_page(
            'Stripe Payment',          
            'Stripe Payment',                
            'manage_options',             
            'ilab-stripe-payment',          
            [$this, 'ilab_stripe_payment_page'], 
            '',                           
            null                          
        );
    }

    function ilab_stripe_settings_page()
    {
        include('views/admin/stripe_settings.php');   
    }

    function ilab_custom_user_action_link( $actions, $user )
    {
        
        if ( current_user_can( 'edit_user', $user->ID ) ) 
        {
            $custom_link = add_query_arg( [
                'user_id' => $user->ID,
                'action' => 'stripe_payment',
                'page' => 'ilab-stripe-payment'
            ], admin_url( 'admin.php' ) );
    
            $actions['stripe_payment'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( $custom_link ),
                __( 'Stripe Payment', 'ilab' )
            );
        }
    
        return $actions;   
    }

    function ilab_stripe_payment_page()
    {
        include('views/admin/stripe_payment.php');
    }

    function hide_stripe_payment_page()
    {
        remove_menu_page('ilab-stripe-payment');
    }

    function addUserPaymentHistory( $amount, $status, $user_id )
    {
        /*
        if (str_contains($status, 'successful'))
        {
            $status = "Successful";
        }
        else
        {
            $status = "Fail";
        }
        */

        global $wpdb;
        $wpdb->insert( $this->tblUserPaymentHistory, [
            "amount" => $amount,
            "status" => $status,
            "user_id" => $user_id
        ] );
    }

    function getUserPaymentHistory( $user_id )
    {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM {$this->tblUserPaymentHistory} WHERE user_id = {$user_id}");
        return $rows;
    }
}

global $clsWpAdmin;
$clsWpAdmin = new clsWpAdmin();

register_activation_hook( "wp-registration-steps/init.php", array( 'clsWpAdmin', 'user_payment_history_install' ) );

if (!function_exists('p_r')){function p_r($s){echo "<pre>";print_r($s);echo "</pre>";}}