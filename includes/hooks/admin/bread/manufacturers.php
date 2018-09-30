<?php

    class hook_admin_bread_manufacturers
    {
        public function listen_getKey()
        {
            return 'manufacturers_id';
        }

        public function listen_BrowseManufacturers()
        {
            global $languages_id;
            $Query_raw = "select * from manufacturers m, manufacturers_info mi WHERE m.manufacturers_id = mi.manufacturers_id AND mi.languages_id = '".$languages_id."' order by manufacturers_name";
            return $Query_raw;
        }

        public function listen_ReadManufacturers()
        {
            global $languages_id;
            if (isset($_GET['id'])) $id = tep_db_prepare_input($_GET['id']);

            $Query_raw = "select * from manufacturers m, manufacturers_info mi WHERE m.manufacturers_id = '".$id."' AND mi.manufacturers_id = '".$id."' AND mi.languages_id = '".$languages_id."'";
            return $Query_raw;
        }

        public function listen_EditManufacturers()
        {
            if (isset($_GET['id'])) $id = tep_db_prepare_input($_GET['id']);

            $sql_data_array = [
                'manufacturers_name' => tep_db_prepare_input($_POST['manufacturers_name']),
                'last_modified' => 'now()',
            ];

            tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . (int)$id . "'");

            $this->setImage($id);           
            $this->setDescription($id);
        }

        public function listen_AddManufacturers()
        {
            if(tep_not_null($_POST)){
                $sql_data_array = [
                    'manufacturers_name' => tep_db_prepare_input($_POST['manufacturers_name']),
                    'date_added' => 'now()',
                ];

                tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
                $id = tep_db_insert_id();
                if(tep_not_null($_FILES)){
                    $this->setImage($id);
                }
                $this->setDescription($id);
            } 
        }

        public function listen_DeleteManufacturers()
        {
            
            $id = tep_db_prepare_input($_POST['id']);

            if (isset($_POST['delete_image']) && ($_POST['delete_image'] == 'on')) {
                $manufacturer_query = tep_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$id . "'");
            $manufacturer = tep_db_fetch_array($manufacturer_query);

            $image_location = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG_IMAGES . $manufacturer['manufacturers_image'];

                if (file_exists($image_location)) @unlink($image_location);
            }

            tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$id . "'");
            tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$id . "'");

            if (isset($_POST['delete_products']) && ($_POST['delete_products'] == 'on')) {
                $products_query = tep_db_query("select products_id from " . TABLE_PRODUCTS . " where manufacturers_id = '" . (int)$id . "'");
                while ($products = tep_db_fetch_array($products_query)) {
                    tep_remove_product($products['products_id']);
                }
            } else {
                tep_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . (int)$id . "'");
            }

        }

        public function setImage($id)
        {
            foreach ($_FILES as $key => $value) {
                $manufacturers_image = new upload($key);
                $manufacturers_image->set_destination(DIR_FS_CATALOG_IMAGES);

                if ($manufacturers_image->parse() && $manufacturers_image->save()) {
                    tep_db_query("update " . TABLE_MANUFACTURERS . " set manufacturers_image = '" . tep_db_input($manufacturers_image->filename) . "' where manufacturers_id = '" . (int)$id . "'");
                }
            }
        }

        public function setDescription($id)
        {
            global $action;
            $action = (isset($_GET['action']) ? $_GET['action'] : '');

            $languages = tep_get_languages();
            for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                $language_id = $languages[$i]['id'];

                $sql_data_array = [
                    'manufacturers_url' => tep_db_prepare_input($_POST['manufacturers_url'][$language_id]),
                    'manufacturers_description' => tep_db_prepare_input($_POST['manufacturers_description'][$language_id]),
                    'manufacturers_seo_description' => tep_db_prepare_input($_POST['manufacturers_seo_description'][$language_id]),
                    'manufacturers_seo_keywords' => tep_db_prepare_input($_POST['manufacturers_seo_keywords'][$language_id]),
                    'manufacturers_seo_title' => tep_db_prepare_input($_POST['manufacturers_seo_title'][$language_id]),
                ];

                if ($action == 'add') {
                    $insert_sql_data = [
                        'manufacturers_id' => $id,
                        'languages_id' => $language_id,
                    ];

                    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

                    tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);

                } elseif ($action == 'edit') {

                    tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . (int)$id . "' and languages_id = '" . (int)$language_id . "'");
                }
            }

            if (USE_CACHE == 'true') {
                tep_reset_cache_block('manufacturers');
            }
        }

    }    