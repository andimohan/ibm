<?php

class CreditNote extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'credit_note_header';
    $this->tableNameDetail = 'credit_note_detail';
    $this->tableCustomer = 'customer';
    $this->tableWarehouse = 'warehouse';   
    $this->tableCurrency = 'currency';   
    $this->tableStatus = 'transaction_status';
    $this->isTransaction = true;
    $this->tableEMKLInvoiceHeader= 'emkl_order_invoice_header';  
    $this->tableEMKLInvoiceDetail= 'emkl_order_invoice_detail';  
    $this->tableEMKLJobOrderHeader= 'emkl_job_order_header';  
    $this->tableEMKLJobOrderDetail= 'emkl_job_order_detail';  
    $this->tableAR = 'ar';  
    $this->securityObject = 'CreditNote';

    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
	$this->arrDataDetail['arkey'] = array('hidARKey',array('mandatory'=>true));
	$this->arrDataDetail['totalcredit'] = array('creditTotal',array('datatype'=>'number','mandatory'=>true));
    $this->arrDataDetail['invoicekey'] = array('hidInvoiceKey');
    $this->arrDataDetail['rate'] = array('rate','number');

    $arrDetails = array(); 
    array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
    $this->arrData['code'] = array('code');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['trdesc'] = array('trDesc');
    //$this->arrData['reftabletype'] = array('reftabletype');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['grandtotal'] = array('grandTotal','number');
	$this->arrData['currencykey'] = array('selCurrency');
        
    array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','default'=>true, 'dbfield' => 'warehousename', 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname','default'=>true ,'width' => 80, 'align' => 'center'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true ,'width' => 100, 'align' => 'right', 'format' => 'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
    
    $this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/creditNote'));
          
    $this->includeClassDependencies(array( 
        'Currency.class.php', 
        'Customer.class.php',  
        'AR.class.php',  
        'Warehouse.class.php',					  
		'ChartOfAccount.class.php', 
        'COALink.class.php', 
        'EMKLOrderInvoice.class.php',
        'GeneralJournal.class.php',
        'EMKLJobOrder.class.php'
    ));  
    
    }

    function getQuery(){

        $sql = '
            SELECT
                '.$this->tableName.'.* , 
                '.$this->tableWarehouse.'.name as warehousename,
                 '.$this->tableCustomer.'.name as customername, 
                '.$this->tableStatus.'.status as statusname,
                '.$this->tableCurrency.'.name as currencyname 
            FROM '.$this->tableStatus.',
                 '.$this->tableCustomer.',
                 '.$this->tableName.' 
                    left join '.$this->tableCurrency.' on '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey  ,
                 '.$this->tableWarehouse.'
            WHERE   
                  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
                ' .$this->criteria ;
            
        $sql .=  $this->getWarehouseCriteria() ;
         
        $sql .= ' group by '.$this->tableName.'.pkey ';
                    
        return $sql;
    }
  

    function reCountGrandTotal($arrParam){
           
        $transactionObj = $this->getTransactionObject();
            
        $grandTotal = 0; 
 
        $arrArkey = $arrParam['hidARKey']; 
        $arrAmount = $arrParam['creditTotal'];   
         
        for ($i=0;$i<count($arrArkey);$i++){
            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            if (empty($arrArkey[$i]) || empty($arrAmount[$i]))  continue;
             
            $grandTotal += $this->unFormatNumber($arrAmount[$i]); 
        }   

        $reCountResult = array();                       
        $reCountResult['grandTotal'] = $grandTotal;  

        return $reCountResult;
                  

    } 

     function validateForm($arr,$pkey = ''){ 
        
        $transactionObj = $this->getTransactionObject();
        
        $arrayToJs = parent::validateForm($arr,$pkey); 
 
        $customerkey = $arr['hidCustomerKey'];  
        $currencykey = $arr['selCurrency'];  
        $arrARKey = $arr['hidARKey']; 
        $arrPick = $arr['chkPick'];  
        $arrCredit = $arr['creditTotal'];
        $arrAmount = $arr['amount'];
         
        $arrDetailKey = array();
        
        if(empty($customerkey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]); 
        
        $rsARCol = $transactionObj->searchDataRow(array($transactionObj->tableName.'.pkey',$transactionObj->tableName.'.code',$transactionObj->tableName.'.currencykey',$transactionObj->tableName.'.customerkey',$transactionObj->tableName.'.statuskey'),
                                            ' and '.$transactionObj->tableName.'.pkey in ('.$this->oDbCon->paramString($arrARKey,',').')');
        $rsARCol = array_column($rsARCol,null,'pkey');
          
        // cek ad duplikasi gk, dan cek customernya sesuai gk
        for($i=0;$i<count($arrARKey);$i++) {   

            if ( (!empty($arrARKey[$i]) ) && !empty($arrPick[$i]) )  { 
                
//                $rsSOI = $transactionObj->getDataRowById($arrARKey[$i]);
                $rsSOI = $rsARCol[$arrARKey[$i]];
                
                $arrCredit[$i] = $this->unFormatNumber($arrCredit[$i]);
                $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
				if($currencykey <> $rsSOI['currencykey'])
					$this->addErrorList($arrayToJs,false, $rsSOI['code'].'. '.$this->errorMsg['creditNote'][4]); 
                
                if (in_array($arrARKey[$i],$arrDetailKey)){  
                    $this->addErrorList($arrayToJs,false, $rsSOI['code'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    if (!empty($arrARKey[$i]))  
                        array_push($arrDetailKey, $arrARKey[$i]);
                }

                if ($rsSOI['customerkey'] <> $customerkey)
                    $this->addErrorList($arrayToJs,false, $rsSOI['code'].'. '.$this->errorMsg['invoice'][2]);
                
                
                if ($arrCredit[$i] > $arrAmount[$i]) 
                    $this->addErrorList($arrayToJs,false,$rsSOI['code'].'. '. $this->errorMsg['creditNote'][2]); 
                 else if($arrCredit[$i] <= 0) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['creditNote'][1]); 
 
            }
             
        } 
 
        return $arrayToJs;
    }
    
     function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,  
            '.$this->tableAR.'.refcode,  
            '.$this->tableAR.'.code as arcode,  
            '.$this->tableAR.'.trdate as ardate, 
            '.$this->tableAR.'.rate, 
            '.$this->tableAR.'.outstanding as artotal
          from
            '.$this->tableNameDetail.',
            '.$this->tableAR.' 
          where  
            '. $this->tableNameDetail.'.refkey  = '.$this->oDbCon->paramString($pkey) . ' and
            '. $this->tableNameDetail.'.arkey = '.$this->tableAR.'.pkey ' ;

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);

    } 
    
    function normalizeParameter($arrParam, $trim=false){
         
        // remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
        
    	$reCountResult = $this->reCountGrandtotal($arrParam); 
        $arrParam['grandTotal'] = $reCountResult['grandTotal'];
		
		//update ulang informasi invoicekey, agar join lebih mudah
		$ar = new AR();
		$arrARKey = $arrParam['hidARKey'];
		$rsAR = $ar->searchDataRow( array($ar->tableName.'.pkey',$ar->tableName.'.refheaderkey',$ar->tableName.'.rate'),
								    ' and ' .$ar->tableName.'.pkey in ('.$this->oDbCon->paramString($arrARKey,',').')'
								   );
		$rsAR = array_column($rsAR,null,'pkey');
		
		// agar lebih pasti saja, di loop
		$arrParam['hidInvoiceKey'] = array();
		$arrParam['rate'] = array();
		for($i=0;$i<count( $arrParam['hidARKey'] );$i++) { 
			$arkey = $arrParam['hidARKey'][$i];
			$arrParam['hidInvoiceKey'][$i] = $rsAR[$arkey]['refheaderkey'];
			$arrParam['rate'][$i] = $rsAR[$arkey]['rate'];
		}
		
        $arrParam = parent::normalizeParameter($arrParam,true); 
         
        return $arrParam;
    }

    function validateConfirm($rsHeader){  
        $id = $rsHeader[0]['pkey']; 
        
        $obj = $this->getTransactionObject();
        
        $rsDetail = $this->getDetailById($id);
     	$currencykey = $rsHeader[0]['currencykey']; 
        
        $arKeys = array_column($rsDetail,'arkey');
        
        $rsARCol = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.code',$obj->tableName.'.currencykey',$obj->tableName.'.statuskey',$obj->tableName.'.outstanding'),
                                            ' and '.$obj->tableName.'.pkey in ('.$this->oDbCon->paramString($arKeys,',').')');
        $rsARCol = array_column($rsARCol,null,'pkey');
        
        $rsExistingCN = $this->getCreditNoteDetailByAR($arKeys,true);
        $rsExistingCN = array_column($rsExistingCN,null,'arkey');
        
        for($i=0;$i<count($rsDetail);$i++){
            
            $arkey = $rsDetail[$i]['arkey'];
//            $rsAR = $obj->getDataRowById($arkey);
			$rsAR = $rsARCol[$arkey];
            
			if($currencykey<>$rsAR['currencykey'])
				$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsAR['code'].'</strong>. ' . $this->errorMsg['creditNote'][4]);
                        
            // credit note kalo konfirmasi, invoice nya harus konfirmasi atau selesai
            if($rsAR['statuskey'] > AP_STATUS['partial'] )
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsAR['code'].'</strong>. ' . $this->errorMsg[204]);
          
                        
             if ( ($rsDetail[$i]['totalcredit'] + $rsExistingCN[$arkey]['totalcredit'] ) > $rsAR['outstanding']) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsAR['code'].'</strong>.  '.$this->errorMsg['creditNote'][2]); 
             else if($rsDetail[$i]['totalcredit'] <= 0) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>.  '.$this->errorMsg['creditNote'][1]); 

        }
        
        if (USE_GL){
            $coaLink = new COALink();
            $warehouse = new Warehouse();
            $arrCOA = array();
            array_push($arrCOA, 'salesservicediscount', 'ar'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }     
         } 
        
    }
    
    function confirmTrans($rsHeader){ 
        
            $warehouse = new Warehouse();
            $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
 
            $ar = new AR();  

            $rsARKey = $ar->getTableKeyAndObj($this->tableName);  
            $arrParam = array();	

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefCode2'] =  $rsHeader[0]['donumber'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['amount'] = -$rsHeader[0]['grandtotal'];
            $arrParam['trDesc'] = $rsHeader[0]['code'];
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selARType'] = AR_TYPE['creditNote'];
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];   
            $arrParam['selCurrency'] = $rsHeader[0]['currencykey'];   
        
            $rate = 1;
            if($rsHeader[0]['currencykey'] <> CURRENCY['idr']){
                //cari rate yg bukan 1
                foreach($rsDetail as $detailRow)
                    if ($detailRow['rate']<>1){
                        $rate = $detailRow['rate'];
                        break;
                    }
            }
        
        
            $arrParam['currencyRate'] = $rate;  // sementara ambil rate pertama aj dulu

            $returnVal = $ar->addData($arrParam,false); 
             
           /* $rsHeader[0]['arKey'] = $returnVal[0]['data']['pkey'];
            $rsHeader[0]['arCode'] = $returnVal[0]['data']['code'];*/
            $rsHeader[0]['rate'] = $rate;
        
            $this->updateGL($rsHeader);
    } 
    
     function updateGL($rs){
        if (!USE_GL) return;
         
        if ($rs[0]['overwriteGL'] == 1) return;
         
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $customer = new Customer();
        
        $warehousekey = $rs[0]['warehousekey']; 
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
         
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0;
		$arr['refCode'] = $rs[0]['code'];
		$arr['trDesc'] = $rsCustomer[0]['name'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1; 
		  
        $total = $rs[0]['grandtotal'] *  $rs[0]['rate'] ;
         
        //akun potongan penjualan  
        $rsCOA = $coaLink->getCOALink ('salesservicediscount', $warehouse->tableName,$warehousekey, 0);
        $temp++; 
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey']; 
        $arr['debit'][$temp] = $total; 
        $arr['credit'][$temp] = 0;  
        
        $temp++; 
//        $rsCOA = $coaLink->getCOALink ('ar', $warehouse->tableName,$warehousekey, 0);
        $arr['hidCOAKey'][$temp] = $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $total;  
        
 
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
       
    function validateCancel($rsHeader, $autoChangeStatus = false){
         
        // credit note hanya bisa cancel jika ar statusnya open / cancel
        // dengan kata lain kalo AR statusnya paid / partial, gagal cancel
        
        $id = $rsHeader[0]['pkey'];
  
        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
		$rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey in (2,3))');
		if(!empty($rsAR)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2],true);
     } 
     
    function cancelTrans($rsHeader,$copy){  
         
        // kalo cancel credit note, semua AR yg berhubungan harus dicancel jg
        
		$id = $rsHeader[0]['pkey'];
         
        $ar = new AR();
        $rsObjKey = $this->getTableKeyAndObj($this->tableName); 
        
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) {  
			$arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
		if ($copy)
			$this->copyDataOnCancel($id);	  
         
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
      
    function getCreditNoteByAR($arkey,$criteria=''){
        
        $sql = 'select   
            '.$this->tableName.'.pkey ,
            '.$this->tableName.'.code 
          from
            '.$this->tableNameDetail.',
            '.$this->tableName.' 
          where  
            '. $this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and  
            '. $this->tableNameDetail.'.arkey  = '.$this->oDbCon->paramString($arkey);

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);

    } 
    
    
    function getCreditNoteDetailByAR($arkeys,$summary = false ){
        if(!is_array($arkeys)) $arkeys = array($arkeys);
     
        // hitung semua CN yagn sudah diproses,
        // harus cari yg headerny 2 dan 3
        
        $sqlSelect = $this->tableNameDetail.'.totalcredit';
        $sqlGroup = '';
        
        if ($summary) {
         
            $sqlSelect = 'coalesce( sum('.$this->tableNameDetail.'.totalcredit) ,0) as totalcredit';
            $sqlGroup = ' group by '.$this->tableNameDetail.'.arkey';
        }
        
        $sql = 'select 
                        '.$sqlSelect.',
                        '.$this->tableNameDetail.'.arkey
                from  '.$this->tableName.', '.$this->tableNameDetail.' 
                where '.$this->tableName.'.pkey =  '.$this->tableNameDetail.'.refkey and
                      '.$this->tableName.'.statuskey in (2,3) and
                      '.$this->tableNameDetail.'.arkey in ('.$this->oDbCon->paramString($arkeys,',').') 
                ';     
        
        $sql .= $sqlGroup;
          
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }
    
    
	
    function getCreditNoteByEMKLJO($jokey,$criteria=''){
		// return group by CN pkey
		$emklOrderInvoice = new EMKLOrderInvoice();
		
		// gk perlu join semua dulu, karena CN harusnya gk byk
        // select semua CN yg detailkeynya ad invoice yg mengandung jokey yg dicari
		// setelah itu baru di proporsionalkan
		
//		$sql = ' select 
//					distinct('.$this->tableName.'.pkey)
//				 from 
//				 	'.$this->tableName.', 
//				 	'.$this->tableNameDetail.'
//				 where 
//				 	'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and  
//					'.$this->tableNameDetail.'.invoicekey in (
//				 		select refkey from '.$this->tableEMKLInvoiceDetail.' where refsalesorderheaderkey  in ('.$this->oDbCon->paramString($jokey,',').')
//				 	)
//				 and  '.$this->tableName.'.statuskey <> 4
//			   ';
//		

			$sql = ' select 
				'.$this->tableName.'.pkey ,
				'.$this->tableName.'.code ,
				'.$this->tableName.'.customerkey ,
				'.$this->tableName.'.currencykey ,
				'.$this->tableName.'.statuskey ,
				'.$this->tableName.'.grandtotal ,
				'.$this->tableNameDetail.'.rate, 
				'.$this->tableNameDetail.'.totalcredit, 
				'.$this->tableEMKLJobOrderHeader.'.pkey as jokey,
				'.$this->tableEMKLInvoiceHeader.'.pkey as invoicekey, 
				'.$this->tableEMKLInvoiceHeader.'.code as invoicecode, 
				'.$this->tableEMKLInvoiceDetail.'.amount invoicedetailamount, 
				'.$this->tableEMKLJobOrderHeader.'.code as jocode, 
				'.$this->tableEMKLJobOrderDetail.'.code as jodetailcode, 
				'.$this->tableCustomer.'.name as customername
			from 
				'.$this->tableName.',
				'.$this->tableCustomer.',
				'.$this->tableNameDetail.',
				'.$this->tableEMKLInvoiceHeader.',
				'.$this->tableEMKLInvoiceDetail.',
				'.$this->tableEMKLJobOrderHeader.',
				'.$this->tableEMKLJobOrderDetail.'
			 where 
				'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
				'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and 
				'.$this->tableNameDetail.'.invoicekey = '.$this->tableEMKLInvoiceHeader.'.pkey and
				'.$this->tableEMKLInvoiceHeader.'.pkey = '.$this->tableEMKLInvoiceDetail.'.refkey and
				'.$this->tableEMKLInvoiceDetail.'.refsalesorderheaderkey  in ('.$this->oDbCon->paramString($jokey,',').') and
				'.$this->tableEMKLInvoiceDetail.'.salesorderkey = '.$this->tableEMKLJobOrderDetail.'.pkey and
				'.$this->tableEMKLJobOrderHeader.'.pkey = '.$this->tableEMKLJobOrderDetail.'.refkey 
		   ';
		
        $sql .= $criteria;
		//$this->setLog($sql,true);
		
		$rs = $this->oDbCon->doQuery($sql);
		
		// reindex dulu per JO
		$rsCNGroup = $this->reindexDetailCollections($rs,'pkey'); 
		
		// update ulang pricenya sesuai dengan proportional 
		
		// PER CN
		foreach($rsCNGroup as $cnkey=>$rsCN) {

			$arrInvoiceKey = array_column($rsCN,'invoicekey'); 
			$rsInvoiceCol = $emklOrderInvoice->getDetailByColumn('','',true,' and '.$emklOrderInvoice->tableNameDetail.'.refkey in ('.$this->oDbCon->paramString($arrInvoiceKey,',').') ');
			$rsInvoiceCol = $this->reindexDetailCollections($rsInvoiceCol,'refkey'); 
			
			//per detail CN
			
			foreach($rsCN as $cndetailkey=>$cnDetaiLRow) {
				$invoicekey = $cnDetaiLRow['invoicekey'];
				$rsInvoice = $rsInvoiceCol[$invoicekey];
				 
				$totalInvoice = 0;
				$joInvoiced = 0;
				foreach($rsInvoice as $row){  
					// sekalian cari nominal tagihan utk JO nya
					if($cnDetaiLRow['jokey'] == $row['refsalesorderheaderkey'])
							$joInvoiced = $row['amount'];
					 
					$totalInvoice += $row['amount'];
				}
				
//				$this->setLog('***************',true);
//				$this->setLog($rsCNGroup[$cnkey][$cndetailkey]['totalcredit'],true);
//				$this->setLog($joInvoiced,true);
				
				$rsCNGroup[$cnkey][$cndetailkey]['totalcredit'] =  $rsCNGroup[$cnkey][$cndetailkey]['totalcredit'] * ( $joInvoiced / $totalInvoice) ; 
			}
			
			 
		}
		
   		//$this->setLog($rsCNGroup,true);
        return $rsCNGroup; 
    } 
	 
	
	 function afterStatusChanged($rsHeader){   
		// asumsi baru dari Job Order
		 
	    $emklJobOrder = new EMKLJobOrder();
      	$emklOrderInvoice = new EMKLOrderInvoice();
		 
		//$rsJOType = $this->getTableKeyAndObj($emklJobOrder->tableName,array('key'));
        
        // retrieve latest status
        //$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
            
        // dr invoice EMKL
		// gk bisa pake refkey, karena invoice refkeynya
		 
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		$arrInvoiceKey = array_column($rsDetail,'invoicekey');
			
		$rsDetailInvoice =  $emklOrderInvoice->getDetailByColumn('','',true,' and '.$emklOrderInvoice->tableNameDetail.'.refkey in ('.$this->oDbCon->paramString($arrInvoiceKey,',').')');
		
		// call ulang dr JO utk hitung ulang, ad kemungkinan 1 JO bisa beberapa CN
		$emklJobOrder->updateTotalCreditNote(array_column($rsDetailInvoice,'refsalesorderheaderkey'));	
	}
        
    function getTransactionObject(){
        return new AR();
    }

    function getCreditNoteByEMKLInvoice($invoicekeys,$inIDR = true,$criteria=''){
        
        if(!is_array($invoicekeys)) $invoicekeys = array($invoicekeys);
     
            if($inIDR){ 
                $query = 'coalesce(sum('.$this->tableNameDetail.'.totalcredit * '.$this->tableNameDetail.'.rate),0) as totalcredit,
                            1 as rate'; 
            }else{
                $query = 'coalesce(sum(' . $this->tableNameDetail . '.totalcredit),0) as totalcredit';
            }
        
            $sql = 'select 
                    '.$query.',
                    '.$this->tableNameDetail.'.invoicekey,
                    '.$this->tableNameDetail.'.rate,
                    '.$this->tableName.'.currencykey
                from    
                    '.$this->tableName.', 
                    '.$this->tableNameDetail.' 
                where 
                    '.$this->tableName.'.pkey =  '.$this->tableNameDetail.'.refkey and
                    '.$this->tableName.'.statuskey in (2,3) and
                    ' .$this->tableNameDetail.'.invoicekey in ('.$this->oDbCon->paramString($invoicekeys,',').') 
                ';  
                
            if(!empty($criteria)) {
                $sql .= $criteria;
            }
            
            if($inIDR) {
                $sql .= ' group by '.$this->tableNameDetail.'.invoicekey';
            }else{
                $sql .= ' group by '.$this->tableNameDetail.'.invoicekey, '. $this->tableName.'.currencykey';
            }
            
            $rs = $this->oDbCon->doQuery($sql);
            
            return $rs;
    }
     
}

?>
