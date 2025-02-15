<script>
jQuery(document).ready(function($){

    jQuery.ajax({
        url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
        data: {
            action: 'ilab_get_user_data'
        },
        success: function(response){
            var r = JSON.parse( response );
            
            if (r.success == true)
            {
                jQuery('#billing_first_name').val( r.user.first_name );
                jQuery('#billing_last_name').val( r.user.last_name );
                jQuery('#billing_address_1').val( r.user.address );
                jQuery('#billing_phone').val( r.user.phone_number );
            }
            else
            {
                
            }
        }
    });
});
</script>