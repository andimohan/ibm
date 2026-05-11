<?php

class TruckingPurchaseRefund extends BaseClass
{

   function __construct()
   {
      parent::__construct();
      $this->tableName = 'trucking_purchase_refund';
      $this->tableCustomer  = 'customer';
      $this->tableSupplier  = 'supplier';
      $this->tableWarehouse  = 'warehouse';
      $this->tableSalesOrder  = 'trucking_service_order_header';
      $this->tableStatus = 'transaction_status';
      $this->securityObject = 'TruckingPurchaseRefund';
      $this->isTransaction  = true;
      $this->newLoad        = true;

      $this->arrData = array();
      $this->arrData['pkey']   = array('pkey');
      $this->arrData['code']   = array('code');
      $this->arrData['trdate'] = array('trDate', 'date');
      $this->arrData['supplierkey'] = array('hidSupplierKey');
      $this->arrData['warehousekey'] = array('selWarehouseKey');
      $this->arrData['refjoborderkey'] = array('hidSOKey'); 
      $this->arrData['jobinformation'] = array('jobInformation');
      $this->arrData['total'] = array('total', 'number');
      $this->arrData['trdesc'] = array('trDesc');
      $this->arrData['statuskey']  = array('selStatus');

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 90, 'align' => 'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100, ));
      array_push($this->arrDataListAvailableColumn, array('code' => 'supplier', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 140, ));
      array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 140));
      array_push($this->arrDataListAvailableColumn, array('code' => 'jobOrder', 'title' => 'jobOrder', 'dbfield' => 'socode', 'default' => true, 'width' => 130));
      array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'total', 'default' => true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('date', $this->tableName . '.trdate'));
      array_push($this->arrSearchColumn, array('total', $this->tableName . '.total'));
      array_push($this->arrSearchColumn, array('supplier', $this->tableSupplier . '.name'));
      array_push($this->arrSearchColumn, array('warehouse', $this->tableWarehouse . '.name'));
      array_push($this->arrSearchColumn, array('customer', $this->tableCustomer . '.name'));
      array_push($this->arrSearchColumn, array('jobOrder', $this->tableSalesOrder . '.code'));
      array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));

      $this->includeClassDependencies(
         array(
            'AP.class.php',
            'TruckingServiceOrder.class.php',
            'Supplier.class.php', 
            'APCommission.class.php',
            'Warehouse.class.php',
         )
      );

      $this->overwriteConfig();

   }

   function getQuery()
   {
      $sql = '
            SELECT ' . $this->tableName . '.* ,
                    ' . $this->tableSupplier . '.name as suppliername,
                    ' . $this->tableCustomer . '.name as customername, 
                    ' . $this->tableWarehouse . '.name as warehousename, 
                    ' . $this->tableSalesOrder . '.code as socode, 
			        ' . $this->tableStatus . '.status as statusname 
			FROM ' . $this->tableStatus . ',
                  ' . $this->tableName . ' 
				  left join '. $this->tableSalesOrder .' on '. $this->tableName .'.refjoborderkey = '. $this->tableSalesOrder .'.pkey
				  left join ' . $this->tableCustomer . ' on '. $this->tableSalesOrder .'.customerkey = '. $this->tableCustomer .'.pkey
                  left join ' . $this->tableSupplier . ' on '. $this->tableName .'.supplierkey = '. $this->tableSupplier .'.pkey
                  left join ' . $this->tableWarehouse . ' on '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey
        	WHERE 
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		' . $this->criteria;
      return $sql;
   }

   function validateForm($arr, $pkey = '')
   {
      $arrayToJs = parent::validateForm($arr, $pkey);

      $truckingServiceOrder = new TruckingServiceOrder();

      $supplierKey = $arr['hidSupplierKey'];
      $soKey = $arr['hidSOKey']; 
      $warehouseKey = $arr['selWarehouseKey'];
      $total       = $arr['total'];

      $rsSalesOrder = $truckingServiceOrder->getDataRowById($soKey);
      $rsPurchaseRefund = $this->searchDataRow( array( $this->tableName . '.pkey'),
											' and ' . $this->tableName . '.refjoborderkey = ' . $this->oDbCon->paramString($soKey) . ' 
											  and ' . $this->tableName .'.statuskey in (2,3) ');

      if(empty($supplierKey))
      {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['supplier'][1]);
      }
 

      if(empty($rsSalesOrder))
      {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['jobOrder'][1]);
      }
  
      if($total <= 0)
      {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['total'][2]);
      } 

// gk perlu pisah gudang utk saat ini	   
//      if($warehouseKey <> $rsSalesOrder[0]['warehousekey'])
//      {
//         $this->addErrorList($arrayToJs, false, $this->errorMsg['truckingPurchaseRefund'][1]);
//      }

      //chek JO telah digunakan atau belum
	   // gk perlu sepertinya, 1 JO mungkin bisa bebrapa refund
