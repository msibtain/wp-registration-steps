<?php
global $clsStripePayments, $clsWpAdmin;
$user_id = $_GET['user_id'];
$objUser = get_user_by("ID", $user_id);
$strCustomerID = get_user_meta($user_id, "stripe_customer_id", true);
//$strPaymentMethodID = get_user_meta($user_id, "stripe_payment_method", true);
$strPaymentMethodID = '';


?>
<div class="wrap">
    <h1>Stripe Payment for user - "<?php echo $objUser->first_name; ?> <?php echo $objUser->last_name; ?>"</h1>

    <?php
    if ($strCustomerID)
    {
        if ($_POST)
        {
            if ( !$strPaymentMethodID )
            {
                $strPaymentMethodID = $clsStripePayments->stripe_customer_payment_method( $strCustomerID );
            }
            $response = $clsStripePayments->stripe_customer_charge( $_POST['amount'], $strCustomerID, $strPaymentMethodID );
            $clsWpAdmin->addUserPaymentHistory( $_POST['amount'], $response, $user_id );
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo $response; ?></p>
            </div>
            <?php
        }

        ?>
        <form method="post" >
            <table class="form-table">
            <tr>
                <th scope="row"><label for="blogname">Enter Amount</label></th>
                <td>
                    <input name="amount" type="text" id="amount" class="regular-text">
                </td>
            </tr>

            
            </table>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Pay Now"></p>

        </form>

        <hr>

        <?php

        $arrHistory = $clsWpAdmin->getUserPaymentHistory( $user_id );
        ?>

        <h3>Payment History</h3>
        
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <th width="5%"><b>S.No</b></th>
                    <th width="15%"><b>Amount</b></th>
                    <th><b>Status</b></th>
                    <th width="15%"><b>Date</b></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sno = 0;
                if (count($arrHistory))
                {
                    foreach ($arrHistory as $objHistory)
                    {
                        $sno++;
                        ?>
                        <tr>
                            <td width="5%"><?php echo $sno; ?></td>
                            <td width="15%"><?php echo $objHistory->amount ?></td>
                            <td><?php echo $objHistory->status ?></td>
                            <td width="15%"><?php echo $objHistory->payment_date ?></td>
                        </tr>
                        <?php
                    }
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="4">
                            No payments found.
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php

    }
    else
    {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>This user has no Stripe Customer ID.</p>
        </div>
        <?php
    }
    
    ?>
</div>