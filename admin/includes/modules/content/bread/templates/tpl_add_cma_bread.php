<div class="table-responsive mt-3">
    <table id="dataTable" class="table table-striped table-bordered table-hover">
        <?php   
            echo tep_draw_form('manufacturers', pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME), 'action=add', 'post', 'enctype="multipart/form-data"');
        ?>
        <thead>
            <tr>
                <th>Field Name</th>
                <th>Field Data</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($dataHeadings as $dataHeadkey => $dataHeadValue) {
                    if($dataHeadValue->add == 1){
                        $fieldData = $dataHeadValue->field;
                        $dataTypes[$dataHeadValue->field] = $dataHeadValue->type;
                        echo '<tr>';
                        echo '  <th scope="row">'. $dataHeadValue->display_name . '</th>';
                        echo '<td>';
                        if($dataHeadValue->type == 'image'){
                            echo tep_draw_file_field($dataHeadValue->field);
                        }elseif($dataHeadValue->type == 'checkbox'){
                            if($dataHeadValue->required == 1){
                                $languages = tep_get_languages();
                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                    echo '<div class="input-group mb-3">';
                                    echo '  <div class="input-group-prepend">';
                                    echo '    <span class="input-group-text" id="basic-addon1">'.tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).'</span>';
                                    echo '  </div>';
                                    echo tep_draw_input_field(''.$dataHeadValue->field.'[' . $languages[$i]['id'] . ']');
                                    echo '</div>';
                                }
                            }else{
                                echo tep_draw_input_field($dataHeadValue->field);
                            }
                        }elseif($dataHeadValue->type == 'coordinates'){
                            if($dataHeadValue->required == 1){
                                $languages = tep_get_languages();
                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                    echo '<div class="input-group mb-3">';
                                    echo '  <div class="input-group-prepend">';
                                    echo '    <span class="input-group-text" id="basic-addon1">'.tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).'</span>';
                                    echo '  </div>';
                                    echo tep_draw_input_field(''.$dataHeadValue->field.'[' . $languages[$i]['id'] . ']');
                                    echo '</div>';
                                }
                            }else{
                                echo tep_draw_input_field($dataHeadValue->field);
                            }
                        }elseif($dataHeadValue->type == 'link'){
                            if($dataHeadValue->required == 1){
                                $languages = tep_get_languages();
                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                    echo '<div class="input-group mb-3">';
                                    echo '  <div class="input-group-prepend">';
                                    echo '    <span class="input-group-text" id="basic-addon1">'.tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).'</span>';
                                    echo '  </div>';
                                    echo tep_draw_input_field(''.$dataHeadValue->field.'[' . $languages[$i]['id'] . ']');
                                    echo '</div>';
                                }
                            }else{
                                echo tep_draw_input_field($dataHeadValue->field);
                            }
                        }elseif($dataHeadValue->type == 'color'){
                            if($dataHeadValue->required == 1){
                                $languages = tep_get_languages();
                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                    echo '<div class="input-group mb-3">';
                                    echo '  <div class="input-group-prepend">';
                                    echo '    <span class="input-group-text" id="basic-addon1">'.tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).'</span>';
                                    echo '  </div>';
                                    echo tep_draw_input_field(''.$dataHeadValue->field.'[' . $languages[$i]['id'] . ']');
                                    echo '</div>';
                                }
                            }else{
                                echo tep_draw_input_field($dataHeadValue->field);
                            }
                        }elseif($dataHeadValue->type == 'textarea_rich'){
                            if($dataHeadValue->required == 1){
                                $languages = tep_get_languages();
                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                    echo '<div class="input-group mb-3">';
                                    echo '  <div class="input-group-prepend">';
                                    echo '    <span class="input-group-text" id="basic-addon1">'.tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).'</span>';
                                    echo '  </div>';
                                    echo tep_draw_textarea_field(''.$dataHeadValue->field.'[' . $languages[$i]['id'] . ']', 'soft', '40', '10');
                                    echo '</div>';
                                }
                            }else{
                                echo tep_draw_textarea_field($dataHeadValue->field, 'soft', '40', '10');
                            }
                        }elseif($dataHeadValue->type == 'textarea'){
                            if($dataHeadValue->required == 1){
                                $languages = tep_get_languages();
                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                    echo '<div class="input-group mb-3">';
                                    echo '  <div class="input-group-prepend">';
                                    echo '    <span class="input-group-text" id="basic-addon1">'.tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).'</span>';
                                    echo '  </div>';
                                    echo tep_draw_textarea_field(''.$dataHeadValue->field.'[' . $languages[$i]['id'] . ']', 'soft', '40', '10');
                                    echo '</div>';
                                }
                            }else{
                                echo tep_draw_textarea_field($dataHeadValue->field, 'soft', '40', '10');
                            }
                        }elseif($dataHeadValue->type == 'text'){
                            if($dataHeadValue->required == 1){
                                $languages = tep_get_languages();
                                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                    echo '<div class="input-group mb-3">';
                                    echo '  <div class="input-group-prepend">';
                                    echo '    <span class="input-group-text" id="basic-addon1">'.tep_image(tep_catalog_href_link('includes/languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], '', 'SSL'), $languages[$i]['name']).'</span>';
                                    echo '  </div>';
                                    echo tep_draw_input_field(''.$dataHeadValue->field.'[' . $languages[$i]['id'] . ']');
                                    echo '</div>';
                                }
                            }else{
                                echo tep_draw_input_field($dataHeadValue->field);
                            }
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }
            ?>
            </tbody>
            <tfoot>
                <tr>
                <td>
                        <?php echo tep_draw_button(IMAGE_BACK, 'close', tep_href_link(pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME), (isset($_GET['page']) ? 'page=' . $_GET['page'] : ''))); ?>
                </td>
                <td class="text-right">
                        <?php echo tep_draw_button(IMAGE_SAVE, 'disk', null, 'primary'); ?>
                </td>                
            </tr>
            </tfoot>
        </form>
    </table>
</div>

<script type="text/javascript">
$("form").submit(function(event){
    event.preventDefault();
    
    $.ajax({
        url: '<?php echo pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME); ?>?action=add',
        type: 'POST',
        data: new FormData( this ),
        processData: false,
        contentType: false,
        success: function(data) {
            //$("#result").html(data);
            console.log(data);
        }
    });
  
});

</script>
