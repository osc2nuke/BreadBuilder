<?php

    class hta_bread {
        var $code = 'hta_bread';
        var $group = 'footer_scripts';
        var $title;
        var $description;
        var $sort_order;
        var $enabled = false;
        
        public $root;
        public $data = [];
        public $Types = [];

        function __construct() {
            $this->title = MODULE_ADMIN_HEADER_TAGS_BREAD_TITLE;
            $this->description = MODULE_ADMIN_HEADER_TAGS_BREAD_DESCRIPTION;
            $this->root = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);

            if ( defined('MODULE_ADMIN_HEADER_TAGS_BREAD_STATUS') ) {
                $this->sort_order = MODULE_ADMIN_HEADER_TAGS_BREAD_SORT_ORDER;
                $this->enabled = (MODULE_ADMIN_HEADER_TAGS_BREAD_STATUS == 'True');
            }
        }

        function execute()
        {
            global $PHP_SELF, $oscTemplate, $OSCOM_Hooks;

            if (tep_not_null(MODULE_ADMIN_HEADER_TAGS_BREAD_PAGES))
            {
                $pages_array = array();

                foreach (explode(';', MODULE_ADMIN_HEADER_TAGS_BREAD_PAGES) as $page)
                {
                    $page = trim($page);

                    if (!empty($page)) {
                        $pages_array[] = $page;
                    }
                }

                if (in_array(basename($PHP_SELF), $pages_array))
                {
                    $Query = tep_db_query("SELECT dt.model_name, dr.data_type_id, dr.field, dr.type, dr.display_name, dr.required, dr.browse, dr.read, dr.edit, dr.add, dr.delete, dr.details, dr.order FROM data_rows dr, data_types dt WHERE dt.name = '".$this->root."' AND dt.id = dr.data_type_id ORDER BY dr.order");
            
                    if (tep_db_num_rows($Query))
                    {

                        while ($result = tep_db_fetch_array($Query)) {
                            $this->Types[] = $result;
                        }
                    }

                    $action = (isset($_GET['action']) ? $_GET['action'] : '');
                    
                    if($action == 'read')
                    {
                        $dataHeadings = json_decode(json_encode($this->Types), FALSE);
                        $dataRows = json_decode(json_encode($this->read()), FALSE);
                        $dataTypes = [];

                        $outputHTML .= '<div class="table-responsive mt-3">';
                        $outputHTML .= '    <table id="dataTable" class="table table-striped table-bordered table-hover">';
                        $outputHTML .= '        <thead>';
                        $outputHTML .= '            <tr>';
                        $outputHTML .= '                <th>Field Name</th>';
                        $outputHTML .= '                <th>Field Data</th>';
                        $outputHTML .= '            </tr>';
                        $outputHTML .= '        </thead>';
                        $outputHTML .= '        <tbody>';
                             
                        foreach ($dataRows as $dataRowValue) {
                            foreach ($dataHeadings as $dataHeadkey => $dataHeadValue) {
                                if($dataHeadValue->read == 1){
                                    $fieldData = $dataHeadValue->field;

                                    $outputHTML .='<tr>';
                                    $outputHTML .='  <th scope="row">'. $dataHeadValue->display_name . '</th>';
                                    $outputHTML .='  <td>'.$dataRowValue->$fieldData.'</td>';
                                    $outputHTML .='</tr>';
                                }
                            }
                        }

                        $outputHTML .= '</tbody>';
                        $outputHTML .= '    </table>';
                        echo $outputHTML;
                        exit; //important
                    }

                    if (empty($action))
                    {
                        $outputJS = ' $(".btn-warning").click(function (e) { ';
                        $outputJS .= '  e.preventDefault(); ';
                        $outputJS .= '  ID = $(this).closest("tr").attr("id"); ';
                        $outputJS .= '  $.post( \'' . pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME) . '?action=read\', { id: ID }, function( data ) {';
                        $outputJS .= '      $(".modal-body").append(data);';
                        $outputJS .= '      $("#View").modal("show");';
                        $outputJS .= '  }); ';

                        $outputJS .= '  $("#View").on("hidden.bs.modal", function (e) { ';
                        $outputJS .= '      $(".modal-body").empty(); ';
                        $outputJS .= '  }); ';

                        $outputJS .= ' }); ';

                        $oscTemplate->addBlock('<script>'. $outputJS . '</script>', $this->group);
                    }               
                }
            }
        }

        function isEnabled() {
            return $this->enabled;
        }

        function check() {
            return defined('MODULE_ADMIN_HEADER_TAGS_BREAD_STATUS');
        }

        function install() {
            tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable BREAD Module', 'MODULE_ADMIN_HEADER_TAGS_BREAD_STATUS', 'True', 'Do you want to enable the BREAD module?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
            tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Pages', 'MODULE_ADMIN_HEADER_TAGS_BREAD_PAGES', '" . implode(';', $this->get_default_pages()) . "', 'The pages to add the BREAD Scripts to.', '6', '0', 'ht_bread_show_pages', 'ht_bread_edit_pages(', now())");
            tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_HEADER_TAGS_BREAD_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
        }

        function remove() {
            tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
        }

        function keys() {
            return array('MODULE_ADMIN_HEADER_TAGS_BREAD_STATUS', 'MODULE_ADMIN_HEADER_TAGS_BREAD_PAGES', 'MODULE_ADMIN_HEADER_TAGS_BREAD_SORT_ORDER');
        }

        function get_default_pages() {
            return array('customers.php');
        }

        public function read()
        {
            global $OSCOM_Hooks;

            $QueryRaw =  $OSCOM_Hooks->call('bread', 'Read'.ucfirst(strtolower($this->root)));
            $Query = tep_db_query($QueryRaw);
            
            if (tep_db_num_rows($Query)) {
                while ($result = tep_db_fetch_array($Query)) {
                    $this->data[$result[$id]] = $result;
                }

                return $this->data;
            }
            return false;
        }


    }

    function ht_bread_show_pages($text) {
        return nl2br(implode("\n", explode(';', $text)));
    }

    function ht_bread_edit_pages($values, $key) {
        global $PHP_SELF;

        $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
        $files_array = array();
        if ($dir = @dir(DIR_FS_ADMIN)) {
            while ($file = $dir->read()) {
                if (!is_dir(DIR_FS_ADMIN . $file)) {
                    if (substr($file, strrpos($file, '.')) == $file_extension) {
                        $files_array[] = $file;
                    }
                }
            }
            sort($files_array);
            $dir->close();
        }

        $values_array = explode(';', $values);

        $output = '';
        foreach ($files_array as $file) {
            $output .= tep_draw_checkbox_field('ht_bread_file[]', $file, in_array($file, $values_array)) . '&nbsp;' . tep_output_string($file) . '<br />';
        }

        if (!empty($output)) {
            $output = '<br />' . substr($output, 0, -6);
        }

        $output .= tep_draw_hidden_field('configuration[' . $key . ']', '', 'id="htrn_files"');

        $output .= '<script>
        function htrn_update_cfg_value() {
            var htrn_selected_files = \'\';

            if ($(\'input[name="ht_bread_file[]"]\').length > 0) {
                $(\'input[name="ht_bread_file[]"]:checked\').each(function() {
                    htrn_selected_files += $(this).attr(\'value\') + \';\';
                    });

                    if (htrn_selected_files.length > 0) {
                        htrn_selected_files = htrn_selected_files.substring(0, htrn_selected_files.length - 1);
                    }
                }

                $(\'#htrn_files\').val(htrn_selected_files);
            }

            $(function() {
                htrn_update_cfg_value();

                if ($(\'input[name="ht_bread_file[]"]\').length > 0) {
                    $(\'input[name="ht_bread_file[]"]\').change(function() {
                        htrn_update_cfg_value();
                    });
                }
            });
            </script>';
        return $output;
    }
?>