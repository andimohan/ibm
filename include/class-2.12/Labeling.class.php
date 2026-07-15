<?php

class Labeling extends BaseClass
{
    function __construct()
    {

        parent::__construct();

        $this->tableName = 'labeling_header';
        $this->tableNameDetail = 'labeling_detail';
        $this->tableStatus = 'transaction_status';
        $this->tableItemUnit = 'item_unit';
        $this->tableItemReceivingHeader = 'item_receiving_header';
        $this->tableItemReceivingDetail = 'item_receiving_detail';

        $this->securityObject = 'Labeling';

        $this->isTransaction = true;

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['refreceivingheaderkey'] = array('hidRefReceivingHeaderKey');
        $this->arrDataDetail['refreceivingdetailkey'] = array('hidRefReceivingDetailKey');
        $this->arrDataDetail['submissionnumber'] = array('submissionNumber');
        $this->arrDataDetail['itemcode'] = array('itemCode');
        $this->arrDataDetail['itemname'] = array('itemName');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qtyitem'] = array('qtyItem', 'number');
        $this->arrDataDetail['qtylabeled'] = array('qtyLabeled', 'number');
        $this->arrDataDetail['qty'] = array('qty', 'number');
        $this->arrDataDetail['unitkey'] = array('selUnit');

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 120, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 80));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->includeClassDependencies(array(
            'ItemReceiving.class.php'
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {

        $sql = '
			select 
                ' . $this->tableName . '.* ,
			   ' . $this->tableStatus . '.status as statusname 
			from 
                ' . $this->tableName . ',  
         		 ' . $this->tableStatus . '
			where 
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
                ' . $this->tableItemReceivingHeader . '.submissionnumber
                 from
			  	    ' . $this->tableNameDetail . '
                        left join ' . $this->tableItemReceivingHeader . ' on ' . $this->tableNameDetail . '.refreceivingheaderkey = ' . $this->tableItemReceivingHeader . '.pkey
                where
                    ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);

    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $arrDetailKey = $arr['hidDetailKey'];
        $arrReceivingHeaderKey = $arr['hidRefReceivingHeaderKey'];
        $arrReceivingDetailKey = $arr['hidRefReceivingDetailKey'];
        $arrItemReceiving = $arr['itemReceiving'];
        $arrQty = $arr['qty'];
        $arrSubmission = $arr['submissionNumber'];
        $arrItemCode = $arr['itemCode'];

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

    function afterStatusChanged($rsHeader)
    {
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        $itemReceiving = new ItemReceiving();

        foreach ($rsDetail as $row) {
            $itemReceiving->updateQtyLabeled($row['refreceivingdetailkey']);
        }
    }

    function validateConfirm($rsHeader)
    {
        $itemReceiving = new ItemReceiving();

        $id = $rsHeader[0]['pkey'];

        $rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $arrErrMsg = array();
        $arrDetails = array();

        $arrItemReceivingHeaderKey = array_column($rsDetail, 'refreceivingheaderkey');
        $rsReceiving = $this->searchDataRow(array(
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
            if (isset($rsReceivingCol[$rsDetail[$i]['refreceivingheaderkey']])) {
                array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['receivingcode'] . '. ' . $rsDetail[$i]['submissionnumber'] . ' - ' . $rsDetail[$i]['itemcode'] . '. </strong>' . $this->errorMsg[228]);
            }

            if ($rsDetail[$i]['qty'] <= 0) {
                array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['receivingcode'] . '. ' . $rsDetail[$i]['submissionnumber'] . ' - ' . $rsDetail[$i]['itemcode'] . '. </strong>' . $this->errorMsg[503]);
            } else {

                $rsOutstanding = $itemReceiving->getTotalUnLabelingItemReceiving($rsDetail[$i]['refreceivingdetailkey']);

                if ($rsDetail[$i]['qty'] > $rsOutstanding) {
                    array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['receivingcode'] . '. ' . $rsDetail[$i]['submissionnumber'] . ' - ' . $rsDetail[$i]['itemcode'] . '. </strong>' . $this->errorMsg['labeling'][1]);
                }
            }
        }

        if (!empty($arrErrMsg)) {
            $this->addErrorLog(false, '<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br>' . implode('<br>', $arrErrMsg));
        }

    }

    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

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

    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam, true);
        return $arrParam;
    }


}

?>