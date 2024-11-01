<?php
if (!defined('ABSPATH')) {
    exit;
}
if (isset($_POST['submit']) && wp_verify_nonce($_REQUEST[PAONE_SU_LANG], 'paone_su_admin_settings')) {
    $this->savePaoneSuFields();
}
$data = $this->getPaoneSuFields();
?>

<form action="" method="post" id="paone_su_admin_setting" name="paone_su_admin_setting"
      enctype="multipart/form-data">
    <?php wp_nonce_field('paone_su_admin_settings', PAONE_SU_LANG, false); ?>

    <p>
    <h2>
        <?php _e('Stock Update for WooCommerce', PAONE_SU_LANG); ?>
    </h2>
    </p>
    <table class="form-table">
        <tbody>
        <?php if (!empty($data['last_updated_at'])) {
            ?>
            <tr>
                <th class="row">Last updated At</th>
                <td>
                    <a href="<?php echo $data['stock_csv'] ?>" download="">
                        <?php echo esc_attr($data['last_updated_at']); ?>
                    </a>
                </td>
            </tr>
            <?php
        } ?>
        <tr>
            <th class="row">Update Using File</th>
            <td>
                <div class="paone_su_checkbox_div">
                    <label class="switch">
                        <input type="checkbox" class="paone_su_checkbox" name="paone_su_checkbox"
                               id="paone_su_checkbox">
                        <span class="slider round"></span>
                    </label>
                    <span class="paone_su_checkbox_text">
                <?php _e(' Upload File ', PAONE_SU_LANG); ?>
                </span>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Send Confirmation', PAONE_SU_LANG); ?></th>
            <td>
                <label class="switch">
                    <input type="checkbox" class="send_email" name="send_email"
                           id="send_email" <?php echo($data['send_email'] ? 'checked' : ''); ?>>
                    <span class="slider round"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Enter Email Id (Comma seperated)', PAONE_SU_LANG); ?></th>
            <td>
                <input type="text" id="emails" name="emails" value="<?php echo esc_attr($data['emails']) ?>">
            </td>
        </tr>

        <tr class="using_post_fields">
            <th scope="row"><?php _e('Stock Values', PAONE_SU_LANG); ?></th>
            <td>
                <?php
                echo $this->tableInput("stocks", array("Sku", "Stock"), $data['stocks'], array(), array('after_add_function' => 'jscolor.installByClassName(\'jscolor\')'));
                ?>
            </td>
        </tr>
        <tr class="using_file">
            <th scope="row"><?php _e('Download Sample CSV', PAONE_SU_LANG); ?></th>
            <td>
                <a href="<?php echo PAONE_SU_WP_URL . '/assets/csv/sample.csv' ?>"
                   download><?php _e('Download', PAONE_SU_LANG) ?></a>
            </td>
        </tr>
        <tr class="using_file">
            <th scope="row"><?php _e('Upload CSV', PAONE_SU_LANG); ?></th>
            <td>
                <input type="file" id="stock_csv" name="stock_csv">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button class="button button-primary" type="submit" name="submit"
                        value="submit"><?php _e('Save Options', PAONE_SU_LANG) ?></button>
            </td>
        </tr>
</form>
