<?php
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);  
if (isset($_POST) && !empty($_POST['chkReset']))  resetTable($OBJ,$RESET_TABLE); 
 
// $row pertama utk judul kolom
$arrData = importData($DATA_STRUCTURE,array('datatype' => 'excel', 'worksheet' => $worksheet, 'highestRow' => $highestRow)); 
$arrData = removeUnusedParameter($arrData);

?> 
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />     
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">    
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>    
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>api.min.js"></script>    
<script type="text/javascript"> 
    jQuery(document).ready(function(){ 
        startImportData($(".item-list"),<?php echo json_encode($arrData); ?>,"<?php echo $AJAX_FILE; ?>");
    });
</script>

</head>
<body>
<div style="margin: 2em">
<h2><?php echo $TITLE; ?></h2>
<?php 
    
    $headerRow =  '<div class="div-table-row header-row">';
     
    for($i=1;$i<=$highestColumnIndex;$i++)
        $headerRow .= '  <div class="div-table-col-3">'.$worksheet->getCellByColumnAndRow($i, 1)->getValue().'</div> '; 
    
    $headerRow .= ' <div class="div-table-col-3" style="min-width:5em; text-align:center">'.$OBJ->lang['status'].'</div>';
    $headerRow .= ' <div class="div-table-col-3" style="min-width:30em">'.$OBJ->lang['description'].'</div> ';
    $headerRow .= '</div>';
     
    // FAILED RESULT 
    echo '<div class="div-table import-table import-result-failed" style="margin-bottom:2em">';
    echo '<div class="div-table-caption text-red-cardinal">'.$OBJ->errorMsg[212].'</div>';
    echo $headerRow;
    echo '</div>';
    
      
    // SUCCESS RESULT 
    echo '<div class="div-table import-table import-result-success" style="margin-bottom:2em">';
    echo '<div class="div-table-caption text-green-avocado">'.$OBJ->lang['dataHasBeenSuccessfullyUpdated'].'</div>';
    echo $headerRow;
    echo '</div>';
    
    
    // IMPORT LIST
    echo '<div class="div-table import-table"> ';
    echo '<div class="div-table-caption">'.$OBJ->lang['jobQueue'].' ...</div>';
    echo $headerRow;
    foreach($arrData as $key=>$itemRow){ 
        echo '<div class="div-table-row item-list" relkey="'.$key.'" relgroup="'.$key.'">';
        
        for($i=1;$i<=$highestColumnIndex;$i++)
          echo '<div class="div-table-col-3">'.urldecode($itemRow[$DATA_STRUCTURE[$i]['field']]['value']).'</div>';
                        
        echo ' <div class="div-table-col-3" style="text-align:center"><div class="response-code"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div> 
                    <div class="div-table-col-3"><div class="desc"><div style="text-align:center"><i class="fas fa-spinner fa-spin" style="margin-top: 2px"></i></div></div></div> 
                </div> 
       '; 
    }
    
    
    echo '</div>';    
?>
</div>    
</body>    
</html>