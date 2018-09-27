<?php


    require('includes/application_top.php');

    $action = (isset($_GET['action']) ? $_GET['action'] : '');
    if (tep_not_null($action)) {
        switch ($action) {
        case 'save':

        $data = $_POST;
        
        foreach ($data as $dataKey => $dataValue) {
            if($dataKey == 'table'){

                /* check if exists in db */
                $Query = tep_db_query('SELECT id from data_types where name = "'.$dataValue.'"');
                if (tep_db_num_rows($Query)) {
                    $result = tep_db_fetch_array($Query);
                    $dataTypeId =  $result['id'];
                }else{

                    /* prepare new table data */
                    $data_type_value = tep_db_prepare_input($dataValue);
                    $sql_data_array = array('name' => $data_type_value,
                                            'slug' => $data_type_value,
                                            'display_name_singular' => $data_type_value,
                                            'display_name_plural' => $data_type_value,
                    );                    
                    tep_db_perform('data_types', $sql_data_array);
                    $dataTypeId = tep_db_insert_id();
                }
            }else{
                $Query = tep_db_query('SELECT id from data_rows where field = "' . $dataKey . '" AND data_type_id = "'. $dataTypeId . '"');
                $sql_data_array = array('field' => tep_db_prepare_input($dataKey));
                
                foreach ($dataValue as $fieldKey => $fieldValue) {
                    if($fieldValue == 'on'){
                        $fieldValue = 1;
                    }
                    if($fieldValue == 'off'){
                        $fieldValue = 0;
                    }                                                                                                                    
                   $sql_data_array['`'.$fieldKey.'`'] = tep_db_prepare_input($fieldValue);
                }
                /*update*/
                if (tep_db_num_rows($Query)) {

                    $result = tep_db_fetch_array($Query);
                    $dataRowId =  $result['id'];

                    tep_db_perform('data_rows', $sql_data_array, 'update', "id = '" . (int)$dataRowId . "'");
                }else{
                     /*insert*/   
                    $insert_sql_data = array('data_type_id' => $dataTypeId);
                    $sql_data_array = array_merge($insert_sql_data,$sql_data_array);

                    tep_db_perform('data_rows', $sql_data_array);
                }
            }
        }
        exit;
        break;
        }
    }
    function excludedTables(){
        $excludedTables = [
            'address_format',
            'banners_history',
            'categories_description',
            'configuration',
            'configuration_group',
            'customers_basket_attributes',
            'data_rows',
            'data_types',
            'manufacturers_info',
            'orders_products',
            'orders_products_attributes',
            'orders_products_download',
            'orders_status_history',
            'orders_total',
            'oscom_app_paypal_log',
            'products_description',
            'products_to_categories',
            'reviews_description',
            'sessions',
            'testimonials_description',
        ];
        return $excludedTables;
    }
    function comboTables(){
        $comboTables = [
            'categories'=> ['categories_description',],
            'customers'=> ['customers_info',],
            'manufacturers'=>['manufacturers_info',],
            'products'=>['products_description',],
            'reviews'=>['reviews_description',],
            'testimonials'=>['testimonials_description',],
        ];
        return $comboTables;
    }    
    $comboTable = comboTables();

    function fieldTypes(){
        $fieldTypes = [
            'Checkbox' => 'checkbox',
            'Color' => 'color',
            'Date' => 'date',
            'File' => 'file',
            'Image' => 'image',
            'Multiple Images' => 'multi_image',
            'Number' => 'number',
            'Password' => 'password',
            'Radio Button' => 'radio',
            'Rich Text Box' => 'textarea_rich',
            'Code editor' => 'textarea_code',
            'Markdown Editor' => 'textarea_markdown',
            'Select Dropdown' => 'select_menu',
            'Select Multiple' => 'select_menu_multiple',
            'Text' => 'text',
            'Text Area' => 'textarea',
            'Timestamp' => 'timestamp',
            'Hidden' => 'hidden',
            'Coordinates' => 'coordinates',
        ];
        return array_flip($fieldTypes);
    }
    $fieldTypes = fieldTypes();

    function get_dataRows($table) {
        $data = [];

        $Query = tep_db_query('select dt.model_name, dr.id,dr.data_type_id,dr.field,dr.type,dr.display_name,dr.required,dr.browse,dr.read,dr.edit,dr.add,dr.delete,dr.details,dr.order FROM data_rows dr, data_types dt WHERE dt.name = "'.$table.'" and dt.id = dr.data_type_id order by dr.order');
        if (tep_db_num_rows($Query)) {
            while ( $QueryResults = tep_db_fetch_array($Query) ) {
                $data[$QueryResults['field']] = $QueryResults;
            }
            $data = json_decode(json_encode($data), FALSE);
            return $data;
        }
        return false;        
    }

    function get_tables() {
        $data = [];
        $excludedTables = excludedTables();
        $Query = tep_db_query('show table status');
        if (tep_db_num_rows($Query)) {
            while ( $QueryResults = tep_db_fetch_array($Query) ) {
                if(!in_array($QueryResults['Name'], $excludedTables)){
                    $data[] = $QueryResults['Name'];
                }
            }
        
            return $data;
        }
        return false;        
    }
    $tables = get_tables();

    function get_columns($table) {
        $data = [];

        $Query = tep_db_query('SHOW COLUMNS FROM ' . $table);
        if (tep_db_num_rows($Query)) {
            while ( $QueryResults = tep_db_fetch_array($Query) ) {
                $data[$QueryResults['Field']] = $QueryResults['Type'];
            }
            return $data;
        }
        return false;        
    }
    require('includes/template_top.php');
