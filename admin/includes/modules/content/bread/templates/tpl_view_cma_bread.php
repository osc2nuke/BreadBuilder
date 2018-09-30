<div class="table-responsive mt-3">
    <table id="dataTable" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>Field Name</th>
                <th>Field Data</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($dataRows as $dataRowValue) {
                    foreach ($dataHeadings as $dataHeadkey => $dataHeadValue) {
                        if($dataHeadValue->read == 1){
                            $fieldData = $dataHeadValue->field;
                            $dataTypes[$dataHeadValue->field] = $dataHeadValue->type;

                            echo '<tr>';
                            echo '  <th scope="row">'. $dataHeadValue->display_name . '</th>';
                            echo '  <td>'.$dataRowValue->$fieldData.'</td>';
                            echo '</tr>';
                        }
                    }
                }
            ?>
        </tbody>
    </table>
</div>
<nav>
    <ul class="pagination float-right">
        <?php echo tep_draw_button(IMAGE_BACK, 'close', tep_href_link('manufacturers.php', (isset($_GET['page']) ? 'page=' . $_GET['page'] : ''))); ?>
    </ul>
</nav>
