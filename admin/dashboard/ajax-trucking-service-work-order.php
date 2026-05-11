<?php
require_once '../../_config.php';
require_once '../../_include-v2.php';

includeClass(array('TruckingServiceWorkOrder.class.php', 'Employee.class.php'));
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$employee = new Employee();
$car = new Car();
$obj = $truckingServiceWorkOrder;

if (isset($_GET) && !empty($_GET['action'])) {

   switch ($_GET['action']) {
      case 'getData' :

         $order = 'order by ' . $obj->tableName . '.code asc';

         $arrCriteria = array();
         array_push($arrCriteria, '(' . $obj->tableName . '.statuskey in(1,2) )');

         if (isset($_GET) && !empty($_GET['startdate']) && !empty($_GET['enddate'])) {

            $dateDiff = $obj->dateDiff($_GET['startdate'], $_GET['enddate']);
            if ($dateDiff < 0)
               $_GET['enddate'] = $_GET['startdate'];
            array_push($arrCriteria, $obj->tableName . '.trdate between ' . $obj->oDbCon->paramString($_GET['startdate']) . ' AND ' . $obj->oDbCon->paramString($_GET['enddate'] . ' 23:59:59'));
         }

         if (isset($_GET) && !empty($_GET['warehousekey'])) { 
            array_push($arrCriteria, $obj->tableName . '.warehousekey  = ' . $obj->oDbCon->paramString($_GET['warehousekey']) );
         }
         if (isset($_GET) && !empty($_GET['code'])) { 
            array_push($arrCriteria, $obj->tableName . '.code  = ' . $obj->oDbCon->paramString($_GET['code']) );
         }
         
         $criteria = implode(' and ', $arrCriteria);
         $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

         $rs = $obj->getDataForWorkOrderUpdate($criteria, $order);

         echo json_encode($rs);
      break;

      //case 'getDataDriver':
//
      //   $arrCriteria = array();
      //   array_push($arrCriteria, '(' . $employee->tableName . '.statuskey in(2) )');
//
      //  // if (isset($_GET) && !empty($_GET['name'])) { 
      //   //    array_push($arrCriteria, $employee->tableName . '.code  = ' . $obj->oDbCon->paramString($_GET['name']).' or '.$employee->tableName . '.attendanceid  = ' . $obj->oDbCon->paramString($_GET['name']) );
      //   // }
//
      //   if (isset($_GET) && !empty($_GET['driverkey'])) { 
      //      array_push($arrCriteria, $employee->tableName . '.pkey  = ' . $obj->oDbCon->paramString($_GET['driverkey']).' ');
      //   }
//
//
      //   $criteria = implode(' and ', $arrCriteria);
      //   $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';
//
      //   $rs = $employee->searchData('', '', true, $criteria);
//
      //   echo json_encode($rs);
      break;

      case 'getDataCar':

         $arrCriteria = array();
         array_push($arrCriteria, '(' . $car->tableName . '.statuskey in (1) )');

         if (isset($_GET) && !empty($_GET['policenumber'])) { 
            array_push($arrCriteria, $car->tableName . '.code  = ' . $obj->oDbCon->paramString($_GET['policenumber']).' or '. $car->tableName . '.policenumber  = ' . $obj->oDbCon->paramString($_GET['policenumber']) );
         }

         if (isset($_GET) && !empty($_GET['driverKey'])) { 
            array_push($arrCriteria, $car->tableName . '.driverkey  = ' . $obj->oDbCon->paramString($_GET['driverKey']));
         }

         $criteria = implode(' and ', $arrCriteria);
         $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

         $rs = $car->searchData('', '', true, $criteria);

         echo json_encode($rs);
      break;

      case 'getDataRowById':
         $pkey = 0;
         if (isset($_GET) && !empty($_GET['pkey'])) {
            $pkey = $_GET['pkey'];
         }

         $rs = $obj->searchData('', '', true, ' and ' . $obj->tableName.'.pkey = ('. $obj->oDbCon->paramString($pkey) .') ');

         echo json_encode($rs);
      break;
   }

}

	if (isset($_POST) && !empty($_POST['action'])) { 
	   switch ($_POST['action']) {
		  case 'updateWorkOrder':	
			   						 if(!$security->isAdminLogin($obj->securityObject,11)) die; 

									  $arrData = array();
									  if (isset($_POST) && !empty($_POST['data'])) {
										 $arrData = $_POST['data'];
									  }

									  $result = $obj->updateWorkOrder($arrData,true);

									  echo json_encode($result);

									  break;
	   }
	}

die;


?>
