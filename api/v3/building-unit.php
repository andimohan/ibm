<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/BuildingUnit.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';

function getNewObj()
{
    return  new BuildingUnit();
}

$OBJ = getNewObj();

$buildingUnitCategory = new BuildingUnitCategory();

$API_FIELDS = array_merge(array(
    'requestid'  =>  array('paramName' => 'request_id'),
    'code' =>   array('paramName' => 'code'),
    'block'  =>  array('paramName' => 'block', 'mandatory' => true),
    'unit'  =>  array('paramName' => 'unit', 'mandatory' => true),
    'statuskey'  =>  array('paramName' => 'status_key'), 
//    'ownerkey'  =>  array('paramName' => 'owner_id', 'ref' => array('obj' => new Customer(), 'field' => 'code'), 'return' => array('paramName' => 'ownercode')),
//    'tenantkey'  =>  array('paramName' => 'tenant_id', 'ref' => array('obj' => new Customer(), 'field' => 'code'), 'return' => array('paramName' => 'tenantcode')),
    'unitsize'  =>  array('paramName' => 'unit_size'),  
    'vanumber'  =>  array('paramName' => 'virtual_account'),   
	'categorykey' => array('paramName' => 'category_id', 'search' => array('field' => $buildingUnitCategory->tableName.'.code'), 'ref' => array('obj' => $buildingUnitCategory, 'field' => 'code' ), 'return' => array('paramName' => 'categorycode')),  
    'pricepersquare'  =>  array('paramName' => 'price_per_square'),  
), $API_FIELDS);

require_once '_process.php';
