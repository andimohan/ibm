<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Asset.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/AssetCategory.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/AssetGroup.class.php';

function getNewObj()
{
    return  new Asset();
}

$OBJ = getNewObj();


$API_FIELDS = array_merge(array(
    'requestid'  =>  array('paramName' => 'request_id'),
    'code' =>   array('paramName' => 'code'),
    'name'  =>  array('paramName' => 'name', 'mandatory' => true),
    'warehousename'  =>  array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'warehousekey'  =>  array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => new Warehouse(), 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
    'statuskey'  =>  array('paramName' => 'status_key'), 
    'acquisitiondate'  =>  array('paramName' => 'acquisition_date', 'return' => array('format' => 'mktime')),
    'acquisitionvalue'  =>  array('paramName' => 'acquisition_value'), 
    'bookvalue'  =>  array('paramName' => 'book_value', 'updatable' => false), 
    'initdepreciationvalue'  =>  array('paramName' => 'init_depreciation_value'), 
    'usefullife'  =>  array('paramName' => 'useful_life', 'updatable' => false),
    'categoryname'  =>  array('paramName' => 'category_name', 'updatable' => false, 'return' => array('paramName' => 'categoryname')),
    'categorykey'  =>  array('paramName' => 'category_id', 'mandatory' => true, 'ref' => array('obj' => new AssetCategory(), 'field' => 'code'), 'return' => array('paramName' => 'categorycode')),
   // 'assetgroupname'  =>  array('paramName' => 'asset_group_name', 'updatable' => false, 'return' => array('paramName' => 'assetgroupname')),
    //'assetgroupkey'  =>  array('paramName' => 'asset_group_id', 'mandatory' => true, 'ref' => array('obj' => new AssetGroup(), 'field' => 'code'), 'return' => array('paramName' => 'assetgroupname')),
    'explicensedate'  =>  array('paramName' => 'exp_license_date', 'updatable' => false, 'return' => array('format' => 'mktime')),
), $API_FIELDS);

require_once '_process.php';
