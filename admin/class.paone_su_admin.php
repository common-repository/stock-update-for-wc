<?php
/**
 * Created by PhpStorm.
 * User: pawanpriyadarshi
 * Date: 2020-01-09
 * Time: 15:51
 */
if (!class_exists('PAONE_Admin')) {

    class PaoneSuAdmin extends PaoneSuAdminModel
    {


        public function __construct()
        {
            //Add Actions

            add_action('paone_su_load_admin_settings', array($this, 'paone_su_show_fields'));
            add_action('paone_su_enqueue_scripts_admin', array($this, 'paone_su_enqueue_scripts'));


            //Do Actions
            do_action('paone_su_load_admin_settings');
            do_action('paone_su_enqueue_scripts_admin');

        }

        function paone_su_show_fields()
        {

//            include_once PAONE_WP_PATH . 'admin/helper.php';
            require_once PAONE_SU_WP_PATH . '/admin/templates/form.php';
        }

        function paone_su_enqueue_scripts()
        {
            wp_enqueue_style('admin-style', PAONE_SU_WP_URL . '/assets/css/admin-style.css');
            wp_enqueue_style('style', PAONE_SU_WP_URL . '/assets/css/style.css');
            wp_enqueue_script('jscolor', PAONE_SU_WP_URL . '/assets/js/jscolor.js');
            wp_enqueue_style('select2-css', PAONE_SU_WP_URL . '/assets/select2/css/select2.min.css');
            wp_enqueue_style('paone-su-css', PAONE_SU_WP_URL . '/assets/css/style.css');
            wp_enqueue_script('select2-js', PAONE_SU_WP_URL . '/assets/select2/js/select2.min.js', array('jquery'));
            wp_enqueue_script('jquery-validate', PAONE_SU_WP_URL . '/assets/js/jquery.validate.min.js', array('jquery'));
            wp_enqueue_script('paone-su-js', PAONE_SU_WP_URL . '/assets/js/script.js', array('jquery'));
        }
        /**
         * Used to create table input fields
         *
         * @param string $name
         *            name of the table element
         * @param string $headers
         *            An array that defines the headers to be set in the table .i.e.inshort the columns of the table input
         * @param array $data
         *            This is the data matrix .i.e. the a multidimensional array of default data to be put into the table input
         * @param array $attributes
         *            An array of attributes for the table tag, u can pass class, style etc.here
         * @param array $options
         *            An array which governs the functionling of the table input: you can pass id, afteradd javascript functions and after remove javascript functions here
         * @return string returns the HTMl output with defined parameters
         */
        function tableInput($name = "", $headers, $data = null, $attributes = array(), $options = array())
        {
            $headers = self::makeAssociative($headers);

            $id = empty ($options ['id']) ? $name : $options ['id'];
            $show_sr_no = isset ($options ['show_sr_no']) ? $options ['show_sr_no'] : false;
            $show_headers = isset ($options ['show_headers']) ? $options ['show_headers'] : true;
            $after_add_function = isset ($options ['after_add_function']) ? $options ['after_add_function'] : '';
            $after_remove_function = isset ($options ['after_remove_function']) ? $options ['after_remove_function'] : '';
            $column_attributes = isset ($options ['column_attributes']) && is_array($options ['column_attributes']) ? $options ['column_attributes'] : array();
            $add_more_html = isset ($options ['add_more_html']) ? $options ['add_more_html'] : ''; // whatever be the data, when add button is clicked, this html only should be added

            $add_button = '<a href="javascript:void(0);" style="min-width:10px;display:block;" onclick="ti_add_rows_' . $id . '();return false;" id="ti_add_more_btn_' . $id . '" title="Add more"><img src="' . PAONE_SU_WP_URL . '/assets/images/add-icon.png"  border="0" /></a>';
            $delete_button = '<a href="javascript:void(0);" style="min-width:10px;display:block;" onclick="ti_rows_cnt_' . $id . '--;$conv(this).parent().parent().remove();
						    							' . ($after_remove_function ? 'if(typeof ' . $after_remove_function . ' == \'function\')' . $after_remove_function . '();' : '') . '
															return false;" class="ti_remove_btn_' . $id . '"  title="Remove this row">
							<img src="' . PAONE_SU_WP_URL . '/assets/images/delete-icon.png" border="0" /></a>';
            $table_head = '';
            if (count($headers) > 0 && $show_headers) {
                $table_head = '<thead><tr>';
                $table_head .= $show_sr_no ? '<th>#</th>' : '';
                foreach ($headers as $key => $value)
                    $table_head .= '<th>' . $value . '</th>';
                $table_head .= '<th class="tbinputactn" ' . (@$options ['no_border'] == true ? ' style="border:0!important"' : '') . '></th>'; /* for add / delete buttons */

                $table_head .= '</tr></thead>';
            }

            $first_row = '';

            if (count($headers) > 0) {
                $first_row .= $show_sr_no ? '<td>%sr.no%</td>' : '';
                foreach ($headers as $key => $value) {
                    $class = "";
                    if ($key == "Code") {

                        $class = "jscolor";
                    }
                    $first_row .= '<td><input class="' . $class . '" type="text" name="' . $name . '[' . $key . '][]" id="' . $id . '%sr.no%_' . $key . '"  value="' . $data [0] [$key] . '"/>' . '</td>';
                }
            }
            $js_first_row = ''; // the first row that will be put in the javascription function of adding

            if (empty ($add_more_html))
                $js_first_row = $first_row;
            else {
                $js_first_row .= $show_sr_no ? '<td>%sr.no%</td>' : '';
                foreach ($add_more_html as $key => $value)
                    $js_first_row .= '<td>' . $add_more_html [$key] . '</td>';
            }
            // var_dump($add_more_html);
            /*
             * ti_rows_cnt stores the number of rows
             * ti_rows_cnt stores index
             */

            $after_add_reset_script = "";
            foreach ($headers as $key => $value)
                $after_add_reset_script .= '$conv("#' . $id . '"+ti_rows_index_' . $id . '+"_' . $key . '").val("");';
            $script = '
		   String.prototype.replaceAll = function(needle, replacement) {return this.split(needle).join(replacement||"");};			
			$conv = jQuery;
			var ti_rows_cnt_' . $id . ' = ' . count($data) . ';
			var ti_rows_index_' . $id . ' = ' . count($data) . ';
			function ti_add_rows_' . $id . '(){
				ti_rows_cnt_' . $id . '++;
				ti_rows_index_' . $id . '++;
				var action = ' . json_encode($delete_button) . ';
				var tr_html = "<tr>"+' . json_encode($js_first_row) . '+"<td class=\"tbinputactn\" ' . (@$options ['no_border'] == true ? ' style=\'border:0!important\'' : '') . '>"+action+"</td></tr>"
				$conv("#ti_last_tr_' . $id . '").before(tr_html.replaceAll("%sr.no%",ti_rows_index_' . $id . '));	
				' . $after_add_reset_script . '
				' . ($after_add_function ? 'if(typeof ' . $after_add_function . ' == "function")' . $after_add_function . '();' : '') . '			
			}
			function ti_reset_rows_' . $id . '(){
				$conv(".ti_remove_btn_' . $id . '").each(function(){
					$conv(this).trigger("click");
				});
			}';

            $script = "<script>" . $script . "</script>";


            $table_html = '
				<table name="' . $name . '_table" id="' . $id . '_table">
					' . $table_head . '
					<tbody>
						<tr>
						' . str_replace("%sr.no%", 1, $first_row);
            $table_html .= '<td></td>';
            $table_html .= '</tr>';

            for ($i = 1; $i < count($data); $i++) {
                $table_html .= '<tr>';
                $table_html .= $show_sr_no ? '<td>' . ($i + 1) . '</td>' : '';
                foreach ($headers as $key => $value) {
                    $class = "";
                    if ($key == "Code") {

                        $class = "jscolor";
                    }
                    $table_html .= '<td><input type="text" class="' . $class . '" name="' . $name . '[' . $key . '][]" id="' . $id . $i . "" . $key . '" value="' . $data [$i] [$key] . '" /></td>'; //'<td ' . (! empty ( $column_attributes [$key] ) ? (is_array ( $column_attributes [$key] ) ?  $column_attributes [$key]  : $column_attributes [$key]) : '') . '>' . str_replace ( "%sr.no%", $i + 1, $data [$i] [$key] ) . '</td>'; /* i+1 because we start table trs from 1 */
                }
                $table_html .= '<td class="tbinputactn" ' . (@$options ['no_border'] == true ? ' style="border:0!important"' : '') . '>' . $delete_button . '</td>';

                $table_html .= '</tr>';
            }
            $columns=count($headers);
            $table_html .= '<tr id="ti_last_tr_' . $id . '" style="display:none;">
						</tr>
						<tr><td colspan="'.$columns.'"></td><td class="tbinputactn" ' . (@$options ['no_border'] == true ? ' style="border:0!important"' : '') . '>' . $add_button . '</td></tr>
					</tbody>
				</table>';

            return $script . $table_html;
        }
        /*
         * Convert to Associative Array
         * @param $array
         * @return Associate $return
         */
        function makeAssociative($array)
        {
            $return = array();
            if (!is_array($array))
                return $return;

            foreach ($array as $value)
                $return [$value] = $value;

            return $return;
        }
    }

}