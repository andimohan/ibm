<?php

include_once '../../_config.php'; 
include_once '../../_include.php'; 

$arrModule = array();
$arrModule['updatesql.php'] = 'Update SQL'; 
//$arrModule['importitemmovement-row.php'] = 'Pergerakan Barang (baris)';
//$arrModule['importitemmovement-column.php'] = 'Pergerakan Barang (kolom)'; 
//$arrModule['checkpromolazada.php'] = 'Cek Harga Promo Lazada';

if(!$security->isAdminLogin('SecurityPrivileges',10,true)); 

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-font-awesome.min.css">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>  

<script>
    jQuery(document).ready(function(){  
        
        $( "#form-import" ).submit(function( event ) {
             
            var modulefile = $("[name=selModule]").val(); 
            var form = $(this).closest("form");
                
            form.prop("action" , modulefile );
           
         });
        
    }) 
    
    
function updateChkBoxOnClick(obj){   
    var chkValue = $(obj).prop("checked") ? 1 : 0;
    $(obj).val(chkValue); 
    $(obj).next().val(chkValue); 
} 

function updateChkBoxOnChange(obj){   
    
    var checked = "",chkValue = 0;
    
    if($(obj).val() == 1){
        checked = "checked";
        chkValue = 1;
    } 
    
    $(obj).prev().prop("checked",checked).change(); // dont use click !
    $(obj).prev().val(chkValue);
}

function updateChkPick(obj,onChangeFunc){ 
    var obj = $(obj);  
    var container = obj.closest(".mnv-checkbox-group");
    
    if (obj.attr("relignore"))
        return; 

    var chkPick = container.find("[name='chkPick[]']:enabled"); 

    chkPick.prev().attr("relignore", true); 
    chkPick.val(obj.next().val()).change();
    chkPick.prev().removeAttr("relignore"); 
 
    // cukup sekali, gk perlu setiap klik detail dihitung ulang 
    if(onChangeFunc) onChangeFunc();
}
  
</script>    
    
<title>Upload Template</title>  
</head> 
<body>    
    
<div style="padding: 1em">  
    <form action="importchassis.php" method="post" enctype="multipart/form-data" id="form-import"> 
        <div class="div-table">
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">Modul</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputSelect('selModule', $arrModule); ?></div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">File</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><input type="file" name="fileToUpload"></div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">Reset Data</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputCheckBox('chkReset'); ?></div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">Token</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputText('token'); ?></div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputSubmit('btnSubmit','Upload'); ?></div>
            </div>
        </div> 
    </form>
</div>  
    
<div id="result-panel" style="border:1px solid #333; height: 400px;"></div>    
</body> 
</html> 