?>

<h1><i class="fas fa-project-diagram"></i> Bread</h1><hr/>

<div class="accordion" id="breadBuilder">
<?php
    foreach ($tables as $table => $tableName) {
        $columns = get_columns($tableName);

        if (array_key_exists($tableName, $comboTable)) {
            foreach ($comboTable[$tableName] as $key => $value) {
                $subcolumns = get_columns($value);
            }
            $columns = array_merge($columns, $subcolumns);
        }
        $dataRows = get_dataRows($tableName);

?>
  <div class="card">
    <div class="card-title mb-0" id="_<?php echo $tableName; ?>">
      <h5 class="mb-0"> <i class="fas fa-database ml-3"></i> 
        <button class="btn btn-link ace-launcher" type="button" data-toggle="collapse" data-target="#<?php echo $tableName; ?>" aria-expanded="false" aria-controls="<?php echo $tableName; ?>">
          <?php echo $tableName; ?>
        </button>
      </h5>
    </div>

    <div id="<?php echo $tableName; ?>" class="collapse" aria-labelledby="_<?php echo $tableName; ?>" data-parent="#breadBuilder">
      <div class="card-body">
        <?php
        echo tep_draw_form($tableName, 'breadbuilder.php', '', 'post', 'class="form"');
        echo tep_draw_hidden_field('table', $tableName);
        echo '<table id="' . $tableName . '" class="table table-striped">';
        echo '<thead><tr><th>Field</th><th>Visibility</th><th>InputType</th><th>Display Name</th><th>Optional Details</th></tr></thead>';

        echo '<tbody class="sortable">';
        foreach ($columns as $column => $columnName) {
      
            echo '<tr data-sort-order="'.(isset($dataRows->$column->order) ? $dataRows->$column->order:100).'">';

            echo '<td>';
            echo '<div class="form-group">';
            echo '<label for="' . $column . '"><i class="fas fa-arrows-alt"></i> ' . $column . '</label>';
            echo '</div>';
            echo '</td>';

            echo '<td><div class="form-group form-check">
                    <input '.(isset($dataRows->$column->browse) && ($dataRows->$column->browse == 1) ? 'checked="checked"':null).' name="' . $column . '[browse]" type="checkbox" class="form-check-input" id="browse">
                    <label class="form-check-label" for="browse">Browse</label>
                  </div><div class="form-group form-check">
                    <input '.(isset($dataRows->$column->read) && ($dataRows->$column->read == 1) ? 'checked="checked"':null).' name="' . $column . '[read]" type="checkbox" class="form-check-input" id="read">
                    <label class="form-check-label" for="read">Read</label>
                  </div><div class="form-group form-check">
                    <input '.(isset($dataRows->$column->edit) && ($dataRows->$column->edit == 1) ? 'checked="checked"':null).' name="' . $column . '[edit]" type="checkbox" class="form-check-input" id="edit">
                    <label class="form-check-label" for="edit">Edit</label>
                  </div><div class="form-group form-check">
                    <input '.(isset($dataRows->$column->add) && ($dataRows->$column->add == 1) ? 'checked="checked"':null).' name="' . $column . '[add]" type="checkbox" class="form-check-input" id="add">
                    <label class="form-check-label" for="add">Add</label>
                  </div><div class="form-group form-check">
                    <input '.(isset($dataRows->$column->delete) && ($dataRows->$column->delete == 1) ? 'checked="checked"':null).' name="' . $column . '[delete]" type="checkbox" class="form-check-input" id="delete">
                    <label class="form-check-label" for="delete">Delete</label>
                  </div></td>';
            
            echo '<td>';
            echo '<select class="form-control" name="' . $column . '[type]">';
            foreach ($fieldTypes as $key => $value) {
                echo '  <option '.(isset($dataRows->$column->type) && ($dataRows->$column->type == $key) ? 'selected="selected"':null).' value="'.$key.'">'.$value.'</option>';
            }
            echo '</select>';
            echo '</td>';
            
            echo '  <td><input '.(isset($dataRows->$column->display_name) ? 'value="'.$dataRows->$column->display_name.'"':null).' class="form-control" name="' . $column . '[display_name]" type="text"></td>';
            echo '  <td><div class="json-editor" id="'.(isset($dataRows->$column->id) ? 'editor_'.$dataRows->$column->id: 'editor_1000').'">'.(isset($dataRows->$column->details) ? $dataRows->$column->details:null).'</div><input id="'.(isset($dataRows->$column->id) ? 'editor_'.$dataRows->$column->id: 'editor_1000').'" type="hidden" name="' . $column . '[details]" value="'.(isset($dataRows->$column->details) ? $dataRows->$column->details:null).'" /></td>';
            
            echo '<input type="hidden" class="sort-order" name="' . $column . '[order]" value="'.(isset($dataRows->$column->order) ? $dataRows->$column->order:100).'" '; 
            echo '</tr>';    
        }
        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr><td colspan="5"><button class="btn btn-primary float-right" type="submit">Save</button></td></tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</form>';
        ?>
      </div>
    </div>
  </div>
<?php
}
?>
</div>

