<?php 
require_once '../../_config.php'; 
require_once "../../_include.php";  
 
  
$rsItemCategory = $itemCategory->searchData('','',true, ' and ('.$itemCategory->tableName.'.statuskey = 1)', ' order by name asc');

$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and ('.$brand->tableName.'.statuskey = 1)', ' order by name asc'),'pkey','name'); 
$arrCategory = $class->convertForCombobox($rsItemCategory,'pkey','name'); 

?>

<form action="#" method="post">
    <?php  
        echo $class->inputSelect('selBrand', $arrBrand) . ' '.  $class->inputSelect('selCategory', $arrCategory); //. ' '. $class->inputText('textPrice');
        echo '<br><br>';
        echo $class->inputSubmit('btnSubmit', 'Submit');
    ?>
</form>


<?php
 

if(isset($_POST) && !empty($_POST['btnSubmit'])){
 
        
            $brandkey = $_POST['selBrand'];
            $categorykey = $_POST['selCategory']; 
            
            $rsBrand = $brand->getDataRowById($brandkey);
            $rsCategory = $itemCategory->getDataRowById($categorykey);
            
            echo 'Update Merk <b>'.$rsBrand[0]['name'] .'</b>,<br>Kategori <b>'.$rsCategory[0]['name'].'</b>';
            
            $sql = 'select * from item where brandkey = '.$class->oDbCon->paramString($brandkey).' and categorykey =  '.$class->oDbCon->paramString($categorykey).' order by pkey asc';
            $rs = $class->oDbCon->doQuery($sql);
             
            //$class->setLog($arrItemKey,true);
             
            echo '<br><br>';
            updateproducts($rs);
            echo '<br><br>done'; 
            
           
}

function updateProducts($rs){
    $marketplace = new Marketplace();
    $marketplace->updateProductsQOHInAllMarketplace(array_column($rs,'pkey'));
    
   // $rsMarketplace = $marketplace->searchData($marketplace->tableName.'.statuskey',1);
  
/*    echo '<ol class="item-list" >';
    foreach($rsMarketplace as $row){  
         echo '<li  relkey="'.$row['pkey'].'" ><b>'.$row['name'].'</b> <span class="status" style="margin-left : 0.5em">updating...</span></li>';  
    } 
 
    echo '</ol>';*/
}
 
?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />        
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>   
<!--<script type="text/javascript">
    jQuery(document).ready(function(){
         $(".item-list li").each(function(){   
             var itemline = $(this);
             var itemkey = $(this).attr("relkey"); 
             
               $.ajax({ 
                      url: 'ajax-marketplace.php',  
                      method : 'POST',
                      data: 'action=resynstock&itemkey='+itemkey,  
                      success: function(data){   
                           itemline.find(".status").html("<span style=\"color:#568203\">done</style>");
                       } 
                    });
          })     
    });
</script>-->
</head> 
</html>