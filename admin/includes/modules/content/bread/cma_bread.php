<?php

    class cma_bread
    {

        public $code;
        public $group;
        public $title;
        public $description;
        public $sort_order;
        public $enabled = false;

        public $root;
        public $data = [];
        public $Types = [];

        function __construct()
        {
            global $OSCOM_Hooks;
            $this->code = get_class($this);
            $this->group = basename(dirname(__FILE__));

            $this->root = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
            $this->build($this->root);

            $this->title = MODULE_ADMIN_CONTENT_BREAD_TITLE;
            $this->description = MODULE_ADMIN_CONTENT_BREAD_DESCRIPTION;

            if ( defined('MODULE_ADMIN_CONTENT_BREAD_STATUS') ) {
                $this->sort_order = MODULE_ADMIN_CONTENT_BREAD_SORT_ORDER;
                $this->enabled = (MODULE_ADMIN_CONTENT_BREAD_STATUS == 'True');
            }

            if(pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME) != 'modules_admin_content.php')
            {
            }
        }

        function execute()
        {
            global $oscTemplate;

            $action = (isset($_GET['action']) ? $_GET['action'] : '');

            if (empty($action))
            {
                $dataHeadings = json_decode(json_encode($this->Types), FALSE);
                $dataRows = json_decode(json_encode($this->browse()), FALSE);
                $dataTypes = [];

                ob_start();
                include('includes/modules/content/' . $this->group . '/templates/tpl_browse_' . basename(__FILE__));
                $template = ob_get_clean();

                $oscTemplate->addContent($template, $this->group);
            }

            if($action == 'edit')
            {

                $this->edit();
                ob_start();
                include('includes/modules/content/' . $this->group . '/templates/tpl_edit_' . basename(__FILE__));
                $template = ob_get_clean();

                $oscTemplate->addContent($template, $this->group);                
            }

            if($action == 'add')
            {
                $dataHeadings = json_decode(json_encode($this->Types), FALSE);

                $this->add();
                include('includes/modules/content/' . $this->group . '/templates/tpl_add_' . basename(__FILE__));
                $template = ob_get_clean();

                $oscTemplate->addContent($template, $this->group);                
            }

            if($action == 'delete')
            {
                $this->delete();
            }                                                
        }

        function isEnabled()
        {
            return $this->enabled;
        }

        function check()
        {
            return defined('MODULE_ADMIN_CONTENT_BREAD_STATUS');
        }

        function install()
        {
            tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable this Module', 'MODULE_ADMIN_CONTENT_BREAD_STATUS', 'True', 'Choose option', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");            
            tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Page results', 'MODULE_ADMIN_CONTENT_BREAD_MAX_RESULTS', '10', 'Page results value.', '6', '0', now())");
            tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Page Links', 'MODULE_ADMIN_CONTENT_BREAD_MAX_LINKS', '20', 'Max page links value.', '6', '0', now())");
            tep_db_query("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ADMIN_CONTENT_BREAD_SORT_ORDER', '10', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");                        
        }

        function remove()
        {
            tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
        }

        function keys()
        {
            return array('MODULE_ADMIN_CONTENT_BREAD_STATUS', 'MODULE_ADMIN_CONTENT_BREAD_MAX_RESULTS', 'MODULE_ADMIN_CONTENT_BREAD_MAX_LINKS', 'MODULE_ADMIN_CONTENT_BREAD_SORT_ORDER',);
        }

        public function build()
        {

            $Query = tep_db_query("SELECT dt.model_name, dr.data_type_id, dr.field, dr.type, dr.display_name, dr.required, dr.browse, dr.read, dr.edit, dr.add, dr.delete, dr.details, dr.order FROM data_rows dr, data_types dt WHERE dt.name = '".$this->root."' AND dt.id = dr.data_type_id ORDER BY dr.order");
            
            if (tep_db_num_rows($Query)) {

                while ($result = tep_db_fetch_array($Query)) {
                    $this->Types[] = $result;
                }
            }
        }

        public function browse()
        {
            global $OSCOM_Hooks;
                        
            $QueryRaw = $OSCOM_Hooks->call('bread', 'Browse'.ucfirst(strtolower($this->root)));
            $id = $OSCOM_Hooks->call('bread', 'getKey');

            $splitPageResults = new splitPageResults($_GET['page'], MODULE_ADMIN_CONTENT_BREAD_MAX_RESULTS, $QueryRaw, $Numrows);
            $Query = tep_db_query($QueryRaw);
            
            if (tep_db_num_rows($Query)) {

                while ($result = tep_db_fetch_array($Query)) {
                    $this->data[$result[$id]] = $result;
                }
                return $this->data;
            }
            return false;
        }

        public function edit()
        {
            global $OSCOM_Hooks;
            $unKnown = $OSCOM_Hooks->call('bread', 'Edit'.ucfirst(strtolower($this->root)));
        }

        public function add()
        {
            global $OSCOM_Hooks;
            $OSCOM_Hooks->call('bread', 'Add'.ucfirst(strtolower($this->root)));

        }

        public function delete()
        {
            global $OSCOM_Hooks;
            return $OSCOM_Hooks->call('bread', 'Delete'.ucfirst(strtolower($this->root)));
           //return $unKnown;

        }

        public function getPagination()
        {
            $Query = "select * from " . $this->root;
            $splitPageResults = new splitPageResults($_GET['page'], MODULE_ADMIN_CONTENT_BREAD_MAX_RESULTS, $Query, $Numrows);
            $totalPages = constant('TEXT_DISPLAY_NUMBER_OF_'.strtoupper($this->root));
            
            $pagination = '<nav class="mt-5">';
            $pagination .= '    <ul class="pagination float-left">' . $splitPageResults->display_count($Numrows, MODULE_ADMIN_CONTENT_BREAD_MAX_RESULTS, $_GET['page'], $totalPages) . '</ul>';
            $pagination .=      $splitPageResults->display_links($Numrows, MODULE_ADMIN_CONTENT_BREAD_MAX_RESULTS, MODULE_ADMIN_CONTENT_BREAD_MAX_LINKS, $_GET['page']);
            $pagination .= '</nav>';
            
            return $pagination;
        }
    }
?>