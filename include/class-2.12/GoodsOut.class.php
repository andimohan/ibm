<?php

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\BaseParserClass;

class GoodsOut extends BaseClass
{
    function __construct()
    {
        parent::__construct();

        $this->tableName = 'goods_out_header';
        $this->tableNameDetail = 'goods_out_detail';
        $this->tableStatus = 'transaction_status';
        $this->tableItemUnit = 'item_unit';
        $this->tableItemReceivingHeader = 'item_receiving_header';
        $this->tableItemReceivingDetail = 'item_receiving_detail';
        $this->tableDocumentType = 'document_type';
        $this->tableWarehouseLayout = 'warehouse_layout';

        $this->securityObject = 'GoodsOut';

        $this->isTransaction = true;

        $this->uploadFileFolder = 'goods-out/';
        $this->useStorage = array(); //$this->useStorage('S3');

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['refreceivingheaderkey'] = array('hidRefReceivingHeaderKey');
        $this->arrDataDetail['refreceivingdetailkey'] = array('hidRefReceivingDetailKey');
        $this->arrDataDetail['warehouselayoutkey'] = array('hidWarehouseLayoutDetailKey');
        $this->arrDataDetail['submissionnumber'] = array('submissionDetailNumber');
        $this->arrDataDetail['itemcode'] = array('itemCode');
        $this->arrDataDetail['itemname'] = array('itemName');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['itemqty'] = array('itemQty', 'number');
        $this->arrDataDetail['issuedqty'] = array('issuedQty', 'number');
        $this->arrDataDetail['qty'] = array('qty', 'number');
        $this->arrDataDetail['amount'] = array('amount', 'number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['documenttypekey'] = array('selDocumentType');
        $this->arrData['submissionnumber'] = array('submissionNumber');
        $this->arrData['submissiondate'] = array('submissionDate', 'date');
        $this->arrData['registrationnumber'] = array('registrationNumber');
        $this->arrData['registrationdate'] = array('registrationDate', 'date');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['recipient'] = array('recipient');
        $this->arrData['recipientaddress'] = array('recipientAddress');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['car'] = array('car');
        $this->arrData['driver'] = array('driver');

        $this->arrData['file'] = array('item-file-uploader', array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder, 'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'align' => 'center', 'format' => 'date', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'recipient', 'title' => 'recipient', 'dbfield' => 'recipient', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Penerima', $this->tableName . '.recipient'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'], 'icon' => 'print', 'url' => 'print/goodsOut'));

        $this->includeClassDependencies(array(
            'ItemReceiving.class.php',
            'DocumentType.class.php',
            'Customer.class.php',
            'itemMovement.class.php',
            'Currency.class.php'
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {

        $sql = '
			select 
                ' . $this->tableName . '.* ,
                ' . $this->tableCustomer . '.name as customername,
			   ' . $this->tableStatus . '.status as statusname 
			from 
                ' . $this->tableName . ',
                ' . $this->tableCustomer . ' ,
                ' . $this->tableStatus . '
			where 
                ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 	  ' . $this->criteria;

        $sql .= $this->getCompanyCriteria();

        return $sql;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                ' . $this->tableItemReceivingHeader . '.code as receivingcode,
                ' . $this->tableItemReceivingDetail . '.unit,
                ' . $this->tableItemReceivingDetail . '.containernumber,
                ' . $this->tableWarehouseLayout . '.name as warehouselayoutname,
                ' . $this->tableItemUnit . '.name as unitname,
                ' . $this->tableDocumentType . '.name as documenttypename 
                 from
			  	    ' . $this->tableNameDetail . '
                        left join ' . $this->tableItemReceivingHeader . ' on ' . $this->tableNameDetail . '.refreceivingheaderkey = ' . $this->tableItemReceivingHeader . '.pkey
                        left join ' . $this->tableWarehouseLayout . ' on ' . $this->tableNameDetail . '.warehouselayoutkey = ' . $this->tableWarehouseLayout . '.pkey
                        left join ' . $this->tableDocumentType . ' on ' . $this->tableItemReceivingHeader . '.documenttype = ' . $this->tableDocumentType . '.pkey
                        left join ' . $this->tableItemReceivingDetail . ' on ' . $this->tableNameDetail . '.refreceivingdetailkey = ' . $this->tableItemReceivingDetail . '.pkey
                        left join ' . $this->tableItemUnit . ' on ' . $this->tableItemReceivingDetail . '.unit = ' . $this->tableItemUnit . '.pkey
                where
                    ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);

    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $customerkey = $arr['hidCustomerKey'];
        $recipient = $arr['recipient'];
        $recipientAddress = $arr['recipientAddress'];
        $arrReceivingDetailKey = $arr['hidRefReceivingDetailKey'];
        $arrItemReceiving = $arr['itemReceiving'];
        $arrQty = $arr['qty'];
        $arrSubmission = $arr['submissionNumber'];
        $arrItemCode = $arr['itemCode'];
        $this->setLog($arr, true);

        if (empty($customerkey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }

        // if (empty($recipient)) {
        //     $this->addErrorList($arrayToJs, false, $this->errorMsg['recipient'][1]);
        // }

        // if (empty($recipientAddress)) {
        //     $this->addErrorList($arrayToJs, false, '<strong>' . $this->lang['recipient'] . '. </strong>' . $this->errorMsg['address'][1]);
        // }

        if (empty($arrReceivingDetailKey[0])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
        } else {

            $arrDetails = array();
            for ($i = 0; $i < count($arrReceivingDetailKey); $i++) {

                $itemReceiving = $arrItemReceiving[$i];
                $submissionNumber = $arrSubmission[$i];
                $qty = $this->unFormatNumber($arrQty[$i]);
                $itemCode = $arrItemCode[$i];

                if (in_array($arrReceivingDetailKey[$i], $arrDetails)) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $itemReceiving . '. ' . $submissionNumber . ' - ' . $itemCode . '. </strong>' . '. ' . $this->errorMsg[215]);
                } else {
                    array_push($arrDetails, $arrReceivingDetailKey[$i]);
                }

                if ($qty <= 0) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $itemReceiving . '. ' . $submissionNumber . ' - ' . $itemCode . '. </strong>' . $this->errorMsg[503]);
                }

            }
        }

        return $arrayToJs;
    }


    function validateConfirm($rsHeader)
    {


    }

    function afterStatusChanged($rsHeader)
    {
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        $itemReceiving = new ItemReceiving();

        foreach ($rsDetail as $row) {
            $itemReceiving->updateIssuedQty($row['refreceivingdetailkey']);
        }
    }

    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $itemMovement = new ItemMovement();
        $note = $rsHeader[0]['code'] . '. ' . $this->ucFirst($this->lang['itemOut']);

        for ($i = 0; $i < count($rsDetail); $i++) { 
            $itemMovement->updateItemMovement(
                            array(
                                'refkey' => $id,
                                'refkey2' => $rsDetail[$i]['refreceivingheaderkey'],
                                'refdetailkey' => $rsDetail[$i]['refreceivingdetailkey'],
                            ),
                            $rsDetail[$i]['itemkey'],
                            -$rsDetail[$i]['qty'],
                            0,
                            $this->tableName,
                            array(
                                'warehousekey' => $rsHeader[0]['warehousekey'],
                                'warehouselayoutkey' => $rsDetail[$i]['warehouselayoutkey']
                            ),
                            $note,
                            $rsHeader[0]['trdate']
                        );
        }

    }

