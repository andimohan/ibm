<?php 
require_once '../../_config.php'; 
require_once "../../_include.php";  
  

?>

<form action="#" method="post">
    <?php  
  
        echo $class->inputSubmit('btnSubmit', 'Submit');
    ?>
</form>


<?php
 

if(isset($_POST) && !empty($_POST['btnSubmit'])){
  
            $sql = 'select * from item';
            
            $rs = $class->oDbCon->doQuery($sql);  
             
            echo '<br><br>';
            updateproducts($rs);
            echo '<br><br>done'; 
            
           
}

function updateProducts($rs){
    $marketplace = new Marketplace();
    $marketplace->updateProductsQOHInAllMarketplace(array_column($rs,'pkey')); 
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