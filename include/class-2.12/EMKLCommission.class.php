<?php
  
class EMKLCommission extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'emkl_commission_header';
		$this->tableNameDetail = 'emkl_commission_detail'; 
        $this->tablePayment = 'emkl_commission_payment';
		$this->tableStatus = 'transaction_status'; 
        $this->tableJobOrder = 'emkl_job_order_header';
        $this->tableSupplier = 'supplier';
        $this->tableCustomer = 'customer';
        $this->tableCurrency = 'currency';  
		$this->tablePort = 'port';
		$this->tableDebitNoteHeader = 'debit_note_header';
        $this->tableDebitNoteDetail = 'debit_note_detail';
         
        $this->tableWarehouse = 'warehouse';
		$this->securityObject = 'EMKLCommission'; 
        $this->isTransaction = true; 
        
        $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['description'] = array('detailDescription');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit','number'); 
        $this->arrDataDetail['subtotal'] = array('detailSubtotal','number'); 
        $this->arrDataDetail['subtotalcurrency'] = array('detailRowCurrencySubtotal','number'); 
 	    $this->arrDataDetail['currencykey'] = array('selCurrencyDetail'); 
         
        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 
            
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));    
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['refkey'] = array('hidJobOrderKey', array('mandatory'=>true));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');    
        $this->arrData['supplierkey'] = array('hidSupplierKey');  
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['balance'] = array('balance','number');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['grandtotal'] = array('total','number');
        $this->arrData['currencykey'] = array('selCurrency');  
        $this->arrData['rate'] = array('currencyRate','number'); 
        $this->arrData['customerkey'] = array('hidShipperKey');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'defaut'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'JOCode','title' => 'JOCode','dbfield' => 'jocode','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => 'shipper','dbfield' => 'shippername', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etdpol','title' => 'etd','dbfield' => 'etdpol','default'=>true,'align' => 'center', 'width' => 80, 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etapod','title' => 'eta','dbfield' => 'etapod','default'=>true,'align' => 'center', 'width' => 80, 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname','default'=>true,'width' => 60, 'align'=>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'rate','title' => 'rate','dbfield' => 'rate', 'width' => 70, 'align'=>'right','format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true,'width' => 100, 'align'=>'right','format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
        
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
              
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklCommission'));
     
       
        $this->includeClassDependencies(array(
              'AP.class.php', 
              'APCommission.class.php', 
              'Currency.class.php', 
              'EMKLJobOrder.class.php', 
              'PaymentMethod.class.php', 
              'Supplier.class.php', 
              'TermOfPayment.class.php', 
              'GeneralJournal.class.php', 
              'Customer.class.php', 
              'Warehouse.class.php' 
        ));

        $this->overwriteConfig();
   }
   
  function getQuery(){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.* ,
              '.$this->tableJobOrder.'.code as jocode,
              '.$this->tableJobOrder.'.etdpol,
              '.$this->tableJobOrder.'.etapod,
              '.$this->tableWarehouse.'.name as warehousename,
              '.$this->tableSupplier.'.name as suppliername,
              '.$this->tableCustomer.'.name as shippername,
			  '.$this->tableStatus.'.status as statusname ,
              '.$this->tableCurrency.'.name as currencyname,
              pol.name as polname,
              pod.name as podname
			FROM 
                 '.$this->tableName.'
                     left join '.$this->tableCurrency.' on  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey ,
                 '.$this->tableSupplier.',
                 '.$this->tableJobOrder.'
                    left join '.$this->tablePort.' pol on  '.$this->tableJobOrder.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableJobOrder.'.podkey = pod.pkey
                    left join '.$this->tableCustomer.' on  '.$this->tableJobOrder.'.customerkey = '.$this->tableCustomer.'.pkey,
                 '.$this->tableWarehouse.',
                 '.$this->tableStatus.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and 
                '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and  
                '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey  
 		' .$this->criteria ;   
              
      $sql .=  $this->getWarehouseCriteria() ;
      $sql .=  $this->getSalesCriteria('',array(),array($this->tableJobOrder.'.saleskey')) ;
      
      return $sql;
		 
    } 
    
  function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
	    $emklJobOrder = new EMKLJobOrder();
        $emklJobOrder->updateTotalCommission($rsHeader[0]['refkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
	
    function validateForm($arr,$pkey = ''){    
	    $supplier = new Supplier();
        $emklJobOrder = new EMKLJobOrder();
        $item = new Item();
          
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$refKey = $arr['hidJobOrderKey'];    
        
        $supplierKey = $arr['hidSupplierKey'];  
        $qty = $arr['qty'];
        $rsTotal = $arr['detailSubtotal']; 
        $arrPrice = $arr['priceInUnit']; 
         
        $rs = (!empty($pkey)) ? $this->getDataRowById($pkey) : array() ;
         
        //validasi kalo status gk menunggu / konfirmasi gk bisa edit 
		if (!empty($rs)){ 
			if ($rs[0]['statuskey'] > 4){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
        
        if(empty($supplierKey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['supplier'][1]); 
          
       
        $rsJO = $emklJobOrder->getDataRowById($refKey);
        if(empty($rsJO)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['jobOrder'][1]);
		} 
        
        
        for($i=0;$i<count($rsTotal);$i++){
            if($this->unFormatNumber($rsTotal[$i]) <= 0 || $this->unFormatNumber($arrPrice[$i]) <= 0){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[503]);
            }
        } 
        
        
		return $arrayToJs;
	 }

    
    function reCountSubtotal($arrParam){
        $subtotal = 0 ;
        $grandtotal = 0; 
        //$amount = 0;
        
        $arrDetailKey = $arrParam['hidDetailKey'];  
        $arrPriceinunit = $arrParam['priceInUnit'];
        $qtyInBaseUnit =  $arrParam['qty'] ; 
        $arrCurrency =  $arrParam['selCurrencyDetail'] ; 
        $currencykey=  $arrParam['selCurrency'] ; 
        $rate =  $this->unFormatNumber($arrParam['currencyRate']) ; 
        
        $arrItemDetail = array();
        for ($i=0;$i<count($arrDetailKey);$i++){
					
            // gk perlu continue, kalo add pasti kosong diawal
            //if (empty($arrDetailKey[$i])) continue; 
                        
            $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);   
            $qty = $this->unFormatNumber($qtyInBaseUnit[$i]);   
            $detailCurrencySubtotal = $qty * $priceInUnit;
            $detailSubtotal = $detailCurrencySubtotal;  
            
            // sementara cuma support 2 currency
            if($currencykey==CURRENCY['idr']){
                if($arrParam['selCurrencyDetail'][$i] <> CURRENCY['idr']) // harusnya gk pernah masuk, kalo header IDR detail gk bisa pilih USD
					$detailSubtotal *= $rate;
            }else{
                if($arrParam['selCurrencyDetail'][$i] == CURRENCY['idr'])
					$detailSubtotal /= $rate; 
            }
            
            $arrItemDetail[$i]['detailRowCurrencySubtotal'] = $detailCurrencySubtotal; 
            $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;
            $subtotal += $detailSubtotal; 
        }  
        
        
        $grandtotal = $subtotal;
        
        $balance = 0;
        $totalPayment = 0; 
                
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
        if ($rsTOP[0]['duedays'] == 0){ 
            $payment = $arrParam['paymentMethodValue'];
                for($i=0;$i<count($payment);$i++){
                    $totalPayment += $this->unFormatNumber($payment[$i]);
                }
        }
 
        $balance = $totalPayment - $grandtotal;
         
        $reCountResult['subtotal'] = $subtotal; 
        $reCountResult['total'] = $grandtotal;  
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['balance'] = $balance; 
		$reCountResult['detailCOGS'] = $arrItemDetail;
        
        return $reCountResult;
    }	
    
	function validateConfirm($rsHeader){ 
        $id = $rsHeader[0]['pkey'];

        $termOfPayment = new TermOfPayment();

        $rsPayment = $this->getPaymentMethodDetail($id); 
  
        $balance = 0;
        $totalPayment = 0;

        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
        
        $balance = $totalPayment - $rsHeader[0]['grandtotal'];   

        if ($isCash){   
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }
        
        
        // hanya boleh konfirmasi utk JO yg sudah konfirmasi
        // dan harus ad JO nya
        $emklJobOrder = new EMKLJobOrder();
        $rsEMKL = $emklJobOrder->getDataRowById($rsHeader[0]['refkey']); 
        if(empty($rsEMKL) || ( $rsEMKL[0]['statuskey'] <> 2 && $rsEMKL[0]['statuskey'] <> 3))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsEMKL[0]['code'].'</strong>. ' . $this->errorMsg[204]);   
        
        //validasi JO kalau komisi sudah di bayarkan tidak boleh ubah status
        $isCommissionPaid = $emklJobOrder->isCommissionRequested($rsHeader[0]['refkey']);
        if(!empty($isCommissionPaid[$rsHeader[0]['refkey']])) 
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '. </strong>  '.$this->errorMsg[201].'<br>'.$rsEMKL[0]['code'].' - ' .$this->errorMsg['emklJobOrder'][9]); 


	 }
	 
	function confirmTrans($rsHeader){
            $id = $rsHeader[0]['pkey'];
        
            $apCommission = new APCommission();  // harusntya ke AP COMMISSION
            $supplier = new Supplier();   
            $warehouse = new Warehouse();
            $termOfPayment = new TermOfPayment(); 
            $emklJobOrder = new EMKLJobOrder(); 
            $coaLink = new COALink(); 
        
            $amount = $rsHeader[0]['grandtotal']; 
            if ($amount <= 0)  return; 
          
			$rate = ($rsHeader[0]['currencykey'] == CURRENCY['idr']) ? 1 : $rsHeader[0]['rate']; 
        
		    $rsDetail = $this->getDetailWithRelatedInformation($id);
            $rsEMKL = $emklJobOrder->getDataRowById($rsHeader[0]['refkey']); 
            $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']); 
          
            $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();
            $rsAPKey = $apCommission->getTableKeyAndObj($this->tableName,array('key'));

            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
            $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
    
            $rsPayment = $this->getPaymentMethodDetail($rsHeader[0]['pkey']);  
        
            $note = array(); 
        
            if ($isCash){
               
            }else{
                
              
                $arrParam = array();	 
                
				$arrParam['code'] = 'xxxxxx';
				$arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
				$arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
				$arrParam['hidRefKey2'] = $rsEMKL[0]['pkey']; 
				$arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
				$arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
				$date = new DateTime($rsHeader[0]['trdate']);
				$date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
				$arrParam['dueDate'] = $date->format('d / m / Y'); 
				$arrParam['hidRefCode'] = $rsHeader[0]['code'];
				$arrParam['hidRefCode2'] = $rsEMKL[0]['code'];
				$arrParam['hidRefDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
				$arrParam['hidRefTable'] = $rsAPKey['key'];
				$arrParam['amount'] =  $amount; 
				$arrParam['amountIDR'] = $amount * $rate;
				$arrParam['currencyRate'] = 1;
				$arrParam['trDesc'] = implode(chr(13),$note);
				$arrParam['overwriteGL'] = 1;
				$arrParam['islinked'] = 1;
				$arrParam['selAPType'] = AP_TYPE['salesCommission'];
				$arrParam['selWarehouse'] = $warehousekey;
				$arrParam['selCurrency'] = $rsHeader[0]['currencykey'];
				$arrParam['currencyRate'] = $rate;
				$arrParam['trDesc'] = $rsHeader[0]['trdesc']; 
				$arrParam['salesordercodecache'] = $rsEMKL[0]['code'];

				$arrayToJs = $apCommission->addData($arrParam);  
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
                  
            }
            
          $this->updateGL($rsHeader,$rsPayment);  
            
	}
 
    
    function cancelTrans($rsHeader,$copy){
        $id = $rsHeader[0]['pkey'];  
        $this->cancelVendorAP($rsHeader); 
		if ($copy) $this->copyDataOnCancel($id);

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	}  
    
    function validateCancel($rsHeader,$autoChangeStatus=false){
        $apCommission = new APCommission();
        $pkey = $rsHeader[0]['pkey'];
        
        parent::validateCancel($rsHeader,$autoChangeStatus); 
        $rsAPKey = $apCommission->getTableKeyAndObj($this->tableName,array('key'));
        $rsAP = $apCommission->searchData('','',true,' and  '.$apCommission->tableName.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and '.$apCommission->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$apCommission->tableName.'.statuskey in(2,3))');
        if(!empty($rsAP))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
        
        // buat jaga2
        $apCommission = new AP();
        $rsAP = $apCommission->searchData('','',true,' and  '.$apCommission->tableName.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and '.$apCommission->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$apCommission->tableName.'.statuskey in(2,3))');
        if(!empty($rsAP))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
        
      
    }
    
    function cancelVendorAP($rsHeader){
        $ap = new APCommission(); 
        $arrAPKey = array();
        
        $rsEMKLKey = $ap->getTableKeyAndObj($this->tableName,array('key'));    
        $arrAPKey = $rsEMKLKey['key'];
     
        $rsAP = $ap->searchData('','',true,' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$ap->tableName.'.reftabletype = '.$arrAPKey.' and '.$ap->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAP);$i++) { 
            $ap->changeStatus($rsAP[$i]['pkey'],4,'',false, true);  
        }
        
        // buat jaga2 modul lama 
        $ap = new AP();   
        $rsAP = $ap->searchData('','',true,' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$ap->tableName.'.reftabletype = '.$arrAPKey.' and '.$ap->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAP);$i++) { 
            $ap->changeStatus($rsAP[$i]['pkey'],4,'',false, true);  
        }
          
    }
    
 	function getDetailWithRelatedInformation($pkey){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
			  	'.$this->tableName.'.rate,
	   			'.$this->tableCurrency.'.name as currencyname
                
              from
			  	'.$this->tableName .',
			  	'.$this->tableNameDetail .',
			  	'.$this->tableCurrency .'   
			  where 
			  	'.$this->tableName .'.pkey = '.$this->tableNameDetail .'.refkey and
			  	'.$this->tableNameDetail .'.currencykey = '.$this->tableCurrency .'.pkey and
			  	'.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';
       
        //$sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
	
   }
	
    function generateCommissionReport($criteria='',$order='',$pkey=''){ 
	   $sql =  '
            SELECT '.$this->tableName.'.code ,
                  '.$this->tableName.'.refkey,
                  '.$this->tableName.'.termofpaymentkey,
                  '.$this->tableName.'.trdate,
                  '.$this->tableName.'.trdesc,
                  '.$this->tableName.'.rate,
                  '.$this->tableJobOrder.'.code as jocode,
                  '.$this->tableJobOrder.'.etdpol,
                  '.$this->tableJobOrder.'.etapod,
                  '.$this->tableWarehouse.'.name as warehousename,
                  '.$this->tableCustomer.'.name as shippername,
                  '.$this->tableNameDetail.'.currencykey,
                  '.$this->tableNameDetail.'.qty,
                  '.$this->tableNameDetail.'.description,
                  '.$this->tableNameDetail.'.subtotal,
                  '.$this->tableNameDetail.'.priceinunit,
                  '.$this->tableNameDetail.'.subtotalcurrency,
                  '.$this->tableStatus.'.status as statusname,
                  '.$this->tableCurrency.'.name as currencyname,
                  '.$this->tableSupplier.'.name as suppliername,
                  pol.name as polname,
                  pod.name as podname 
		    FROM 
                 '.$this->tableName.'
                    left join '.$this->tableJobOrder.' on  '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey
                    left join '.$this->tableSupplier.' on  '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey
                    left join '.$this->tablePort.' pol on  '.$this->tableJobOrder.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableJobOrder.'.podkey = pod.pkey
                    left join '.$this->tableCustomer.' on  '.$this->tableJobOrder.'.customerkey = '.$this->tableCustomer.'.pkey,
                 '.$this->tableWarehouse.',
                 '.$this->tableNameDetail.' 
                    left join '.$this->tableCurrency.' on  '.$this->tableNameDetail.'.currencykey = '.$this->tableCurrency.'.pkey,
                 '.$this->tableStatus.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);

        if (!empty($order))  
            $sql .=  ' ' .$order; 
         
       // $this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);
		 
    } 
    function normalizeParameter($arrParam, $trim=false){
        $arrParam['currencyRate'] =   ($arrParam['selCurrency'] == CURRENCY['idr']) ? 1 : $arrParam['currencyRate']; 
		$arrDetailKey = $arrParam['hidDetailKey'];
		$arrCurrencyDetail = $arrParam['selCurrencyDetail'];
		
        // tambah nama shipper / customer
        $emklJobOrder = new EMKLJobOrder();  
        $rsJO = $emklJobOrder->getDataRowById($arrParam['hidJobOrderKey']);
        $arrParam['hidShipperKey'] = (!empty($rsJO[0]['customerkey'])) ? $rsJO[0]['customerkey'] : 0;  
        $arrParam['selWarehouseKey'] = $rsJO[0]['warehousekey'];
        
        
        $reCountResult = $this->reCountSubtotal($arrParam);  
        //$arrParam['subtotal'] = $reCountResult['subtotal'];  
        $arrParam['total'] = $reCountResult['total'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['balance'] = $reCountResult['balance']; 
		
		for ($i=0;$i<count($arrCurrencyDetail);$i++){   
            $arrParam['detailSubtotal'][$i] = $reCountResult['detailCOGS'][$i]['detailSubtotal']; 
            $arrParam['detailRowCurrencySubtotal'][$i] = $reCountResult['detailCOGS'][$i]['detailRowCurrencySubtotal'];  
        } 
        
        $arrParam = parent::normalizeParameter($arrParam, true);
         
        return $arrParam;
    } 
    
	function getCommissionByJobOrder($jokey){
		$sql = 'select	
					'.$this->tableName.'.code, 
					'.$this->tableName.'.supplierkey, 
					'.$this->tableName.'.grandtotal, 
					'.$this->tableName.'.rate,
					('.$this->tableName.'.grandtotal * '.$this->tableName.'.rate) as grandtotalidr, 
					'.$this->tableName.'.refkey,
					'.$this->tableCurrency.'.name as currencyname, 
					'.$this->tableSupplier.'.name as suppliername
				from
					'.$this->tableName.',
					'.$this->tableCurrency.',
					'.$this->tableSupplier.'
				where 
					'.$this->tableName.'.supplierkey  = '.$this->tableSupplier.'.pkey and
					'.$this->tableName.'.currencykey  = '.$this->tableCurrency.'.pkey and
					'.$this->tableName.'.statuskey in (2,3) and
					'.$this->tableName.'.refkey in ('.$this->oDbCon->paramString($jokey,',').')
				';
		
		
		return $this->oDbCon->doQuery($sql);
	}
	
    function updateGL($rs,$rsPayment){
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $supplier = new Supplier();
        $customer = new Customer();
        $item = new Item();         
        
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
		//$arr['trDesc'] = $this->ucFirst($this->lang['purchaseRefund']. ' ' .  $this->lang['from']) . ' '. $rsSupplier[0]['name'].'.';  
         
		array_push($arrDesc,$rsSupplier[0]['name']);  
        
        // nama shipper
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		array_push($arrDesc,$this->lang['shipper'] .': '.$rsCustomer[0]['name']);  
        
        $arr['trDesc'] = implode(chr(13), $arrDesc);
        
        $temp = -1; 
         
        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        $rate = ($rs[0]['currencykey']==CURRENCY['idr']) ? 1 : $rs[0]['rate']; 
        

       /* $arrItemCOA = array();
        $itemCOAKey = $supplier->getCommissionCOAKey($rs[0]['supplierkey'],$warehousekey);
        foreach($rsDetail as $detail){
            $totalItemValue = $detail['subtotal']; // kali rate
            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
        }
        foreach ($arrItemCOA as $coakey => $coaValue){ 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $coaValue  * $rate;  
            $arr['credit'][$temp] = 0; 
        }
        */
        
        $rsCOA = $coaLink->getCOALink ('purchaserefundcost', $warehouse->tableName,  $warehousekey);   
        $coakey = $rsCOA[0]['coakey'];
        
        $totalCommission = 0;  
        foreach($rsDetail as $detail)
            $totalCommission +=$detail['subtotal']; 

        $temp++;
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = $totalCommission  * $rate;  
        $arr['credit'][$temp] = 0; 

         
        /*$rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName,$warehousekey, 0); 
	    $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] =  $rs[0]['taxvalue'] * $rate; 
		$arr['credit'][$temp] = 0; */
         
      
         
        $termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
        
        $totalPayment = 0;
        if ($isCash) {
            //$rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
            for($i=0;$i<count($rsPayment); $i++){ 
                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                 $temp++;
                 $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                 $arr['debit'][$temp] = 0;
                 $arr['credit'][$temp] =  $rsPayment[$i]['amount'] * $rate;  
            }
		
             //selisih pembayaran  
            
            if($rs[0]['balance'] != 0){ 
                $temp++; 
                if ($rs[0]['balance'] < 0){ 
                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] = abs($rs[0]['balance'] * $rate); 
                }else{ 
                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = abs($rs[0]['balance'] * $rate);  
                    $arr['credit'][$temp] = 0;
                }
                    
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            }

        }else {  
                $temp++;
                $arr['hidCOAKey'][$temp] = $supplier->getCommissionCOAKey($rs[0]['supplierkey'],$warehousekey);
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] =  $rs[0]['grandtotal'] * $rate; 
        }
         
       
		$arrayToJs = $generalJournal->addData($arr);
         
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
    
     function getDetailJobOrder($arrKey){
         
        $tableType = $this->getTableKeyAndObj($this->tableJobOrder,array('key'))['key'];
         
        // sementara purchase isi job nya masih di Header, kedepan akan dipindah ke detail
        
        $rs = $this->searchDataRow(array($this->tableName.'.pkey',$tableType .' as reftabletype', $this->tableName.'.refkey as sokey'),
                                             ' and '.$this->tableName.'.pkey in (' . $this->oDbCon->paramString($arrKey,',').')'
                                              );
        
        return $rs;
    }
    
        
    function getDebitNote($jokey,$statuskey=array(2,3)){
         
        
        // sementara purchase isi job nya masih di Header, kedepan akan dipindah ke detail
        // nanti jika JO ad di PO detail, totaldebit harus di prorate ulang
        
        $tablekey =  $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
            
        $sql = 'select 
                   '.$this->tableName.'.pkey as pokey,
                   '.$this->tableName.'.code as pocode,
                   '.$this->tableDebitNoteDetail.'.refpurchasetabletype as purchasetabletype,
                   '.$this->tableJobOrder.'.pkey as sokey,
                   '.$this->tableJobOrder.'.code as socode,
                   '.$this->tableDebitNoteHeader.'.pkey as debitnotekey,
                   '.$this->tableDebitNoteHeader.'.code as debitnotecode,
                   '.$this->tableDebitNoteHeader.'.statuskey as debitnotestatuskey,
                   '.$this->tableDebitNoteDetail.'.totaldebit,
                   '.$this->tableDebitNoteHeader.'.currencykey,
                   '.$this->tableDebitNoteDetail.'.rate,
                   '.$this->tableSupplier.'.name as suppliername,
                   '.$this->tableCurrency.'.name as currencyname 
                from
                  '.$this->tableName.',
                  '.$this->tableJobOrder.',
                  '.$this->tableSupplier.',
                  '.$this->tableDebitNoteHeader.',
                  '.$this->tableDebitNoteDetail.',
                  '.$this->tableCurrency.'
                where
                  '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey  and 
                  '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey  and 
                  '.$this->tableName.'.refkey in (' . $this->oDbCon->paramString($jokey,',').') and
                  '.$this->tableName.'.statuskey in (2,3) and 
                  '.$this->tableDebitNoteDetail.'.refpurchasetabletype = '.$this->oDbCon->paramString($tablekey).' and
                  '.$this->tableDebitNoteDetail.'.refpurchasekey = '.$this->tableName.'.pkey and
                  '.$this->tableDebitNoteDetail.'.refkey =  '.$this->tableDebitNoteHeader.'.pkey and
                  '.$this->tableDebitNoteHeader.'.statuskey in ('.$this->oDbCon->paramString($statuskey,',').') and
                  '.$this->tableDebitNoteHeader.'.currencykey = '.$this->tableCurrency.'.pkey 
              ';
//        $this->setLog($sql,true);
 
        return $this->oDbCon->doQuery($sql);  
    }
}
?>
