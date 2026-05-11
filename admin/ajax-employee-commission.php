<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('EmployeeCommission.class.php');
$employeeCommission = createObjAndAddToCol(new EmployeeCommission()); 
$obj = $employeeCommission;

$fieldValue = array('code');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
	switch ( $_GET['action']){ 

        case 'generateDataEmployeeCommission':

            $obj = $employeeCommission; 
            $arrParam = array();
            $arrParam['trDate'] = date('d / m / Y');

            $arrParam['periodDate'] =date('d / m / Y',strtotime($_GET['period']));
            $arrParam['endDate'] = date('t / m / Y 23:59',strtotime($_GET['endperiod']));
            $arrParam['employeekey'] = $_GET['employeekey'];
        
            $rsData = $obj->getEmployeeCommissionData($arrParam);
            
            echo json_encode($rsData);  
            break;

        case 'searchJobOrderData':
                    
            $order = 'order by '.$obj->tableEMKLJobOrderHeader.'.pkey asc'; 
            $term = ''; 
            $criteria = ''; 

            $arrParam = array();
            $arrParam['trDate'] = date('d / m / Y');
            $arrParam['periodDate'] =date('d / m / Y',strtotime($_GET['period']));
            $arrParam['endDate'] = date('t / m / Y 23:59',strtotime($_GET['endperiod']));
            $arrParam['employeekey'] = $_GET['employeekey'];

            $arrCriteria = array();
            if (isset($_GET['term']) && !empty($_GET['term'])){
                array_push ($arrCriteria, '('.$obj->tableEMKLJobOrderHeader.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
            }


            if(isset($_GET['limit']) && !empty($_GET['limit']) && is_numeric($_GET['limit'])) {
                $order .= ' limit ' . $_GET['limit'];
            }

            $criteria = implode(' and ', $arrCriteria);        
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            
            $rsData = $obj->getEmployeeCommissionData($arrParam,$criteria,$order );

            $rsData = (!empty($rsData)) ? $rsData[0]['detail'] : array();

            echo json_encode($rsData);
            break;

	}
}

if (isset($_POST) && !empty($_POST['action'])) {
    switch ($_POST['action']) {
       case 'calculateEmployeeCommission':
            
            $obj = $employeeCommission; 
            
            $arrParam = array();
            $arrParam['trDate'] = date('d / m / Y');
            $arrParam['periodDate'] = date('t / m / Y 23:59',strtotime( $_POST['periodDate']));
            
            if(!empty($_POST['selSales']))
                $arrParam['employeekey'] = $_POST['selSales'];
                
            $rsData = $obj->generateEmployeeCommission($arrParam);

           break;
 
    }
}

die;
