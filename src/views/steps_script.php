<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDQfz8co0YKL0xDsfWNYzYg7hMP6R72I7E&libraries=places"></script>

<style>
@import url('https://cdn.jsdelivr.net/npm/bootstrap-v4-grid-only@1.0.0/dist/bootstrap-grid.min.css');

.steps:not(#step1) {
    display: none;
}
#sel_state_callback {
    margin-top: 20px;
}
.vtype, .btnSubmit {
    cursor: pointer;
}
.vtype_selected {
    border-color: #5568F8;
}
#bank_details_wrapper,
#formErrors {
    display: none;
}
.alert {
	position: relative;
	padding: .75rem 1.25rem;
	margin-bottom: 1rem;
	border: 1px solid transparent;
	border-radius: .25rem;
}
.alert-danger {
	color: #721c24;
	background-color: #f8d7da;
	border-color: #f5c6cb;
}
</style>

<script>
var current_step = 1;
jQuery(document).ready(function($){
    
    jQuery('.btnNext').on('click', function(){
        jQuery('#step'+current_step).hide();
        current_step++;
        jQuery('#step'+current_step).show();
    });

    jQuery('.btnBack').on('click', function(){
        jQuery('#step'+current_step).hide();
        current_step--;
        jQuery('#step'+current_step).show();
    });

    jQuery('.sel_state_select').on('change', function(){

        let randomNumber = Math.floor(Math.random() * (371 - 253 + 1)) + 253;

        var html = `<h3>GOOD NEWS!</h3><br>
                We've helped <b>` + randomNumber + `</b> people in <b>` + jQuery(this).val() + `</b> by registering 
                and titling their vehicles through our proven process, which means we can help you!
                <br><br>
                To see exactly how it works for you and get your vehicle on the road as well, click the button below.
            `;

        jQuery('#sel_state_callback').html( html );
        jQuery('#txtState').val( jQuery(this).val() );
    });

    jQuery('.vtype').on('click', function(){

        jQuery('.vtype').removeClass('vtype_selected');
        jQuery(this).addClass('vtype_selected');

        var vtype = jQuery(this).data('type');
        jQuery('#txtVehicleType').val( vtype );

        jQuery('#step'+current_step).hide();
        current_step++;
        jQuery('#step'+current_step).show();
        
    });

    // frmUserDetail

    jQuery('.btnSubmit').on('click', function(){
        jQuery('#frmUserDetail').submit();
    });

    jQuery('#frmUserDetail').on('submit', function(e){
        e.preventDefault();
        
        /* validations here */
        jQuery.ajax({
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            data: {
                action: 'ilab_user_detail_submit',
                form_data: jQuery(this).serialize()
            },
            success: function(response){
                var r = JSON.parse( response );

                if (r.success == true)
                {
                    jQuery('#formErrors div.alert').html( '' );
                    jQuery('#formErrors').hide();
                    //alert('redirect');        
                    if ( jQuery('input[name="have_turn_signal"]:checked').val() === "0" )
                    {
                        window.location = "<?php echo get_permalink(979); // Turn Signal Kit product ?>";
                    }
                    else
                    {
                        window.location = "<?php echo wc_get_checkout_url(); // Checkout page ?>";
                    }
                    // window.location = "<?php //echo get_permalink( get_option('ilab_stripe_payment_page') ) ?>/?user=" + jQuery('#txtUsername').val() + "&vtype=" + jQuery('#txtVehicleType').val();
                    //window.location = "https://5starregistration.com/registration-v2-step3/?user=" + jQuery('#txtUsername').val() + "&vtype=" + jQuery('#txtVehicleType').val();
                }
                else
                {
                    jQuery('#formErrors div.alert').html( r.message );
                    jQuery('#formErrors').show();
                }

                // https://5starregistration.com/registration-v2-step3/?user={text-1}&vtype={hidden-1}
            }
        });
    });

});    

function hideSteps()
{
    var steps = jQuery('.steps');
    jQuery.each(steps, function(){
        if (jQuery(this).attr('id') != "step1")
        {
            jQuery(this).hide();
        }
    });
}
</script>