    function validateClose($rsHeader)
    {
        parent::validateClose($rsHeader);

        $itemReceiving = new ItemReceiving();

        $id = $rsHeader[0]['pkey'];

        $rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $id = $rsHeader[0]['pkey'];

        $rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $arrErrMsg = array();
        $arrDetails = array();

        $arrItemReceivingHeaderKey = array_column($rsDetail, 'refreceivingheaderkey');
        $rsReceiving = $itemReceiving->searchDataRow(array(
            $itemReceiving->tableName . '.pkey',
            $itemReceiving->tableName . '.code',
            $itemReceiving->tableName . '.statuskey'
        ), ' and ' . $itemReceiving->tableName . '.pkey in (' . $this->oDbCon->paramString($arrItemReceivingHeaderKey, ',') . ') and ' . $itemReceiving->tableName . '.statuskey in (2,3) ');
        $rsReceivingCol = $this->reindexDetailCollections($rsReceiving, 'pkey');

        for ($i = 0; $i < count($rsDetail); $i++) {

            if (in_array($rsDetail[$i]['refreceivingdetailkey'], $arrDetails)) {
                array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['receivingcode'] . '. ' . $rsDetail[$i]['submissionnumber'] . ' - ' . $rsDetail[$i]['itemcode'] . '. </strong>' . '. ' . $this->errorMsg[215]);
            } else {
                array_push($arrDetails, $rsDetail[$i]['refreceivingdetailkey']);
            }

            //cek apakah status penerimaan masih konfirmasi / selesai ?
            if (!isset($rsReceivingCol[$rsDetail[$i]['refreceivingheaderkey']])) {
                array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['receivingcode'] . '. ' . $rsDetail[$i]['submissionnumber'] . ' - ' . $rsDetail[$i]['itemcode'] . '. </strong>' . $this->errorMsg[228]);
            }

            if ($rsDetail[$i]['qty'] <= 0) {
                array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['receivingcode'] . '. ' . $rsDetail[$i]['submissionnumber'] . ' - ' . $rsDetail[$i]['itemcode'] . '. </strong>' . $this->errorMsg[503]);
            } else {

                $rsOutstanding = $itemReceiving->getTotalUnIssuedItemReceiving($rsDetail[$i]['refreceivingdetailkey']);

                if ($rsDetail[$i]['qty'] > $rsOutstanding) {
                    array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['receivingcode'] . '. ' . $rsDetail[$i]['submissionnumber'] . ' - ' . $rsDetail[$i]['itemcode'] . '. </strong>' . $this->errorMsg['goodsOut'][1]);
                }
            }
        }

        if (!empty($arrErrMsg)) {
            $this->addErrorLog(false, '<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br>' . implode('<br>', $arrErrMsg));
        }

    }

    function closeTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $id = $rsHeader[0]['pkey'];

    }

    function cancelTrans($rsHeader, $copy)
    {

        $itemMovement = new ItemMovement();
        $id = $rsHeader[0]['pkey'];
        $itemMovement->cancelMovement($id, $this->tableName);

        if ($copy)
            $this->copyDataOnCancel($id);

    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

}

?>