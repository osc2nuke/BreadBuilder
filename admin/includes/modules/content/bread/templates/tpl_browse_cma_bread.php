<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"/>
<div class="col-md-12">
    <h1><i class="fas fa-industry"></i> <?php echo HEADING_TITLE; ?></h1><hr/>
     
    <div class="table-responsive mt-3">
        <table id="dataTable" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" /></th>
                    <?php
                        foreach ($dataHeadings as $dataHeadkey => $dataHeadValue) {
                            if($dataHeadValue->browse == 1){
                                $dataTypes[$dataHeadValue->field] = $dataHeadValue->type;
                                echo '<th>'. $dataHeadValue->display_name . '</th>';
                            }
                        }
                        echo '<th>' . MODULE_ADMIN_CONTENT_BREAD_ACTION . '</th>';
                    ?>
                </tr>
            </thead>
            <tbody>                 
                <?php
                    foreach ($dataRows as $dataRowKey => $dataRowValue) {
                        echo '<tr id="'.$dataRowKey.'">';
                        echo '<td><input type="checkbox" id="'.$dataRowKey.'" /></td>';
                        foreach($dataTypes as $dataTypeKey => $dataTypeValue){
                            $dataContent = $dataRowValue->$dataTypeKey;
                            echo '<td>';
                            if($dataTypeValue == 'image'){ 
                                echo  tep_image(DIR_WS_CATALOG_IMAGES . $dataContent, null, '100', 'auto', 'img-responsive'). '<span style="display:none;">'.DIR_WS_CATALOG_IMAGES . $dataContent.'</span>';
                            }elseif($dataTypeValue == 'checkbox'){
                                //todo
                            }elseif($dataTypeValue == 'coordinates'){
                                //todo
                            }elseif($dataTypeValue == 'link'){
                            echo '<a href="' . $dataContent . '">'. (mb_strlen($dataContent) > 200 ? mb_substr($dataContent, 0, 200) . ' ...' : $dataContent) . '</a>';
                            }elseif($dataTypeValue == 'color'){
                                echo '<span class="badge badge-lg" style="background-color: ' . $dataContent . '">';
                            }elseif($dataTypeValue == 'richtextbox'){
                                echo mb_strlen($dataContent) > 200 ? mb_substr($dataContent, 0, 200) . ' ...' : $dataContent;
                            }elseif($dataTypeValue == 'textarea'){
                                echo mb_strlen($dataContent) > 200 ? mb_substr($dataContent, 0, 200) . ' ...' : $dataContent;
                            }elseif($dataTypeValue == 'text'){
                                echo mb_strlen($dataContent) > 200 ? mb_substr($dataContent, 0, 200) . ' ...' : $dataContent;
                            }else{
                                echo '<span>' . $dataContent . '</span>';
                            } 
                            echo '</td>';       
                        }
                        echo '<td><button type="button" class="btn btn-sm btn-warning"><i class="far fa-eye"></i></button> ' .
                             '<button type="button" class="btn btn-sm btn-primary"><i class="far fa-edit"></i></button> ' .
                             '<button type="button" class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button>' .
                             '</td>';
                        echo '</tr>';           
                    }
                ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->getPagination(); ?>
</div>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready( function () {
        var unsortable = $( "th" ).length -1;
        $('#dataTable').DataTable({
            "searching": false,
            "info": false,
            "paging": false,
            "order": [],
            "columnDefs": [ {
            "targets"  : [0,unsortable],
            "orderable": false,
            }],

        });
        /* checkbox selection */
        $('#selectAll').click(function (e) {
            $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
        });

        $('button.btn-danger').click(function (e) {
            ID = $(this).closest('tr').attr('id');
            $.post( 'manufacturers.php?action=delete', { id: ID });
        });    
    });
</script>