//      if(!empty($rsPurchaseRefund))
//      { 
//         $this->addErrorList($arrayToJs, false, ' <strong> '. $rsSalesOrder[0]['code'] .'. </strong> ' . $this->errorMsg['truckingPurchaseRefund'][2]);
//      }

      return $arrayToJs;
   }

   function validateConfirm($rsHeader)
   {
      $truckingServiceOrder = new TruckingServiceOrder();
      $soKey            = $rsHeader[0]['refjoborderkey'];
//      $rsPurchaseRefund = $this->searchDataRow( array($this->tableName . '.refjoborderkey'),
//											   ' and '. $this->tableName . '.refjoborderkey = ' . $this->oDbCon->paramString($soKey) . ' 
//											     and ' . $this->tableName . '.statuskey in (2,3) 
//												 and '. $this->tableName . '.pkey <> '. $this->oDbCon->paramString($rsHeader[0]['pkey'])  
//											  );

      //chek JO telah digunakan atau belum
	  // gk perlu sepertinya, 1 JO mungkin bisa bebrapa refund
//      if (!empty($rsPurchaseRefund)) {
//         $rsSalesOrder = $truckingServiceOrder->getDataRowById($rsPurchaseRefund[0]['refjoborderkey']);
//         $this->addErrorLog(false, ' <strong> '. $rsSalesOrder[0]['code'] .'. </strong> '. $this->errorMsg['truckingPurchaseRefund'][2]);
//      }
	   
	   	// kalo sudah telh difaktur, gk boleh proses refund
        $rsServiceOrder = $truckingServiceOrder->searchDataRow(array($truckingServiceOrder->tableName . '.pkey', 
                                                                $truckingServiceOrder->tableName . '.code', 
                                                                $truckingServiceOrder->tableName . '.statuskey'), 
                                                                ' and ' . $truckingServiceOrder->tableName . '.statuskey in (1,6,7)  
																  and  ' . $truckingServiceOrder->tableName . '.pkey in (' . $this->oDbCon->paramString($soKey, ',') . ') ');
        

        //Check JO dapat di peroses jika status < 6, atau tidak telah di faktur
        if (!empty($rsServiceOrder)) {
            $errMsg = array();
            foreach ($rsServiceOrder as $serviceOrder) {
                array_push($errMsg, '<b>' . $serviceOrder['code'] . '. </b>' . $this->errorMsg['truckingServiceOrder'][6]);
            }

            $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'. </strong>' . $this->errorMsg[201] . '<br>' . implode('<br>', $errMsg));

        }
	   
	   
   }

   function validateCancel($rsHeader, $autoChangeStatus = false)
   {
      
	    $id = $rsHeader[0]['pkey'];
       
        $ap = new APCommission();
   
        //cek ad Prepaid yg ad bukti potongnya blm 
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));
	   
		$rsAP = $ap->searchDataRow(array($ap->tableName.'.pkey',$ap->tableName.'.code'),
								   ' and refheaderkey = '.$this->oDbCon->paramString($id).'
								     and '.$ap->tableName.'.reftabletype = '.$rsAPKey['key'].' 
									 and ('.$ap->tableName.'.statuskey in (2,3) )');
     
		if(!empty($rsAP)) {
            $arrAP = array_column($rsAP,'code');
			$this->addErrorLog( false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br>'.$this->errorMsg['apCommission'][2].'<br>' . implode(', ', $arrAP ).'.');
        }
	   
   }

   function afterStatusChanged($rsHeader)
   {
      $truckingServiceOrder = new TruckingServiceOrder(); 
      $truckingServiceOrder->updateTotalPurchaseRefund($rsHeader[0]['refjoborderkey']);
   }


   public function confirmTrans($rsHeader)
   {
      $this->addAPCommission($rsHeader);
	   
	   
	  // sementara 
	  $rsPayment = array();
	  $this->updateGL($rsHeader,$rsPayment);  
   }
   
	
    function updateGL($rs,$rsPayment){
        if (!USE_GL) return;

        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $supplier = new Supplier();
        $customer = new Customer();
        $truckingServiceOrder = new TruckingServiceOrder(); 
        
        $warehousekey = $rs[0]['warehousekey'];
		
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['createdBy'] = 0; 
        $arr['selWarehouse'] = $warehousekey;
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        $arrDesc = array();
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
		  
		array_push($arrDesc,$rsSupplier[0]['name']);  
        
		$rsJobOrder = $truckingServiceOrder->searchDataRow(array($truckingServiceOrder->tableName.'.code',$truckingServiceOrder->tableName.'.customerkey'),
														   ' and '. $truckingServiceOrder->tableName.'.pkey = ' .  $this->oDbCon->paramString($rs[0]['refjoborderkey'])
														  );
		
        // nama shipper  
		$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.name'),
												' and '. $customer->tableName.'.pkey = ' .  $this->oDbCon->paramString($rsJobOrder[0]['customerkey'])
											  );
														   
		array_push($arrDesc,$this->lang['jobOrder'] .': '.$rsJobOrder[0]['code']);  
		array_push($arrDesc,$this->lang['customer'] .': '.$rsCustomer[0]['name']);  
        
        $arr['trDesc'] = implode(chr(13), $arrDesc);
        
        $temp = -1; 
         
		// sementara baru 1 currency
        $rate = 1; // ($rs[0]['currencykey']==CURRENCY['idr']) ? 1 : $rs[0]['rate']; 
         
        $rsCOA = $coaLink->getCOALink ('purchaserefundcost', $warehouse->tableName,  $warehousekey);   
        $coakey = $rsCOA[0]['coakey'];
														   
        $temp++;
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = $rs[0]['total'] * $rate;  
        $arr['credit'][$temp] = 0; 
 
        
		// sementara, blm ad cash
		$isCash=false;
		
//        $termOfPayment = new TermOfPayment();
//		$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
//		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
        
        $totalPayment = 0;
        if ($isCash) {
//            //$rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
//            for($i=0;$i<count($rsPayment); $i++){ 
//                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
//                 $temp++;
//                 $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//                 $arr['debit'][$temp] = 0;
//                 $arr['credit'][$temp] =  $rsPayment[$i]['amount'] * $rate;  
//            }
//		
//             //selisih pembayaran  
//            
//            if($rs[0]['balance'] != 0){ 
//                $temp++; 
//                if ($rs[0]['balance'] < 0){ 
//                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
//                    $arr['debit'][$temp] = 0; 
//                    $arr['credit'][$temp] = abs($rs[0]['balance'] * $rate); 
//                }else{ 
//                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
//                    $arr['debit'][$temp] = abs($rs[0]['balance'] * $rate);  
//                    $arr['credit'][$temp] = 0;
//                }
//                    
//                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//            }

        }else {   
			
                $temp++;
                $arr['hidCOAKey'][$temp] = $supplier->getCommissionCOAKey($rs[0]['supplierkey'],$warehousekey);
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] =  $rs[0]['total'] * $rate; 
        }
		
		
		$arrayToJs = $generalJournal->addData($arr);
         
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }

   function cancelTrans($rsHeader, $copy)
   {
      $this->cancelAPCommission($rsHeader);
      if ($copy)
         $this->copyDataOnCancel($rsHeader[0]['pkey']);
	   
	   
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
   }

   function addAPCommission($rsHeader)
   {
	   
	  $amount = $rsHeader[0]['total'];
      $currency = CURRENCY['idr'];
      $rate     = 1;
	   
      if ($amount <= 0) return;
	   
	   
      $apCommission = new APCommission();
      $truckingServiceOrder = new TruckingServiceOrder();

      $rsJO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refjoborderkey']);

      $rsPurchaseRefund = $this->getTableKeyAndObj($this->tableName, array('key'));

 
      $arrParam                     = array();
      $arrParam['code']             = 'xxxxxx';
      $arrParam['trDate']           = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
      $arrParam['hidSupplierKey']   = $rsHeader[0]['supplierkey'];
      $arrParam['hidRefHeaderKey']  = $rsHeader[0]['pkey'];
      $arrParam['hidRefKey']        = $rsHeader[0]['pkey'];
      $arrParam['hidRefKey2']       = $rsJO[0]['pkey'];
      $arrParam['hidRefCode']       = $rsHeader[0]['code'];
      $arrParam['hidRefCode2']      = $rsJO[0]['code'];
      $arrParam['hidRefDate']       = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
      $arrParam['hidRefTable']      = $rsPurchaseRefund['key'];
      $arrParam['amount']           = $amount;
      $arrParam['amountIDR']        = $amount * $rate;
      $arrParam['trDesc']           = $rsHeader[0]['trdesc'];
      $arrParam['dueDate']          = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
      $arrParam['overwriteGL']      = 1;
      $arrParam['islinked']         = 1;
      $arrParam['selAPType']        = AP_TYPE['salesCommission'];
      $arrParam['selWarehouse']     = $rsHeader[0]['warehousekey'];
      $arrParam['selCurrency']      = $currency;
      $arrParam['selStatus']        = TRANSACTION_STATUS['menunggu'];
      $arrParam['currencyRate']     = $rate;

      $arrayToJs = $apCommission->addData($arrParam);

      if (!$arrayToJs[0]['valid'])
         throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

   }

   function cancelApCommission($rsHeader)
   {
      $ap       = new APCommission();

      $rsPurchaseRefundKey =  $this->getTableKeyAndObj($this->tableName, array('key'));
      $arrPurchaseKey  = $rsPurchaseRefundKey['key'];

      $rsAP = $ap->searchDataRow(array($ap->tableName.'.pkey'),
								  ' and  ' . $ap->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' 
								    and ' . $ap->tableName . '.reftabletype = ' . $this->oDbCon->paramString($arrPurchaseKey) . ' 
									and ' . $ap->tableName . '.statuskey = 1');
      
      for ($i = 0; $i < count($rsAP); $i++) {
         $ap->changeStatus($rsAP[$i]['pkey'], 4, '', false, true);
      }
   }

   function normalizeParameter($arrParam, $trim = false)
   {

      $arrParam = parent::normalizeParameter($arrParam, true);
      return $arrParam;
   }

}

?>
