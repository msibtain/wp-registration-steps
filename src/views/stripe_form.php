<form id="payment-form">
    <?php
    $objUser = get_user_by("login", $_GET['user']);
    $vtype = $_GET['vtype'];
    switch ($vtype)
    {
        case "standard_vehicle":
            $amount_hidden = 1049;
            break;
        case "utv":
            $amount_hidden = 849;
            break;
        case "military_vehicle":
            $amount_hidden = 1049;
            break;
        default:
            $amount_hidden = 0.99;
            break;
    }
    ?>
    <table border="0" class="table table-noborder">
        <tr>
            <td>
                <label>Name on Card <font color="red">*</font></label>
                <br>
                <input type="text" name="name_on_card" id="name_on_card" value="<?php echo $objUser->display_name ?>" />
            </td>
            <td>
                <label>Email Address <font color="red">*</font></label>
                <br>
                <input type="email" name="email" id="email" value="<?php echo $objUser->user_email ?>" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label>Credit / Debit Card <font color="red">*</font></label>
                <div id="card-element">Loading payment form...</div>
            </td>
        </tr>
    </table>

    <div align="center">
        <input type="hidden" name="username_hidden" id="username_hidden" value="<?php echo $_GET['user']; ?>">
        <input type="hidden" name="amount_hidden" id="amount_hidden" value="<?php echo $amount_hidden; ?>">
        <button type="submit" class="btn btn-register">Complete Registration</button>
    </div>
    

    <div id="card-errors" role="alert"></div>

</form>