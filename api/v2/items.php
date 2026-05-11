<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemMovement.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Marketplace.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemChecklist.class.php';     
 
$OBJ = new Item();
$itemUnit = new ItemUnit();

//$arrCondition = array('8001' => 'Baru', '8002' => 'Pernah Digunakan');
$arrCondition = array('baru' => '8001', 'pernah digunakan' => '8002');

$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code'), 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),      
               'categorykey'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => new ItemCategory())),  
               'brandkey'  =>  array('paramName' => 'brand_name', 'ref' => array('obj' => new Brand())),    
               'conditionkey'  =>  array('paramName' => 'condition', 'ref' => array('dataset' => $arrCondition)),  
               'gramasi'  =>  array('paramName' => 'weight'),      
               'weightunitkey'  =>  array('paramName' => 'weight_unit', 'mandatory' => true,  'ref' => array('obj' => $itemUnit)),    
               'baseunitkey'  =>  array('paramName' => 'base_unit', 'mandatory' => true, 'ref' => array('obj' => $itemUnit)),    
               'minstockqty'  =>  array('paramName' => 'min_stock' ,'defaultValue' => 0),    
               'maxstockqty'  =>  array('paramName' => 'max_stock','defaultValue' => 0),    
               'sellingprice'  =>  array('paramName' => 'selling_price'),  
               'shortdescription'  =>  array('paramName' => 'short_description'),     
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') ) 
            ));
       
require_once '_process.php';
     
?>