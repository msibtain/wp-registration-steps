<div class="wrap">
    <h1>Stripe Settings</h1>

    <form action="" method="post">

        <?php
        if (isset($_POST['ilab_stripe_mode']))
        {
            update_option("ilab_stripe_mode", $_POST['ilab_stripe_mode']);
            update_option("ilab_stripe_key_live", $_POST['ilab_stripe_key_live']);
            update_option("ilab_stripe_secret_live", $_POST['ilab_stripe_secret_live']);
            update_option("ilab_stripe_key_test", $_POST['ilab_stripe_key_test']);
            update_option("ilab_stripe_secret_test", $_POST['ilab_stripe_secret_test']);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Settings has been saved!', 'ap2t' ); ?></p>
            </div>
            <?php
        }
        ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="blogname">Payment Mode</label></th>
                    <td>
                        <select name="ilab_stripe_mode" id="ilab_stripe_mode">
                            <option value="test" <?php if ( get_option("ilab_stripe_mode") === "test" ) echo "selected"; ?> >Test</option>
                            <option value="live" <?php if ( get_option("ilab_stripe_mode") === "live" ) echo "selected"; ?> >Live</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="blogname">Key ( Live )</label></th>
                    <td>
                        <input name="ilab_stripe_key_live" type="text" id="ilab_stripe_key_live" value="<?php echo get_option('ilab_stripe_key_live'); ?>" class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="blogname">Secret ( Live )</label></th>
                    <td>
                        <input name="ilab_stripe_secret_live" type="text" id="ilab_stripe_secret_live" value="<?php echo get_option('ilab_stripe_secret_live'); ?>" class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="blogname">Key ( Test )</label></th>
                    <td>
                        <input name="ilab_stripe_key_test" type="text" id="ilab_stripe_key_test" value="<?php echo get_option('ilab_stripe_key_test'); ?>" class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="blogname">Secret ( Test )</label></th>
                    <td>
                        <input name="ilab_stripe_secret_test" type="text" id="ilab_stripe_secret_test" value="<?php echo get_option('ilab_stripe_secret_test'); ?>" class="regular-text">
                    </td>
                </tr>

            </tbody>
        </table>

        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>

    </form>

</div>