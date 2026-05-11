<?php

class AssetPurchase extends BaseClass{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'asset_purchase_header';
        $this->tableNameDetail = 'asset_purchase_detail'; 
        $this->tableSupplier = 'supplier';
        $this->tableWarehouse = 'warehouse'; 
		$this->tableAssetCategory = 'asset_category';
		$this->tableStatus = 'transaction_status';
        $this->tablePayment = 'asset_purchase_payment';
        
		$this->isTransaction = true;
        $this->newLoad = true;

        $this->securityObject = 'AssetPurchase'; 
 
        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['name'] = array('detailName', array('mandatory' => true));
        $this->arrDataDetail['qty'] = array('qty', 'number');
        $this->arrDataDetail['categorykey'] = array('selCategoryKey');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit', 'number');
        $this->arrDataDetail['subtotal'] = array('detailSubtotal', 'number'); 

        $this->arrPaymentDetail = array();
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod', array('mandatory' => true));

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['reftabletype'] = array('selType');
        $this->arrData['refkey'] = array('hidPurchaseRequestKey');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'number');

        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType', 'number');
        $this->arrData['finaldiscount'] = array('finalDiscount', 'number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax');
        $this->arrData['etccost'] = array('etcCost', 'number');
        $this->arrData['grandtotal'] = array('grandtotal', 'number');
        $this->arrData['totalpayment'] = array('totalPayment', 'number');
        $this->arrData['isfullreceive'] = array('chkIsFullReceive');
        $this->arrData['balance'] = array('balance', 'number');
        $this->arrData['refinvoicecode'] = array('refInvoiceCode');
        $this->arrData['statuskey'] = array('selStatus');
 
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'grandtotal', 'default' => true, 'width' => 150, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
 

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama Gudang ', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Pemasok ', $this->tableSupplier . '.name'));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/assetPurchase'));

        $this->includeClassDependencies(array(
            'AP.class.php',
            'Asset.class.php',
            'AssetMovement.class.php',
            'CashBank.class.php',
            'COALink.class.php',
            'GeneralJournal.class.php',
            'Item.class.php',
            'ItemUnit.class.php',  
            'PaymentMethod.class.php', 
            'Supplier.class.php',
            'AssetCategory.class.php',
            'TermOfPayment.class.php',
        ));
 
        $this->overwriteConfig();
    }

    function getQuery()
    {

        $sql = '
			SELECT ' . $this->tableName . '.* ,
			   ' . $this->tableWarehouse . '.name as warehousename,
			   ' . $this->tableSupplier . '.name as suppliername,
			   ' . $this->tableStatus . '.status as statusname
			FROM 
                 ' . $this->tableName . '
                 left join ' . $this->tableWarehouse . ' on ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey
                 left join ' . $this->tableSupplier . ' on ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey, 
                 ' . $this->tableStatus . '
			WHERE '
            . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		' . $this->criteria;
        $sql .=  $this->getCompanyCriteria();
        $sql .=  $this->getWarehouseCriteria();

        return $sql;
    }

    function reCountSubtotal($arrParam)
    {
        $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;

        $subtotal = 0;
        $grandtotal = 0;

        $hidDetailKey = $arrParam['hidDetailKey'];

        $taxValue = $this->unFormatNumber($arrParam['taxValue']);
        $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']);
        $finalDiscountType = $arrParam['selFinalDiscountType'];
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
        $etcCost = $this->unFormatNumber($arrParam['etcCost']);
        
		for ($i = 0; $i < count($hidDetailKey); $i++) {
            $detailQty = $this->unFormatNumber($arrParam['qty'][$i]);
            $detailPrice = $this->unFormatNumber($arrParam['priceInUnit'][$i]);
            $detailSubtotal = $detailQty * $detailPrice;
			
            $arrParam['detailSubtotal'][$i] = $detailSubtotal;

            $subtotal += $detailSubtotal;
        }

        $grandtotal = $subtotal;

        if ($finalDiscount != 0) {
            if ($finalDiscountType == 2)
                $finalDiscount = $finalDiscount / 100 * $grandtotal;
        }

        $beforeTaxTotal = $subtotal - $finalDiscount;
        $grandtotal = $beforeTaxTotal;

        if ($isPriceIncludeTax == false) {
            $taxValue = $beforeTaxTotal * $taxPercentage / 100;
            $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AP nanti
            $grandtotal += $taxValue;
        } else {
            $taxValue = ($taxPercentage / (100 + $taxPercentage)) * $grandtotal;
            $beforeTaxTotal = $grandtotal - $taxValue;
        }

        $grandtotal +=  $etcCost;
 
        $balance = 0;
        $totalPayment = 0;

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);
        if ($rsTOP[0]['duedays'] == 0) {
            $payment = $arrParam['paymentMethodValue'];
            for ($i = 0; $i < count($payment); $i++) {
                $totalPayment += $this->unFormatNumber($payment[$i]);
            }
        }
        $balance = $totalPayment - $grandtotal;

        $reCountResult = array();
        $reCountResult['subtotal'] = $subtotal;
        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
        $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
        $reCountResult['grandtotal'] = $grandtotal;
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['balance'] = $balance;
        $reCountResult['detailSubtotal'] = $arrParam['detailSubtotal'];

        return $reCountResult;
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $termOfPayment = new TermOfPayment();
 
		$itemName = $arrParam['detailName'];
        for($i=0;$i<count($itemName); $i++) { 
            $arrParam['unitConvMultiplier'][$i] = 1; 
            $arrParam['qty'][$i] = 1; 
        }
		
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);
        if ($rsTOP[0]['duedays'] != 0) {
            for ($i = 0; $i < count($arrParam['paymentMethodValue']); $i++) {
                $arrParam['paymentMethodValue'][$i] = 0;
                $arrParam['hidDetailPaymentKey'][$i] = 0;
            }
        }


     
        $reCountResult = $this->reCountSubtotal($arrParam);
        $arrParam['subtotal'] = $reCountResult['subtotal'];
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['balance'] = $reCountResult['balance'];
        $arrParam['detailSubtotal'] = $reCountResult['detailSubtotal'];

        $arrParam = parent::normalizeParameter($arrParam,true);

        return $arrParam;
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $supplierkey = $arr['hidSupplierKey'];
        $arrItemName = $arr['detailName']; 
        $arrPriceinunit = $arr['priceInUnit'];   
 
        if (empty($supplierkey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['supplier'][1]);
        }

		for($i=0;$i<count($arrPriceinunit);$i++) {   
			if ( $this->unFormatNumber($arrPriceinunit[$i]) <= 0){  
				$this->addErrorList($arrayToJs,false,$arrItemName[$i]. '. ' . $this->errorMsg[511]);  
			}   
		}

        $arrDetailKeys = array();


        return $arrayToJs;
    }
 
  
    function validateConfirm($rsHeader)  {

        $warehouse = new Warehouse();
        $coaLink = new COALink();

        $id = $rsHeader[0]['pkey'];
 

        $rsPayment = $this->getPaymentMethodDetail($id);
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        $totalPayment = 0;
        for ($i = 0; $i < count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'];

        if ($isCash) {
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if ($balance < ($thresholdDiscount * -1))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[509]);
        }

		
        if (USE_GL) {
            $arrCOA = array();
            array_push($arrCOA, 'taxin', 'othercost',  'purchaseretaildiscount', 'shippingcost', 'otherrevenue');
            for ($i = 0; $i < count($arrCOA); $i++) {
                $rsCOA = $coaLink->getCOALink($arrCOA[$i], $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
                if (empty($rsCOA))
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $arrCOA[$i] . ' ' . $this->errorMsg['coa'][3]);
            }

            if ($isCash) {
                for ($i = 0; $i < count($rsPayment); $i++) {
                    if ($rsPayment[$i]['amount'] > 0) {
                        $rsCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
                        if (empty($rsCOA))
                            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['coa'][3]);
                    }
                }
            } else {
                $rsCOA = $coaLink->getCOALink('ap', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
                if (empty($rsCOA))
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['coa'][3]);
            }
        }
    }

    function confirmTrans($rsHeader){

        $id = $rsHeader[0]['pkey'];

        $supplier = new Supplier();
        $warehouse = new Warehouse();
        $coaLink = new COALink();

        $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
        $note = $rsHeader[0]['code'] . '. Beli dari ' . $rsSupplier[0]['name'];
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        $rsPayment = array();

        // MENGHITUNG PAYMENT
        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($id);
            if (ADV_FINANCE) {

                $cashBank = new CashBank();
                for ($i = 0; $i < count($rsPayment); $i++) {
                    if ($rsPayment[$i]['amount'] == 0) continue;

                    if (USE_GL) {
                        $rsPaymentCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
                        $coakey = $rsPaymentCOA[0]['coakey'];
                    } else {
                        $coakey = $rsPayment[$i]['paymentkey'];
                    }

                    $rsCashBank = $cashBank->addCashBank($rsHeader, $this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'], 'coakey' => $coakey, 'desc' => $note, 'amount' => -$rsPayment[$i]['amount']));
                    $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
                }
            }
        } else {
            //update AP
            $ap = new AP();
            $arrParam = array();

            $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefTable'] = $rsAPKey['key'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['amount'] = abs($rsHeader[0]['grandtotal']);
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P' . $rsTOP[0]['duedays'] . 'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y'); // date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['islinked'] = 1;
            $arrParam['overwriteGL'] = 1;
            $arrParam['selAPType'] = 1;

            $arrayToJs = $ap->addData($arrParam);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
        }
        // END   
        
		// update daftar asset
		
		$assetCategory = new AssetCategory();
		$rsAssetCategory = $assetCategory->searchDataRow(array($assetCategory->tableName.'.pkey',$assetCategory->tableName.'.aging'), 
														 ' and '.$assetCategory->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'categorykey') ,',').')');
		$rsAssetCategory = array_column($rsAssetCategory,'aging','pkey');
		
		$isPriceIncludeTax = ($rsHeader[0]['ispriceincludetax'] == 1) ? true : false;
		$taxPercentage = $rsHeader[0]['taxpercentage'];
			
		foreach($rsDetail as $detailRow){
			$asset = new Asset();
			$arrParam = array();
 
			
			$acquisitionValue = $detailRow['priceinunit'];  
			if ($isPriceIncludeTax == true) {
				$taxValue = ($taxPercentage / (100 + $taxPercentage)) * $acquisitionValue;
				$acquisitionValue = $acquisitionValue - $taxValue;
			}

			
			$arrParam['code'] = 'xxxxxx';
			$arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
			$arrParam['name'] = $detailRow['name'];
			$arrParam['hidPurchaseKey'] = $id;   
			$arrParam['selCategory'] = $detailRow['categorykey']; 
			$arrParam['selStatus'] = 1;
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['bookValue'] = $acquisitionValue;
            $arrParam['acquisitionValue'] = $acquisitionValue;
            $arrParam['acquisitionDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y'); 
			
			$aging = $rsAssetCategory[$detailRow['categorykey']]; 
			// nanti dihitugn ulang ketik add asset
            //$arrParam['depreciationValue'] = $asset->calculateDepreciationValue($acquisitionValue,$rsAssetCategory[$detailRow['categorykey']] );
				
			$arrParam['createdBy'] = 0;
            $arrParam['islinked'] = 1;
          
			$arrayToJs = $asset->addData($arrParam);
			if (!$arrayToJs[0]['valid'])
				throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
 
 
            
             //update asset movement
             $assetMovement = new AssetMovement();  
             $assetMovement->updateItemMovement($id,$arrayToJs[0]['data']['pkey'],$detailRow['qty'],$acquisitionValue,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);

            		}
		
			
        //update jurnal umum 
        $this->updateGL($rsHeader, $rsPayment);
    }

	
	function cancelTrans($rsHeader,$copy){  
		
		$id = $rsHeader[0]['pkey']; 
   
		$ap = new AP();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key')); 
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsAPKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsAP);$i++) {
            $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'],4,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }	
            

        $assetMovement = new AssetMovement();  
        $assetMovement->cancelMovement($id,$this->tableName);
            
        $cashBank = new CashBank();
        $cashBank->cancelCashBank($rsHeader,$this->tableName);
		if ($copy) $this->copyDataOnCancel($id);	  
	 
		// cancel asset
		$asset = new Asset();
		$rsAsset = $asset->searchDataRow(array($asset->tableName.'.pkey'), 
										 ' and '.$asset->tableName.'.refpurchasekey = ' .$this->oDbCon->paramString($id) 
										);
		
		foreach($rsAsset as $assetRow)
			$asset->delete($assetRow['pkey'],true);
		
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
	
	 
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
		$id = $rsHeader[0]['pkey'];
  
		//cek ad AP terbayar
		$ap = new AP(); 
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));  
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$ap->tableName.'.statuskey in (2,3))');
		
		if(!empty($rsAP))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);

		// cek asset sudah disusutkan atau belum 
		$asset = new Asset();
		$rsAsset = $asset->searchDataRow(array($asset->tableName.'.pkey',$asset->tableName.'.code',$asset->tableName.'.totaldepreciatedctr'), 
										 ' and '.$asset->tableName.'.refpurchasekey = ' .$this->oDbCon->paramString($id) 
										);
		
		foreach($rsAsset as $assetRow){
			if($assetRow['totaldepreciatedctr'] > 0)	
				$this->addErrorLog(false,'<strong>'.$assetRow['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['asset'][4]);
  
            if($assetRow['statuskey'] == 2)
                $this->addErrorLog(false,'<strong>'.$assetRow['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['asset'][5]);
		}
 	 }
	

    function updateGL($rs, $rsPayment)
    {
   		if (!USE_GL) return;

        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink(); 
		$asset = new Asset();
		$supplier = new Supplier();

        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key'));
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
        $arr['createdBy'] = 0;
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
 

        $temp = -1;

        //$rsDetail = $this->getDetailById($rs[0]['pkey']);  
		$rsAsset = $asset->searchDataRow( array($asset->tableName.'.pkey', $asset->tableName.'.name', $asset->tableName.'.acquisitionvalue'), 
										' and '.$asset->tableName.'.refpurchasekey = ' . $this->oDbCon->paramString($rs[0]['pkey'])
										);
		
		
        $arr['trDesc'] = implode(', ', array_column($rsAsset,'name'));
		
		$rsCOA = $asset->getAssetCOAKey(array_column($rsAsset,'pkey'),$warehousekey);
		$rsCOA = array_column($rsCOA,null,'pkey');
		
		// gabungin dulu per COA
		
		$arrCOACol = array();
		
        foreach ($rsAsset as $detail) { 
			$arrCOAkey = $rsCOA[$detail['pkey']];
		
			$coakey = $arrCOAkey['coaassetkey'];
			if(!isset($arrCOACol[$coakey])){ 
				$arrCOACol[$coakey]['debit'] = 0;
				$arrCOACol[$coakey]['credit'] = 0;
				$arrCOACol[$coakey]['desc'] = array();
			}
			
			$arrCOACol[$coakey]['debit'] += $detail['acquisitionvalue']; 
 			array_push($arrCOACol[$coakey]['desc'],$detail['name']);
        }
		
        foreach ($arrCOACol as $coakey=>$detail) {   
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $detail['debit'];
            $arr['credit'][$temp] = $detail['credit']; 
            $arr['trdescDetail'][$temp] = implode(', ',$detail['desc']); 
            $arr['refCashBankKey'][$temp] = '';    
        }
 
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
                 $arr['credit'][$temp] =  $rsPayment[$i]['amount'];  
                 $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];
				 $arr['trdescDetail'][$temp] = '';
                 $totalPayment += $rsPayment[$i]['amount'];  
            }
		
             //selisih pembayaran  
            
            if($rs[0]['balance'] != 0){ 
                $temp++; 
                if ($rs[0]['balance'] < 0){ 
                    $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] = abs($rs[0]['balance']); 
                }else{ 
                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = abs($rs[0]['balance']);  
                    $arr['credit'][$temp] = 0;
                }
             
                $arr['refCashBankKey'][$temp] = '';        
				$arr['trdescDetail'][$temp] = '';
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            }

        }else {  
                $temp++;
                $arr['hidCOAKey'][$temp] = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] =  $rs[0]['grandtotal']; 
                $arr['refCashBankKey'][$temp] = '';    
				$arr['trdescDetail'][$temp] = '';
        }
         
		
		         
        $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName,$warehousekey, 0); 
	    $temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] =  $rs[0]['taxvalue']; 
		$arr['credit'][$temp] = 0; 
        $arr['refCashBankKey'][$temp] = ''; 
		$arr['trdescDetail'][$temp] = '';
		 
	 
        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
    }
 
    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			'.$this->tableNameDetail.'.*,
				'.$this->tableAssetCategory.'.name as categoryname
			  from
			  	' . $this->tableNameDetail . ',
				'.$this->tableAssetCategory.'
			  where
			    ' . $this->tableNameDetail . '.categorykey = '.$this->tableAssetCategory.'.pkey and
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

     

