<?php
/**
 * Created by PhpStorm.
 * User: pawanpriyadarshi
 * Date: 2020-05-05
 * Time: 03:01
 */
if (!class_exists("PaoneSuAdminModel")) {
    class PaoneSuAdminModel
    {
        /*
         * Function to save the Admin field as Option
         */
        protected function savePaoneSuFields()
        {
            $data = array();
            $send_email = 0;
            if (isset($_POST['send_email']) && !empty(sanitize_text_field($_POST['send_email']))) {
                $send_email = true;
            }
            $data['last_updated_at']=date('Y/ M / d - H:i:s');
            $data['send_email'] = $send_email;
            $data['emails'] = sanitize_text_field($_POST['emails']);


            if (isset($_FILES['stock_csv']['name']) && $_FILES['stock_csv']['name'] != '') {
                add_filter('upload_dir', 'paone_su_upload_dir');

                add_filter('sanitize_file_name', 'paone_su_hash_filename', 10);
                $uploaded_file = $_FILES['stock_csv'];
                $filename = sanitize_file_name($_FILES['stock_csv']['name']);

                $file_type = wp_check_filetype_and_ext($uploaded_file['tmp_name'], $filename);
                $upload_overrides = array('test_form' => false);
//                if (!wp_match_mime_types('image', $file_type['type'])) {
//                    echo('<div class="paone_su_error">The uploaded file is not a valid image. Please try again.</div>');
//                    return;
//                }
                $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
                if ($movefile && !isset($movefile['error'])) {
                    $file_url = $movefile['url'];
                    $file_path = $movefile['file'];
                    $data['stock_csv'] = sanitize_text_field($file_url);
                    $data['stock_csv_path'] = sanitize_text_field($file_path);
//                    var_dump($movefile);exit();
                    updateStockByFile($file_url, $send_email, $data['emails'], $file_path);

                } else {
                    echo "<div class='error notice'>" . $movefile['error'] . "</div>";
                }
                // Set everything back to normal.
                remove_filter('sanitize_file_name', 'paone_su_hash_filename', 10);
                remove_filter('upload_dir', 'paone_su_upload_dir');
            }
            if (!isset($data['stock_csv'])) {
                $old_data = get_option('paone_su_data');

                $data['stock_csv'] = esc_attr($old_data['stock_csv']);
            }
            $post_stocks = isset($_POST['stocks']) ? wp_unslash($_POST['stocks']) : array('Sku' => array(), 'Stock' => array());

            $skus = sanitize_text_field(implode("|", $post_stocks['Sku']));
            $stocks = sanitize_text_field(implode("|", $post_stocks['Stock']));
            $data['skus'] = $skus;
            $data['stocks'] = $stocks;

            //update the stock Values
            if (!isset($_POST['paone_su_checkbox']) && empty(sanitize_text_field($_POST['paone_su_checkbox']))) {

                updateStockByPost($skus, $stocks, $send_email, $data['emails']);
            }
            //Update end
            update_option('paone_su_data', $data);
            echo "<div class='notice updated'><p style='font-size: 15px;'> Stock Updated Succesfully. Please check the confirmation email. </p></div>";
        }

        /*
         * Function to fetch the saved admin fields used in the form template
         */
        protected function getPaoneSuFields()
        {
            $admin_data = array();
            $data = get_option('paone_su_data');
            if (!empty($data)) {

                $skus = esc_attr($data['skus'], '');
                $stocks = esc_attr($data['stocks'], '');
                $admin_data['stock_csv'] = esc_attr($data['stock_csv']);

            }
            $stock_data = array();
            if (!empty($stocks)) {
                $sku_list = explode("|", $skus);
                $stock_list = explode("|", $stocks);

                if (is_array($sku_list) && is_array($stock_list) && count($skus) == count($stocks)) {
                    for ($i = 0; $i < count($sku_list); $i++) {
                        $stock_data[] = array("Sku" => $sku_list[$i], 'Stock' => getProductStock($sku_list[$i]));
                    }
                }
            }
            $admin_data['stocks'] = $stock_data;
            $admin_data['send_email'] = esc_attr($data['send_email']);
            $admin_data['emails'] = esc_attr($data['emails']);
            $admin_data['last_updated_at'] = esc_attr($data['last_updated_at']);
            $admin_data['stock_csv'] = esc_attr($data['stock_csv']);
            return $admin_data;
        }
    }

    function paone_su_upload_dir($dir)
    {
        return array(
                'path' => $dir['basedir'] . '/paone_su',
                'url' => $dir['baseurl'] . '/paone_su',
                'subdir' => '/paone_su',
            ) + $dir;
    }

    function paone_su_hash_filename($filename)
    {
        $info = pathinfo($filename);
        $ext = empty($info['extension']) ? '' : '.' . $info['extension'];
        $t = time();

        return 'stock_csv-' . $t . $ext;
    }


    function paone_su_custom_mime_types($mimes)
    {
        $mimes['jpg'] = 'image/jpeg';
        $mimes['png'] = 'image/png';
        unset($mimes['exe']);

        return $mimes;
    }

    add_filter('upload_mimes', 'paone_su_custom_mime_types');
    /*
     * Update the stock values by Post Fields
     * @param $skus
     * @param $stock Values
     */
    function updateStockByPost($skus, $stocks, $send_email, $emails = '')
    {

        $skus = esc_attr($skus, '');
        $stocks = esc_attr($stocks, '');
        $sku_list = explode("|", $skus);
        $stock_list = explode("|", $stocks);
        $html = "<h3>Product List</h3><table style='text-align: center;'><tr><th width='30%'>SKU</th><td width='30%'>Stock</td></tr>";
        if (is_array($sku_list) && is_array($stock_list) && count($skus) == count($stocks)) {
            for ($i = 0; $i < count($sku_list); $i++) {
                updateProductStock($sku_list[$i], $stock_list[$i]);
                if ($send_email && is_numeric($stock_list[$i])) {
                    $html .= "<tr><td>$sku_list[$i]</td><td>$stock_list[$i]</td></tr>";
                }
            }
        }

        if (isset($send_email) && !empty($send_email))
            sendStockUpdateConfirmationEmail($emails, '', $html);
    }

    /*
     * Update the Product Stock By Sku
     * @param $sku
     * @param $stock
     */
    function updateProductStock($sku, $stock)
    {
        $product_id = wc_get_product_id_by_sku($sku);
        if (!empty($product_id) && isset($product_id)) {
            // Get an instance of the WC_Product object
            $product = new WC_Product($product_id);
            $product->set_stock_quantity($stock);
            if ($stock > 0) {
                $product->set_stock_status('instock');
            } else {
                $product->set_stock_status('outofstock');
            }
            // Save the data and refresh caches
            $product->save();
        }
    }
    function getProductStock($sku){
        $product_id = wc_get_product_id_by_sku($sku);
        $stock=0;
        if (!empty($product_id) && isset($product_id)) {
            // Get an instance of the WC_Product object
            $product = new WC_Product($product_id);
            $stock=$product->get_stock_quantity();
        }
        return $stock;
    }

    function updateStockByFile($file_url, $send_email = 0, $emails = '', $file_path)
    {
        $file = fopen($file_url, "r");
        while (!feof($file)) {
            $data = fgetcsv($file);

            $sku = $data[0];
            $stock = $data[1];
            updateProductStock($sku, $stock);
        }
        fclose($file);
        if (isset($send_email) && !empty($send_email)) {

            sendStockUpdateConfirmationEmail($emails, $file_path);
        }
    }

    function sendStockUpdateConfirmationEmail($emails, $file = "", $html = '')
    {
        if (!empty($emails)) {
            $mailer = WC()->mailer();

            $recipient = $emails;
            function get_stock_update_email_html( $heading = false, $mailer,$content)
            {

                $template = 'admin/templates/email.php';
                ob_start();
                 get_template_part($template, array(
                    'email_heading' => $heading,
                    'sent_to_admin' => false,
                    'plain_text' => false,
                    'email' => $mailer,
                    'content'=>$content
                ));
                 return ob_get_clean();

            }
            $subject = __("Stock on " . get_bloginfo('name') . " has been updated successfully!", 'Guidecraft');
            $content = 'Attached are the list of items which are updated ';//get_stock_update_email_html($subject,$mailer,$html);
            $content .= $html;
            $headers = "Content-Type: text/html\r\n";
            $mailer->send($recipient, $subject, $content, $headers, array($file));
        } else {
            return;
        }
    }

}