<?php
  require('includes/template_bottom.php');
?>
<style>

.ace_editor {
    /** Setting height is also important, otherwise editor wont showup**/
    height: 300px;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.1/ace.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>

    $( function() {

        /* sort order table rows*/
        $('#breadBuilder').on('show.bs.collapse', function (e) {
            Active = $(e.target).attr('id');

            tb = $('table#'+Active+' tbody');
            rows = tb.find('tr');
            rows.sort(function(a, b) {
                keyA = $(a).attr('data-sort-order');
                keyB = $(b).attr('data-sort-order');
                return keyA - keyB;
            });
            $.each(rows, function(index, row) {
                tb.append(row);
            });                      
        });

        /* sortables */
        updateIndex = function(e, ui) {
            $('input.sort-order', ui.item.parent()).each(function (i) {
                $(this).val(i + 1);
            });
        };
        $( ".sortable" ).sortable({
            handle: "i.fa-arrows-alt",
            placeholder: "ui-state-highlight",
            stop: updateIndex
        });
        $( ".sortable" ).disableSelection();

        /* control checkboxes ON/OFF */
        $('form').on('change','input[type="checkbox"]',function() {
            formName = $(this).closest('form').attr('name');
            inputHidden = '<input type="hidden" name="'+this.name+'" value="off" />';
            if($('form[name="'+formName+'"] input[type="checkbox"][name="'+this.name+'"]').attr("checked") == 'checked'){
                $('form[name="'+formName+'"] input[type="checkbox"][name="'+this.name+'"]').removeAttr("checked");
                $('form[name="'+formName+'"]').append(inputHidden);
            }else{
                $('input[type="hidden"][name="'+this.name+'"]').remove();
                $('form[name="'+formName+'"] input[name="'+this.name+'"]').attr("checked", "checked");
            }
        });

        /*ace editor*/
        $('.ace-launcher').on('click', function (e) {
            launch = $(e.target).attr('data-target');

            $('table'+launch+' .json-editor').each(function(index){ 
                var id = $(this).attr('id');
                var editor = ace.edit(id);
                editor.setTheme("ace/theme/chrome");
                editor.getSession().setMode("ace/mode/json");
                editor.getSession().getAnnotations();

                editor.getSession().on("change", function (event, el) {
                    $('input[id="'+id+'"]').val(editor.getSession().getValue());
                });
            });
        });
 
        $("form").submit(function(event){
            event.preventDefault();
            $.post( 'breadbuilder.php?action=save', $( this ).serializeArray());
        });
    });
</script>
<?php
  require('includes/application_bottom.php');
?>