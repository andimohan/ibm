<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderCategory.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Car.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/JobProgress.class.php';

function getNewObj(){ 
    return new TruckingServiceWorkOrder(); 
}

$OBJ = getNewObj();

$truckingServiceOrder = new TruckingServiceOrder();
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();
$customer = new Customer();
$jobProgress = new JobProgress();

$jobProgressDetail = array( 
    'pkey' => array('paramName' => 'key'),
    'refkey' => array('paramName' => 'trucking_service_work_order_key'),
    'jobprogresskey' => array('paramName' => 'job_progress_key'),
    'jobprogressheaderkey' => array('paramName' => 'job_progress_header_key'),
    'jobprogresscode' => array('paramName' => 'job_progress_id', 'mandatory' => true, 'ref' => array('obj' => $jobProgress, 'field' => 'code'), 'return' => array('paramName' => 'jobprogresscode')),
    'jobprogressname' => array('paramName' => 'job_progress_name', 'mandatory' => true, 'ref' => array('obj' => $jobProgress, 'field' => 'name'), 'return' => array('paramName' => 'jobprogressname')),
    'number' => array('paramName' => 'number'),
    'needpod' => array('paramName' => 'need_pod'),
    'completeddate' => array('paramName' => 'completed_date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'iscompleted' => array('paramName' => 'is_completed','mandatory' => true),
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'spk_code'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'stuffingdatetime' => array('paramName' => 'stuffingdate', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => new Warehouse(), 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
    'refkey' => array('paramName' => 'sales_order_key'),
    'serviceordercode' => array('paramName' => 'sales_order_id', 'mandatory' => true, 'ref' => array('obj' => new TruckingServiceOrder, 'field' => 'code'), 'return' => array('paramName' => 'serviceordercode')),
    //'categorykey' => array('paramName' => 'sales_order_category_id'),
    //'categoryname' => array('paramName' => 'sales_order_category_name', 'mandatory' => true,'return' => array('paramName' => 'categoryname')),
    //'customerkey' => array('paramName' => 'customer_id'),
    //'customername' => array('paramName' => 'customer_name', 'updatable' => false, 'return' => array('paramName' => 'customername')),
    //'carkey' => array('paramName' => 'car_key'),
    //'policenumber' => array('paramName' => 'police_number', 'ref' => array('obj' => $car, 'field' => 'code'), 'return' => array('paramName' => 'policenumber')),
    'jobtypename' => array('paramName' => 'job_type_name'),
    'job_progress_detail' => array('paramName' => 'job_progress_detail', 'dataset' => $OBJ->arrJobProgressDetail, 'detail' => $jobProgressDetail),
));


require_once '_process.php';

?>