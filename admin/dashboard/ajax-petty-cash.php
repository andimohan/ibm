<?php
require_once '../../_config.php';
require_once '../../_include-v2.php';

includeClass(array('PettyCash.class.php','GeneralJournal.class.php','Employee.class.php'));
$pettyCash = new PettyCash();
$generalJournal = new GeneralJournal();
$empoyee = new Employee();
$obj = $pettyCash;

if (isset($_GET) && !empty($_GET['action'])) {

   switch ($_GET['action']) {
      case 'getData' :

         $order = 'order by ' . $obj->tableName . '.trdate, pkey asc';

         $arrCriteria = array();
         array_push($arrCriteria, '(' . $obj->tableName . '.statuskey in(1,2,3) )');
         array_push($arrCriteria, $obj->tableName.'.coakey = ' . $obj->oDbCon->paramString($_GET['coakey']) );  
        if(isset($_GET['supplierkey']) && !empty($_GET['supplierkey'])) {
            array_push($arrCriteria, $obj->tableName.'.supplierkey = ' . $obj->oDbCon->paramString($_GET['supplierkey']) ); 
         } 

         if(isset($_GET['typekey']) && !empty($_GET['typekey'])) {
            $typekey = $_GET['typekey'];
            if($typekey == 1) {
               //Type Normal
               array_push($arrCriteria, $obj->tableName.'.isdownpayment = 0');  
            } elseif($typekey == 2){ 
               //Type Downpayment
               array_push($arrCriteria, $obj->tableName.'.isdownpayment = 1');  
            }
         }

         if (isset($_GET) && !empty($_GET['startdate']) && !empty($_GET['enddate'])) {

            $dateDiff = $obj->dateDiff($_GET['startdate'], $_GET['enddate']);
            if ($dateDiff < 0)
               $_GET['enddate'] = $_GET['startdate'];
            array_push($arrCriteria, $obj->tableName . '.trdate between ' . $obj->oDbCon->paramString($_GET['startdate']) . ' AND ' . $obj->oDbCon->paramString($_GET['enddate'] . ' 23:59:59'));
         }

         $criteria = implode(' and ', $arrCriteria);
         $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

         $rs = $obj->getDataPettyCash($criteria, $order);

         echo json_encode($rs);
      break;

      case 'getCOAmout':

         if (!isset($_GET) || empty($_GET['coakey']))
            die;

         $arrStartingBalance = $obj->sumAccount($_GET['coakey'],'',$_GET['startdate']); 

         echo json_encode($arrStartingBalance);
      break;
   }

}

	if (isset($_POST) && !empty($_POST['action'])) { 
       
	   switch ($_POST['action']) {
		  case 'save':	
            $arrResult = array();

            $arrId = $_POST['hidId'];

            $order = 'order by ' . $obj->tableName . '.trdate asc';

            $arrCriteria = array();
            array_push($arrCriteria, '(' . $obj->tableName . '.statuskey in(1,2,3) )');
            array_push($arrCriteria, $obj->tableName.'.coakey = ' . $obj->oDbCon->paramString($_POST['coakey']) );  

            if (isset($_POST) && !empty($_POST['startdate']) && !empty($_POST['enddate'])) {

               $dateDiff = $obj->dateDiff($_POST['startdate'], $_POST['enddate']);
               if ($dateDiff < 0)
                  $_POST['enddate'] = $_POST['startdate'];
               array_push($arrCriteria, $obj->tableName . '.trdate between ' . $obj->oDbCon->paramString($_POST['startdate']) . ' AND ' . $obj->oDbCon->paramString($_POST['enddate'] . ' 23:59:59'));
            }  

            $criteria = implode(' and ', $arrCriteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            $arrKey = json_decode($_POST["data"], true);


            for($i=0;$i<count($arrKey);$i++){  
               if (!in_array($arrKey[$i],$arrId)){ 
                  $arrayToJs = $obj->delete($arrKey[$i]);
               }
            }

            $arrDriver = $employee->searchData($employee->tableName.'.statuskey',2,true, ' and ' . $employee->tableName.'.isdriver=1');

            for($i=0;$i<count($arrId);$i++){ 
                $trdate = $_POST['trdate'][$i];
                $trdate = date('y-m-d', strtotime(str_replace(' / ', '-', $trdate)));
               
                $arr = array();
                $arr['code'] = 'xxxxxx';
                $arr['hidId'] = $arrId[$i];
                $arr['trDate'] = $trdate;
                $arr['hidCustomerKey'] = $_POST['hidCustomerKey'][$i];
                $arr['doNumber'] = $_POST['doNumber'][$i];
                $arr['hidCostKey'] = $_POST['hidCostKey'][$i];
                $arr['hidStuffingLocationFromKey'] = $_POST['hidStuffingLocationFromKey'][$i];
                $arr['hidStuffingLocationKey'] = $_POST['hidStuffingLocationKey'][$i];
                $arr['hidServiceKey'] = $_POST['hidServiceKey'][$i];
                $arr['hidCarKey'] = $_POST['hidCarKey'][$i];
                $arr['hidDriverKey'] = $_POST['hidDriverKey'][$i];
                $arr['hidCoDriverKey'] = $_POST['hidCoDriverKey'][$i];
                $arr['qtyMulti'] = $_POST['qtyMulti'][$i];
                $arr['debit'] = $_POST['debit'][$i];
                $arr['credit'] = $_POST['credit'][$i];
                $arr['trDesc'] = $_POST['trDesc'][$i];
                $arr['driverNameDesc'] = $_POST['driverNameDesc'][$i];
                $arr['coDriverNameDesc'] = $_POST['coDriverNameDesc'][$i];
                $arr['chkIsOutsource'] = $_POST['chkIsOutsource'][$i];
                $arr['hidSupplierKey'] = $_POST['hidSupplierKey'][$i];
                $arr['carOutsource'] = $_POST['carOutsource'][$i];
                $arr['hidCOAKey'] = $_POST['coakey'];
                $arr['chkIsSPK'] = $_POST['chkIsSPK'][$i];
                $arr['hidDriverKey'] = $arrDriver[0]['pkey'];
                $arr['hidCoDriverKey'] = $arrDriver[0]['pkey'];
                $arr['chkIsDownpayment'] = $_POST['chkIsDownpayment'][$i];
                $arr['settlementAmount'] = $_POST['settlementAmount'][$i];
                
                if ($arr['chkIsOutsource'] == 1) {
                  $arr['driverNameDesc'] = '';
                  $arr['coDriverNameDesc'] = '';
               } else  {
                   $arr['hidSupplierKey'] = 0;
                   $arr['carOutsource'] = '';
                }


                if (!empty($arrId[$i])) {
                    $arrayToJs = $obj->editData($arr);
                } else {
                    
                   if (empty($arr['hidCostKey']) && empty($arr['trDesc'])){ 
                      continue;
                   }
                   
                   // mau di review ulang
   		          //$debit  = $obj->unFormatNumber($arr['debit']); 
               //   $credit = $obj->unFormatNumber($arr['credit']); 
                  //if ($debit == 0 && $credit == 0) {
                  //   // kalau debit dan kredit 0 jangan save rownya
                  //   continue; 
                  //}
                   
                    $arrayToJs = $obj->addData($arr); 
                }

                if(isset($arrayToJs[0]) &&!$arrayToJs[0]['valid']) {
                   $arrResult[] = $arrayToJs;
                }

            }
               echo json_encode($arrResult);

            break;
	   }
	}

die;


?>