//    function updatePurchaseOrderReceivedItem($pkey)
//    { 
//        $rsHeader = $this->getDataRowById($pkey);
//        $rsDetail = $this->getDetailById($pkey);
//
//        for ($i = 0; $i < count($rsDetail); $i++) {
//            $sql = 'select 
//                        coalesce(sum(receivedqtyinbaseunit),0) as totalreceivedqtyinbaseunit
//                    from 
//                        ' . $purchaseReceive->tableName . ', ' . $purchaseReceive->tableNameDetail . '
//                    where 
//                         ' . $purchaseReceive->tableName . '.pkey = ' . $purchaseReceive->tableNameDetail . '.refkey and
//                         ' . $purchaseReceive->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and
//                         ' . $purchaseReceive->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] . ' and 
//                         ' . $purchaseReceive->tableNameDetail . '.refpodetailkey = ' . $rsDetail[$i]['pkey'] . ' and 
//                         (statuskey = 2 or statuskey = 3)';
//
//            $rsTotal = $this->oDbCon->doQuery($sql);
//
//            // INI AKAN PROBLEM KALO DETAIL PUNYA 2 ITEM YG SAMA
//            $sql = 'update 
//                            ' . $this->tableNameDetail . ' 
//                        set  
//                            receivedqtyinbaseunit = ' . $rsTotal[0]['totalreceivedqtyinbaseunit'] . '
//                        where 
//                            refkey = ' . $pkey . ' and 
//                            pkey = ' . $rsDetail[$i]['pkey'] . ' and 
//                            itemkey = ' . $rsDetail[$i]['itemkey'];
//
//            $this->oDbCon->execute($sql);
//        }
//
//        //check if all item received, change PO status to finish
//        $sql = 'select * from ' . $this->tableNameDetail . ' where refkey = ' . $this->oDbCon->paramString($pkey) . ' and  receivedqtyinbaseunit < qtyinbaseunit';
//        $rs = $this->oDbCon->doQuery($sql);
//
//        $statuskey = (empty($rs)) ? 3 : 2;
//
//        if ($rsHeader[0]['statuskey'] <> $statuskey)
//            $this->changeStatus($pkey, $statuskey, '', false, true);
//    }
}
