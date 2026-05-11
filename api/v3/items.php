<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemMovement.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemChecklist.class.php';   

if($class->isActiveModule('marketplace'))  
	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Marketplace.class.php';   


function getNewObj(){
   return new Item();
}

$OBJ = getNewObj();
$itemUnit = new ItemUnit();
$brand = new Brand();
$itemCategory = new ItemCategory();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$SN = array( 
    'pkey' => array('paramName' => 'key'),   
    'serialnumber' => array('paramName' => 'serial_number'),
    'warehousename' => array('paramName' => 'warehouse'),
    'itemindate' => array('paramName' => 'item_in_date'),
    'costinbaseunit' => array('paramName' => 'cogs'),
);


$availableUnit = array( 
    'pkey' => array('paramName' => 'key'),
    'conversionunitkey' => array('paramName' => 'conversion_unit_key'),
    'sellingprice' => array('paramName' => 'selling_price'),
    'conversionmultiplier' => array('paramName' => 'conversion_multiplier'),
    'conversionunitkey'  =>  array('paramName' => 'unit_id', 'mandatory' => true, 'ref' => array('obj' => $itemUnit, 'field' => 'code' ), 'return' => array('paramName' => 'unitcode')),
    'unitname' => array('paramName' => 'unit_name'),
);
$arrCondition = array('baru' => '8001', 'pernah digunakan' => '8002'); 

// name from each keya
$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'), 
    'barcode' =>   array('paramName' => 'barcode'), 
    'name'  =>  array('paramName' => 'name', 'mandatory' => true,'search' => array('field' => $OBJ->tableName.'.name')),  
    'conditionkey'  =>  array('paramName' => 'condition',  'mandatory' => true, 'ref' => array('dataset' => $arrCondition), 'return' => array('paramName' => 'conditionname')), 
    'categorykey' => array('paramName' => 'category_id', 'mandatory' => true,'search' => array('field' => $itemCategory->tableName.'.code'), 'ref' => array('obj' => $itemCategory, 'field' => 'code' ), 'return' => array('paramName' => 'categorycode')), 
    'categoryname' => array('paramName' => 'category_name','updatable' => false),

    // pake id saja, agar gk salah
    //'categoryname'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => $itemCategory)), 
    'weightunit'  =>  array('paramName' => 'weight_unit_name','updatable' => false,  'ref' => array('obj' => $itemUnit),  'return' => array('paramName' => 'weightunitname')),
    'weightunitkey'  =>  array('paramName' => 'weight_unit_id', 'mandatory' => true,  'ref' => array('obj' => $itemUnit, 'field' => 'code' ),  'return' => array('paramName' => 'weightunitcode')),
    'baseunit'  =>  array('paramName' => 'base_unit_name', 'updatable' => false, 'ref' => array('obj' => $itemUnit), 'return' => array('paramName' => 'baseunitname')),
    'baseunitkey'  =>  array('paramName' => 'base_unit_id', 'mandatory' => true, 'ref' => array('obj' => $itemUnit, 'field' => 'code' ), 'return' => array('paramName' => 'baseunitcode')),
    // nanti kalo ad yg perlu baru direturn parentnya
    //'parent'  =>  array('paramName' => 'parent', 'ref' => array('obj' => $OBJ), 'return' => array('paramName' => 'parentname')),
    'parentkey'  =>  array('paramName' => 'parent_id', 'return' => array('isReturn' => false), 'ref' => array('obj' => $OBJ, 'field' => 'code'), 'return' => array('paramName' => 'parentcode')),
    'brand'  =>  array('paramName' => 'brand', 'ref' => array('obj' => $brand), 'return' => array('paramName' => 'brandname')),
    'brandkey'  =>  array('paramName' => 'brand_id', 'ref' => array('obj' => $brand, 'field' => 'code'), 'return' => array('paramName' => 'brandcode')),
    'sellingprice'  =>  array('paramName' => 'selling_price'),
    'originalsellingprice'  =>  array('paramName' => 'original_selling_price','updatable' => false ),
    'discpercentage'  =>  array('paramName' => 'disc_percentage','updatable' => false ),
    'hasdisc'  =>  array('paramName' => 'has_disc','updatable' => false ),
    'maxstockqty'  =>  array('paramName' => 'max_stock','defaultValue' => 0),
    'gramasi'  =>  array('paramName' => 'weight'),
    'shortdescription'  =>  array('paramName' => 'short_description'),
    'qtyonhand'  =>  array('paramName' => 'qtyonhand' ,'defaultValue' => 0),    
    'minstockqty'  =>  array('paramName' => 'min_stock' ,'defaultValue' => 0),    
    'maxstockqty'  =>  array('paramName' => 'max_stock','defaultValue' => 0),     
    'needsn'  =>  array('paramName' => 'need_sn','defaultValue' => 0),     
	'tag'  =>  array('paramName' => 'tag','search' => array('field' => $OBJ->tableName.'.tag')),     
	'searchkey'  =>  array('paramName' => 'searchkey','updatable' => false , 'return' => array('isReturn' => false), 'search' => array('field' => array($OBJ->tableName.'.name',$OBJ->tableName.'.tag'))),     

    //'item-image-uploader'  =>  array('paramName' => 'item_image_uploader','forceParam' => true, 'return' => array('isReturn' => false)),   
    //'token-item-image-uploader'  =>  array('paramName' => 'token_item_image_uploader','forceParam' => true, 'return' => array('isReturn' => false)),     

    'statuskey'  =>  array('paramName' => 'status_key'), 
    'serial_number' => array('paramName' => 'serial_number',  'detail' =>  $SN), 
    'available_unit' => array('paramName' => 'available_unit',  'detail' =>  $availableUnit), 
    'image_url' => array('paramName' => 'image_url',  'detail' =>  $imageUrl), // kalo jenis image harus diconvert ke token, dan image harus diupload ke _temp
));

$IMAGE_SET = array(); // image set bisa lebih dr 1 jenis
array_push($IMAGE_SET,array(
                                'paramName' => 'image_url',
                                'paramImageList' => 'item_image_uploader',
                                'paramToken' => 'token_item_image_uploader',
                            ));
require_once '_process.php';

?>
