<?php

require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemIn.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';       
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';       
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';       
 
$OBJ = new ItemIn();  
 
$itemsDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'itemkey' =>  array('paramName' => 'item_name', 'mandatory' => true, 'ref' => array('obj' =>  new Item() )), 
    'qty' =>  array('paramName' => 'qty', 'mandatory' => true),     
    'unitkey' =>  array('paramName' => 'unit_name', 'mandatory' => true, 'ref' => array('obj' =>  new ItemUnit() )), 
    'costinbaseunit'  =>  array('paramName' => 'value_in_baseunit'  ) 
);
     
$API_FIELDS = array_merge($API_FIELDS,array(
                'code' =>   array('paramName' => 'code'),  
                'trdate'  =>  array('paramName' => 'date','mandatory' => true ), 
                'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 
                'trdesc'  =>  array('paramName' => 'description'),     
                'items_detail' =>  array('paramName' => 'items_detail', 'mandatory' => true, 'dataset' => $OBJ->arrDataDetail, 'detail' =>  $itemsDetail),
            )); 
  
require_once '_process.php';
     
?>