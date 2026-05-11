<?php
require_once '../../_config.php';  
require_once '_include.php';
require_once '_global.php';
 
$customCode = new CustomCode();

$obj = null;

$module = explode(',',$_GET['module']);


$RETURN_VALUE =array();

foreach($module as $modulename){

    switch ($modulename){ 
        case 'customer':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';  
                            $obj = new Customer();

                         break;
        case 'customerCategory':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';  
                            $obj = new CustomerCategory();

                         break;
        case 'supplier':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';  
                            $obj = new Supplier();

                         break;
        case 'employee':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';  
                            $obj = new Employee();

                         break;
        case 'purchaseOrder':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PurchaseOrder.class.php';  
                            $obj = new PurchaseOrder();

                         break;
        case 'salesOrder':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';  
                            $obj = new SalesOrder();

                         break;
        case 'itemUnit':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';  
                            $obj = new ItemUnit();

                         break;
        case 'brand':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';  
                            $obj = new Brand();

                         break;
        case 'warehouse':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';  
                            $obj = new Warehouse();

                         break;
        case 'item':    
                            require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';  
                            $obj = new Item();

                         break;
    }

    if($obj != null){ 
        $arrStatus = array();
        
        $rsStatus = $obj->getAllStatus(); 
        foreach($rsStatus as $row) 
            array_push($arrStatus, array('key' => $row['pkey'],'status_name' =>  $row['status'], 'color' =>  $row['textcolor']));
      
        $rsCustomCode = array();
          
        $rsKey = $obj->getTableKeyAndObj($obj->tableName,array('key')); 
        if(!empty($rsKey)){
          $rsCustomCodeTemp =  $customCode->searchData( $customCode->tableName.'.statuskey','1',true,' and '.$customCode->tableName.'.reftabletype = '. $obj->oDbCon->paramString($rsKey['key'])); 
                  foreach($rsCustomCodeTemp as $tempRow){
                            array_push($rsCustomCode, array('key' =>$tempRow['pkey'], 'custom_code_name' =>$tempRow['name']));
                  }
         }   
        
        $RETURN_VALUE[$modulename] = array(
            'statuses' => $arrStatus, 
            'is_transaction' => ($obj->isTransaction) ? 1 : 0,
            'allowed_status_to_edit' => $obj->allowedStatusForEdit,
            'custom_code' => $rsCustomCode
        );
    }

}


http_response_code(200);
echo json_encode($RETURN_VALUE); 
?>