<?php

class Amortization extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'amortization_header';
        $this->tableNameDetail = 'amortization_detail';
        $this->tableWarehouse = 'warehouse';
        $this->tableStatus = 'transaction_status';
        $this->tablePrepaidExpense = 'prepaid_expense';
        $this->tableService = 'item';
        
        $this->securityObject = 'Amortization';
        $this->isTransaction = true;
        $this->newLoad = true;
        
        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['refprepaidexpensekey'] = array('hidPrepaidExpenseKey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['amount'] = array('amount', 'number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trNotes');
        $this->arrData['total'] = array('total', 'number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['aging'] = array('aging');

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true,  'width' => 150, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true,  'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trnotes','default'=>true,  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'total', 'default' => true, 'width' => 150, 'align' => 'right', 'format' => 'number'));        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true,  'width' => 130));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Pool', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->includeClassDependencies(array(
            'PrepaidExpense.class.php',
            'Warehouse.class.php',
            'GeneralJournal.class.php',
        ));

        $this->overwriteConfig();
    }


    function getQuery(){

        $sql =  '
				select
					'.$this->tableName. '.*,
					'.$this->tableWarehouse.'.name as warehousename, 
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ',
                    '.$this->tableWarehouse.',  
                    '.$this->tableStatus.'  
				where  		
                '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 	    ' .$this->criteria ; 
		
        return $sql;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			    ' . $this->tableNameDetail . '.*,
                    '. $this->tablePrepaidExpense .'.code as prepaidexpensecode,
                    '. $this->tableService .'.name as servicename 
			    from
			  	    ' . $this->tableNameDetail . '
                    left join '. $this->tablePrepaidExpense .' on '. $this->tableNameDetail .'.refprepaidexpensekey = '. $this->tablePrepaidExpense .'.pkey
                    left join ' . $this->tableService . ' on ' . $this->tableNameDetail . '.itemkey = ' . $this->tableService . '.pkey 
			    where
			  	    ' . $this->tableNameDetail . '.refkey = ' . $this->oDbCon->paramString($pkey);


        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);

    }


    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $prepaidExpense = new PrepaidExpense();

        $arrPrepaidExpenseKey = $arr['hidPrepaidExpenseKey'];
        $arrItemKey = $arr['hidItemKey'];
        $arrAmount = $arr['amount'];
        
        if(empty($arrPrepaidExpenseKey[0])) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg[501]);
        } else {

            for ($i = 0; $i < count($arrPrepaidExpenseKey); $i++) {

                if(empty($arrPrepaidExpenseKey[$i])) {
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['prepaidExpense'][1]);
                } else {

                    $rsPrepaidExpense = $prepaidExpense->getDataRowById($arrPrepaidExpenseKey[$i]);

                    $amount = $this->unFormatNumber($arrAmount[$i]);

                    if ($rsPrepaidExpense[0]['costkey'] <> $arrItemKey[$i]) {
                        $this->addErrorList($arrayToJs,false, '<strong>'. $rsPrepaidExpense[0]['code'] .'. </strong>' . $this->errorMsg['expenseAccrual'][1]);
                    }

                    if($amount <= 0) {
                        $this->addErrorList($arrayToJs,false, '<strong>'. $rsPrepaidExpense[0]['code'] .'. </strong>' . $this->errorMsg[503]);
                    }

                    // if($rsPrepaidExpense[0]['priceinunit'] <> $amount) {
                    //     $this->addErrorList($arrayToJs,false, '<strong>'. $rsPrepaidExpense[0]['code'] .'. </strong>' . $this->errorMsg['expenseAccrual'][2]);
                    // }

                }

            }
        }


        return $arrayToJs;

    }

    function validateConfirm($rsHeader)
    {
        $prepaidExpense = new PrepaidExpense();
        
        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailById($id);

        foreach($rsDetail as $detail) {
            
            $rsPrepaidExpense = $prepaidExpense->getDataRowById($detail['refprepaidexpensekey']);

            if($rsPrepaidExpense[0]['outstanding'] <= 0) {
                $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'. </strong>'. $rsPrepaidExpense[0]['code']. ' - ' . $this->errorMsg['expenseAccrual'][3]);
            }

            if($rsPrepaidExpense[0]['statuskey'] == 4 || $rsPrepaidExpense[0]['statuskey'] == 3) {
                $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'. </strong>'. $rsPrepaidExpense[0]['code']. ' - ' . $this->errorMsg['expenseAccrual'][4]);
            }
            
            if($rsPrepaidExpense[0]['costkey'] <> $detail['itemkey']) {
                $this->addErrorLog(false, '<strong>'. $rsPrepaidExpense[0]['code'] .'. </strong>' . $this->errorMsg['expenseAccrual'][1]);
            }

            if ($detail['amount'] <= 0) {
                $this->addErrorLog(false, '<strong>'. $rsPrepaidExpense[0]['code'] .'. </strong>' . $this->errorMsg[503]);
            }

            // if($rsPrepaidExpense[0]['priceinunit'] <> $detail['amount']) {
            //     $this->addErrorLog(false,'<strong>'. $rsPrepaidExpense[0]['code'] .'. </strong>' . $this->errorMsg['expenseAccrual'][2]);
            // }
        
        }    

    }

    function confirmTrans($rsHeader)
    {
        $prepaidExpense = new PrepaidExpense();
        $id = $rsHeader[0]['pkey'];

        //update jurnal umum 
        $this->updateGL($rsHeader);

    }


    function validateCancel($rsHeader, $autoChangeStatus = false)
    {

        $id = $rsHeader[0]['pkey'];

    }

    function cancelTrans($rsHeader, $copy)
    {
    
        $id = $rsHeader[0]['pkey'];
        
        if ($copy)
            $this->copyDataOnCancel($id);

        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);

    }


    function afterStatusChanged($rsHeader){

        $prepaidExpense = new PrepaidExpense();

        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailById($id);

        for ($i = 0; $i < count($rsDetail); $i++) {
            $prepaidExpense->updateOutstandingAmortization($rsDetail[$i]['refprepaidexpensekey'], $rsDetail[$i]['refkey']);
        }

    }

    function updateGL($rs)
    {
        if (!USE_GL)
            return;

        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $supplier = new Supplier();
        $item = new Item();

        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
        $arr['createdBy'] = 0;
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        // desc
        $desc = array();
        array_push($desc, $rs[0]['refcode']);
        if (!empty($rs[0]['trnotes']))
            array_push($desc, $rs[0]['trnotes']);
        $arr['trDesc'] = implode(chr(13), $desc);


        $temp = -1;

        $rsDetail = $this->getDetailById($rs[0]['pkey']);

        $arrItemCOA = array();
        $arrItemCOA2 = array();
        foreach ($rsDetail as $detail) {

            $itemCOAKey = $item->getCostCOAKey($detail['itemkey'], $warehousekey, '', true);
            $itemCOAKey2 =  $item->getCostCOAKey($detail['itemkey'], $warehousekey, '');
            
            $totalItemValue = $detail['amount'];

            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue;
            $arrItemCOA2[$itemCOAKey2] = (!isset($arrItemCOA2[$itemCOAKey2])) ? $totalItemValue : $arrItemCOA2[$itemCOAKey2] + $totalItemValue;

        }

        foreach ($arrItemCOA as $coakey => $coaValue) {
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $coaValue;
            $arr['refCashBankKey'][$temp] = '';
        }

        foreach ($arrItemCOA2 as $coakey => $coaValue) {
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $coaValue;
            $arr['credit'][$temp] = 0;
            $arr['refCashBankKey'][$temp] = '';
        }

        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);

    }

    function reCountSubtotal($arrParam)
    {
        
        $arrPrepaidExpenseKey = $arrParam['hidPrepaidExpenseKey'];
        $arrAmount = $arrParam['amount'];
        
        $total = 0;
        for($i=0; $i<count($arrPrepaidExpenseKey); $i++) {
            
            if(empty($arrPrepaidExpenseKey[$i])) continue;
            
            $amount = $this->unFormatNumber($arrAmount[$i]);
            
            $total += $amount;

        }

        $reCountResult['total'] = $total;

        return $reCountResult;

    }

    function normalizeParameter($arrParam, $trim=false){

        $arrParam = parent::normalizeParameter($arrParam);

        $reCountResult = $this->reCountSubtotal($arrParam);

        $arrParam['total'] = $reCountResult['total'];

        return $arrParam;

    }

}

?>
