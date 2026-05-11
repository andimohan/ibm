<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('CashBank.class.php'));
$cashBank = new CashBank();

$obj = $cashBank;   
$fieldValue = $obj->tableName.'.code'; 
include 'ajax-general.php';

    
if (isset($_GET) && !empty($_GET['action'])) {
        switch ( $_GET['action']){  

            case 'getAvailableVoucher' : 

                $criteria = '';
				$isUnknownSource = true;
				

		//		$recipientType = 1; => customer
		//		$recipientType = 2; => supplier
		//		$recipientType = 3; => employee
				
                if(isset( $_GET['customerkey'] )){
                    $fieldName =  'customerkey';
                    $recipientType = 1;
//                    $isUnknownSource =   false; // lupa knp dibuat false, alhasil gk bisa muncul di AR/AP Payment
                }else if(isset( $_GET['supplierkey'] )){
                    $fieldName = 'supplierkey';
                    $recipientType = 2;
//                    $isUnknownSource = false; // lupa knp dibuat false, alhasil gk bisa muncul di AR/AP Payment
                }else if(isset( $_GET['employeekey'] )){
                    $fieldName = 'employeekey';
                    $recipientType = 3;
//                    $isUnknownSource = false;
                }
                	       
                if (isset($_GET['currencykey']) && !empty($_GET['currencykey'])){  
                    $criteria .=  ' and '.$obj->tableName.'.currencykey = '.$obj->oDbCon->paramString($_GET['currencykey']);
                }
                     
                if (isset($_GET['creditType']) && !empty($_GET['creditType'])){  
                    $criteria .=  ' and '.$obj->tableName.'.credittype = '.$obj->oDbCon->paramString($_GET['creditType']);
                }
                    
                if (!isset($_GET) || empty($_GET[$fieldName]))  die;
                $rs = $obj->getAvailableVoucher($_GET[$fieldName],$criteria,$isUnknownSource,$recipientType);
                $totalRs = count($rs);
                
                for($i=0;$i<$totalRs;$i++)
                    $rs[$i]['description'] = $class->formatNumber($rs[$i]['outstanding']) . ' ('.$rs[$i]['code'].')' ;
                    
                echo json_encode($rs); 
                break;
          
            case 'getStartingBalance' : 
                    if(empty($_GET['coakey']))  die;


                    $rsData = $obj->getStartingBalance($_GET['coakey'],$_GET['startdate'],$_GET['enddate']);
                    echo json_encode($rsData); 
                    break; 

         case 'searchDataForBankReconsiliation':
                 
                $returnField = array('key' => $obj->tableName . '.pkey', 'value' => $fieldValue);
                //overwrite field yg di search
                $searchFiledValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',', $_GET['searchField']) : $fieldValue;
                // $searchOptions = array('field' => $searchFiledValue,  'key' => $_GET['term']);


                if(!isset($_GET['coakey']) || empty($_GET['coakey']))  die;


                $criteria = array();
                array_push($criteria, '(' . $obj->tableName . '.isreconsile =  0 )');
                array_push($criteria, '(' . $obj->tableName . '.statuskey in (2,3) )');

                if (isset($_GET['coakey']) && !empty($_GET['coakey'])) {
                    array_push($criteria, $obj->tableName . '.coakey = ' . $obj->oDbCon->paramString($_GET['coakey']));
                }

                
                if(isset($_GET) && !empty($_GET['startdate']) ) {
                    $month = date('m',strtotime($_GET['startdate']));
                    $year = date('Y',strtotime($_GET['startdate']));
                    array_push($criteria, 'MONTH('.$obj->tableName . '.trdate) = ' . $obj->oDbCon->paramString($month) . ' AND YEAR('.$obj->tableName . '.trdate) =' . $obj->oDbCon->paramString($year));
                }

                $criteria = implode(' and ', $criteria);

                $searchOptions['criteria'] = ' and ' . $criteria;

                $order = ' order by trdate asc, pkey asc';

                $rsData = $obj->searchDataForAutoComplete($returnField, $searchOptions, $order);
                     
                echo json_encode($rsData);
                break;
        }
}

die;
  
?>
