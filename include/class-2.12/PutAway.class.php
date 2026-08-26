<?php

class PutAway extends BaseClass
{

    function __construct($typekey = '')
    {

        parent::__construct();

        $this->tableName = 'put_away_header';
        $this->tableNameDetail = 'put_away_detail';
        $this->tableWarehouse = 'warehouse';
        $this->tableItemReceiving = 'item_receiving_header';
        $this->tablePallet = 'pallet';
        $this->tableWarehouseLayout = 'warehouse_layout';
        $this->tableItem = 'item';
        $this->tableStatus = 'transaction_status';
        $this->tableItemReceivingDetail = 'item_receiving_detail';
        $this->tableDocumentType = 'document_type';
        $this->tableItemUnit = 'item_unit';

        $this->isTransaction = true;
        $this->typekey = $typekey;
        $this->securityObject = 'PutAway';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemreceivingheaderkey'] = array('hidItemReceivingHeaderKey');
        $this->arrDataDetail['itemreceivingdetailkey'] = array('hidItemReceivingDetailKey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['containernumber'] = array('containerNumber');
        $this->arrDataDetail['palletkey'] = array('hidPalletDetailKey');
        $this->arrDataDetail['warehouselayoutkey'] = array('hidZoneDetailKey');
        $this->arrDataDetail['receivingqty'] = array('receivingQty', 'number');
        $this->arrDataDetail['putawayqty'] = array('putAwayQty', 'number');
        $this->arrDataDetail['qty'] = array('qty', 'number');


        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['typekey'] = array('selTypeKey');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['warehousekey'] = array('hidWarehouseKey');
        $this->arrData['putawaydate'] = array('trPutAwayDate', 'date');
        $this->arrData['warehouselayoutkey'] = array('hidWarehouseLayoutKey');
        $this->arrData['warehouselayoutoriginkey'] = array('hidWarehouseLayoutOriginKey');
        $this->arrData['palletkey'] = array('hidPalletKey');
        $this->arrData['submissionnumber'] = array('submissionNumber');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 120));
        if ($this->typekey == 1) {
            array_push($this->arrDataListAvailableColumn, array('code' => 'location', 'title' => 'location', 'dbfield' => 'warehouselayoutname', 'default' => true, 'width' => 120));
            array_push($this->arrDataListAvailableColumn, array('code' => 'itemReceiving', 'title' => 'itemReceiving', 'dbfield' => 'refcode', 'default' => true, 'width' => 120));
        } else if ($this->typekey == 2) {
            array_push($this->arrDataListAvailableColumn, array('code' => 'locationOrigin', 'title' => 'origin', 'dbfield' => 'warehouselayoutoriginname', 'default' => true, 'width' => 150));
            array_push($this->arrDataListAvailableColumn, array('code' => 'locationDestination', 'title' => 'destination', 'dbfield' => 'warehouselayoutdestinationname', 'default' => true, 'width' => 150));
            array_push($this->arrDataListAvailableColumn, array('code' => 'submissionNumber', 'title' => 'submissionNumber', 'dbfield' => 'submissionnumber', 'default' => true, 'width' => 120));
        }

        array_push($this->arrDataListAvailableColumn, array('code' => 'pallet', 'title' => 'pallet', 'dbfield' => 'palletname', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));

        $this->arrSearchColumn = array(); 
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse. '.name')); 

        if ($this->typekey == 1) {
            array_push($this->arrSearchColumn, array('Penerimaan Barang', $this->tableItemReceiving. '.code')); 
            array_push($this->arrSearchColumn, array('No Pengajuan', $this->tableName. '.submissionnumber')); 
        }




        $this->printMenu = array();

        switch ($this->typekey) {
            case 1:
                $printUrl = 'print/putAway';
                break;
            case 2:
                $printUrl = '';
                break;
            case 3:
                $printUrl = 'print/pickingList';
                break;
            default:
                $printUrl = 'print/putAway';
        }

        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'], 'icon' => 'print', 'url' => $printUrl));


        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'WarehouseLayout.class.php',
            'Pallet.class.php',
            'ItemMovement.class.php',
            'ItemReceiving.class.php'
        ));

        $this->overwriteConfig();

    }


    function getQuery()
    {
        $sql = '
			SELECT ' . $this->tableName . '.* ,
                ' . $this->tableWarehouse . '.name as warehousename,
                ' . $this->tableWarehouseLayout . '.name as warehouselayoutname,
                layoutorigin.name as warehouselayoutoriginname,
                layoutdestination.name as warehouselayoutdestinationname,
                ' . $this->tablePallet . '.name as palletname,
                ' . $this->tableItemReceiving . '.code as refcode,
			    ' . $this->tableStatus . '.status as statusname,
                ' . $this->tableDocumentType . '.name as documenttypename 
			FROM 
                ' . $this->tableName . '
                        left join ' . $this->tableWarehouseLayout . ' on ' . $this->tableName . '.warehouselayoutkey = ' . $this->tableWarehouseLayout . '.pkey
                        left join ' . $this->tableWarehouseLayout . ' layoutorigin on ' . $this->tableName . '.warehouselayoutoriginkey = layoutorigin.pkey
                        left join ' . $this->tableWarehouseLayout . ' layoutdestination on ' . $this->tableName . '.warehouselayoutkey = layoutdestination.pkey
                        left join ' . $this->tablePallet . ' on ' . $this->tableName . '.palletkey = ' . $this->tablePallet . '.pkey,
                ' . $this->tableItemReceiving . '
                    left join ' . $this->tableDocumentType . ' on ' . $this->tableItemReceiving . '.documenttype = ' . $this->tableDocumentType . '.pkey,
                ' . $this->tableWarehouse . ',
                ' . $this->tableStatus . '
			WHERE 
                ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
                ' . $this->tableName . '.refkey = ' . $this->tableItemReceiving . '.pkey and
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
 		';

        if (!empty($this->typekey))
            $sql .= ' and  ' . $this->tableName . '.typekey in (' . $this->typekey . ')  ';

        $sql .= $this->criteria;
        $sql .= $this->getCompanyCriteria();

        return $sql;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                 ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItem . '.name as itemname,
                ' . $this->tablePallet . '.code as palletcode,
                ' . $this->tablePallet . '.name as palletname,
                ' . $this->tableItemReceivingDetail . '.containernumber as itemreceivingcontainernumber,
                ' . $this->tableItemReceivingDetail . '.itemcode,
                ' . $this->tableWarehouseLayout . '.name as warehouselayoutname,
                ' . $this->tableItemUnit . '.name as unitname
			  from
			  	' . $this->tableNameDetail . '
                    left join ' . $this->tablePallet . ' on ' . $this->tableNameDetail . '.palletkey = ' . $this->tablePallet . '.pkey
                    left join ' . $this->tableItemReceivingDetail . ' on ' . $this->tableNameDetail . '.itemreceivingdetailkey = ' . $this->tableItemReceivingDetail . '.pkey
                    left join ' . $this->tableWarehouseLayout . ' on ' . $this->tableNameDetail . '.warehouselayoutkey = ' . $this->tableWarehouseLayout . '.pkey
                     left join ' . $this->tableItemUnit . ' on ' . $this->tableItemReceivingDetail . '.unit = ' . $this->tableItemUnit . '.pkey,
                ' . $this->tableItem . '
			  where
                ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
			  	' . $this->tableNameDetail . '.refkey = ' . $this->oDbCon->paramString($pkey);

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);

    }

    function closeTrans($rsHeader)
    {

        $itemReceiving = new ItemReceiving();
        $warehouseLayout = new WarehouseLayout();
        $pkey = $rsHeader[0]['pkey'];


        $id = $rsHeader[0]['pkey'];

        $rsItemReceiving = $itemReceiving->getDataRowById($rsHeader[0]['refkey']);
        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $itemMovement = new ItemMovement();
        $note = $rsHeader[0]['code'] . '. ' . $this->ucFirst($this->lang['putAway']);
        // for ($i = 0; $i < count($rsDetail); $i++) {
        //     if ($rsDetail[$i]['qty'] != 0)
        //         $itemMovement->updateItemMovement($id, $rsDetail[$i]['itemkey'], -$rsDetail[$i]['qty'], 0, $this->tableName, array('warehouselayoutkey' => $rsHeader[0]['warehouselayoutoriginkey'], 'warehousekey' => $rsHeader[0]['warehousekey']), $note, $rsHeader[0]['trdate']);
        //     $itemMovement->updateItemMovement($id, $rsDetail[$i]['itemkey'], $rsDetail[$i]['qty'], 0, $this->tableName, array('warehouselayoutkey' => $rsHeader[0]['warehouselayoutkey'], 'warehousekey' => $rsHeader[0]['warehousekey']), $note, $rsHeader[0]['trdate']);
        // }

        if ($this->typekey == 1) {

            $rsWarehouseLayoutFrom = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutoriginkey']);
            $rsWarehouseLayoutTo = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutkey']);
            $note = $rsHeader[0]['code'] . '. ' . $this->ucFirst($this->lang['putAway'] . ' ' . $this->lang['from']) . ' ' . $this->lang['itemReceiving'] . ' : ' . $rsItemReceiving[0]['code'] . '. Dari Zona ' . $rsWarehouseLayoutFrom[0]['name'] . ' ke ' . $rsWarehouseLayoutTo[0]['name'];

            for ($i = 0; $i < count($rsDetail); $i++) {
                if ($rsDetail[$i]['qty'] != 0) {
                    $itemMovement->updateItemMovement(
                        array(
                            'refkey' => $id,
                            'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
                            'refdetailkey' => $rsDetail[$i]['itemreceivingdetailkey'],
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
                    $itemMovement->updateItemMovement(
                        array(
                            'refkey' => $id,
                            'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
                            'refdetailkey' => $rsDetail[$i]['itemreceivingdetailkey'],
                        ),
                        $rsDetail[$i]['itemkey'],
                        $rsDetail[$i]['qty'],
                        0,
                        $this->tableName,
                        array(
                            'warehousekey' => $rsHeader[0]['warehousekey'],
                            'warehouselayoutkey' => $rsHeader[0]['warehouselayoutkey']
                        ),
                        $note,
                        $rsHeader[0]['trdate']
                    );
                }
            }

        } else {

            $rsWarehouseLayoutFrom = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutoriginkey']);
            $rsWarehouseLayoutTo = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutkey']);
            $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

            $note = $rsHeader[0]['code'] . '. Perpindahan Zona dari ' . $rsWarehouseLayoutFrom[0]['name'] . ' ke ' . $rsWarehouseLayoutTo[0]['name'];

            for ($i = 0; $i < count($rsDetail); $i++) {
                $itemMovement->updateItemMovement(array(
                    'refkey' => $id,
                    'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
                    'refdetailkey' => $rsDetail[$i]['itemreceivingdetailkey'],
                ), $rsDetail[$i]['itemkey'], -$rsDetail[$i]['qty'], 0, $this->tableName, array(
                    'warehousekey' => $rsHeader[0]['warehousekey'],
                    'warehouselayoutkey' => $rsDetail[$i]['warehouselayoutkey']
                ), $note, $rsHeader[0]['trdate']);
                $itemMovement->updateItemMovement(array(
                    'refkey' => $id,
                    'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
                    'refdetailkey' => $rsDetail[$i]['itemreceivingdetailkey'],
                ), $rsDetail[$i]['itemkey'], $rsDetail[$i]['qty'], 0, $this->tableName, array(
                    'warehousekey' => $rsHeader[0]['warehousekey'],
                    'warehouselayoutkey' => $rsHeader[0]['warehouselayoutkey']
                ), $note, $rsHeader[0]['trdate']);
            }

        }


    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);


        $itemReceiving = new ItemReceiving();
        $this->setLog($arr, true);

        $warehousekey = $arr['hidWarehouseKey'];
        $warehouselayoutkey = $arr['hidWarehouseLayoutKey'];
        $palletkey = $arr['hidPalletKey'];
        $refkey = $arr['hidRefKey'];

        $arrItemKey = $arr['hidItemKey'];
        $arrReceivingQty = $arr['receivingQty'];
        $arrPutAwayQty = $arr['qty'];
        $arrItemName = $arr['itemName'];

        if (empty($warehousekey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['warehouse'][1]);
        }

        // if(empty($warehouselayoutkey)) {
        //     $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouseLayout'][1]); 
        // }

        // if(empty($palletkey)) {
        //     $this->addErrorList($arrayToJs,false,$this->errorMsg['pallet'][1]); 
        // }

        // if(empty($refkey)) {
        //     $this->addErrorList($arrayToJs,false,$this->errorMsg['putAway'][1]); 
        // }

        if ($this->typekey == 2) {

            $warehouseOriginKey = $arr['hidWarehouseLayoutOriginKey'];
            $warehouseDestinationKey = $arr['hidWarehouseLayoutKey'];
            $refkey = $arr['hidRefKey'];

            // if (empty($warehouseOriginKey) || empty($warehouseDestinationKey)) {
            //     $this->addErrorList($arrayToJs, false, $this->errorMsg['zoneTransfer'][1]);
            // }

            if ($warehouseOriginKey == $warehouseDestinationKey) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['zoneTransfer'][2]);
            }

            if (empty($refkey)) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['zoneTransfer'][3]);
            } else {
                $rsReceiving = $itemReceiving->getDataRowById($refkey);
                if (empty($rsReceiving)) {
                    $this->addErrorList($arrayToJs, false, $this->errorMsg['zoneTransfer'][4]);
                }
            }

        }

        if (empty($arrItemKey[0])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
        } else {
            for ($i = 0; $i < count($arrItemKey); $i++) {

                $itemName = $arr['itemName'][$i];
                if (empty($arrItemKey[$i])) {
                    $this->addErrorList($arrayToJs, false, $this->errorMsg['item'][1]);
                }

                if ($this->typekey == 1) {
                    $putAwayQty = $this->unFormatNumber($arrPutAwayQty[$i]);
                    $receivingQty = $this->unFormatNumber($arrReceivingQty[$i]);

                    if ($receivingQty <= 0) {
                        $this->addErrorList($arrayToJs, false, '<strong>' . $itemName . '.</strong> ' . $this->errorMsg['putAway'][2]);
                    }

                    if ($putAwayQty <= 0) {
                        $this->addErrorList($arrayToJs, false, '<strong>' . $itemName . '.</strong> ' . $this->errorMsg[510]);
                    } else {

                        if ($putAwayQty > $receivingQty) {
                            $this->addErrorList($arrayToJs, false, '<strong>' . $itemName . '.</strong> ' . $this->errorMsg['putAway'][3]);
                        }

                    }

                } else if ($this->typekey == 2) {

                    $arrQty = $arr['qty'];
                    $qty = $this->unFormatNumber($arrQty[$i]);

                    if ($qty <= 0) {
                        $this->addErrorList($arrayToJs, false, '<strong>' . $itemName . '.</strong> ' . $this->errorMsg[510]);
                    }

                } else if ($this->typekey == 3) {

                }

            }
        }

        return $arrayToJs;
    }

    function validateConfirm($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        $itemMovement = new itemMovement();
        $itemReceiving = new ItemReceiving();
        $warehouseLayout = new WarehouseLayout();


        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $this->setLog($rsDetail, true);

        if ($this->typekey == 1) {

            if (empty($rsDetail)) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[501]);
            } else {
                for ($i = 0; $i < count($rsDetail); $i++) {
                    if ($rsDetail[$i]['receivingqty'] <= 0) {
                        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $rsDetail[$i]['itemname'] . '. ' . $this->errorMsg['putAway'][2]);
                    }
                    if ($rsDetail[$i]['qty'] <= 0) {
                        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $rsDetail[$i]['itemname'] . '. ' . $this->errorMsg['putAway'][2]);
                    } else {
                        if ($rsDetail[$i]['qty'] > $rsDetail[$i]['receivingqty']) {
                            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $rsDetail[$i]['itemname'] . '. ' . $this->errorMsg['putAway'][3]);
                        }
                        $outstadingPutAwayQty = $itemReceiving->getTotalUnPutAwayItemReceiving($rsDetail[$i]['itemreceivingdetailkey']);
                        //apakah qty lebih besar dari outstanding put away
                        $this->setLog($outstadingPutAwayQty, true);
                        if ($rsDetail[$i]['qty'] > $outstadingPutAwayQty) {
                            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $rsDetail[$i]['itemname'] . '. ' . $this->errorMsg['putAway'][4]);
                        }

                    }
                }
            }

        } else if ($this->typekey == 2) {

            $rsReceiving = $itemReceiving->getDataRowById($rsHeader[0]['refkey']);

            if (empty($rsReceiving)) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br>' . $this->errorMsg['zoneTransfer'][4]);
            }


            $rsDetail = $this->getDetailWithRelatedInformation($id);

            $arrErrMsg = array();

            for ($i = 0; $i < count($rsDetail); $i++) {


                //cek stok di zona
                $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], $rsHeader[0]['warehousekey'], $rsHeader[0]['warehouselayoutoriginkey']);
                $totalqty = $saldoakhir - $rsDetail[$i]['qty'];
                if ($totalqty < 0) {
                    array_push($arrErrMsg, '<strong>. ' . $rsDetail[$i]['itemcode'] . ' - ' . $rsDetail[$i]['itemname'] . '</strong>, ' . $this->errorMsg[402]);
                }

                //cek apakah posisiwarehouse terakhir sama dengan asal mutasi?
                $sql = '
                    select
                        ' . $itemMovement->tableName . '.pkey,
                        ' . $itemMovement->tableName . '.refkey,
                        ' . $itemMovement->tableName . '.refkey2,
                        ' . $itemMovement->tableName . '.itemkey,
                        ' . $itemMovement->tableName . '.warehousekey,
                        ' . $itemMovement->tableName . '.warehouselayoutkey
                    from
                        ' . $itemMovement->tableName . '
                    where
                     ' . $itemMovement->tableName . '.statuskey = 1 and
                      ' . $itemMovement->tableName . '.reftable = ' . $this->oDbCon->paramString($this->tableName) . ' and 
                      ' . $itemMovement->tableName . '.warehousekey = ' . $this->oDbCon->paramString($rsHeader[0]['warehousekey']) . ' and 
                      ' . $itemMovement->tableName . '.itemkey = ' . $this->oDbCon->paramString($rsDetail[$i]['itemkey']) . ' order by  ' . $itemMovement->tableName . '.pkey desc limit 1 
                ';

                $rs = $this->oDbCon->doQuery($sql);

                if (!empty($rs)) {
                    $rsData = $this->getDataRowById($rs[0]['refkey']);
                    if (($rsHeader[0]['warehouselayoutoriginkey'] != $rs[0]['warehouselayoutkey']) && $rsData[0]['typekey'] == 2) {
                        $rsLayout = $warehouseLayout->getDataRowById($rs[0]['warehouselayoutkey']);
                        array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['itemcode'] . ' - ' . $rsDetail[$i]['itemname'] . '</strong>. ' . $this->errorMsg['putAway'][5] . ' ke <strong>' . $rsLayout[0]['name'] . '</strong>');
                    }
                }

            }


            if (!empty($arrErrMsg)) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br>' . implode(',<br>', $arrErrMsg));
            }

        } else if ($this->typekey == 3) {



        }



    }

    function confirmTrans($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $itemMovement = new ItemMovement();
        $warehouseLayout = new WarehouseLayout();
        $itemReceiving = new ItemReceiving();

        $rsItemReceiving = $itemReceiving->getDataRowById($rsHeader[0]['refkey']);

        // if ($this->typekey == 1) {

        //     $rsWarehouseLayoutFrom = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutoriginkey']);
        //     $rsWarehouseLayoutTo = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutkey']);
        //     $note = $rsHeader[0]['code'] . '. ' . $this->ucFirst($this->lang['putAway'] . ' ' . $this->lang['from']) . ' ' . $this->lang['itemReceiving'] . ' : ' . $rsItemReceiving[0]['code'] . '. Dari Zona ' . $rsWarehouseLayoutFrom[0]['name'] . ' ke ' . $rsWarehouseLayoutTo[0]['name'];

        //     for ($i = 0; $i < count($rsDetail); $i++) {
        //         if ($rsDetail[$i]['qty'] != 0) {
        //             $itemMovement->updateItemMovement(
        //                 array(
        //                     'refkey' => $id,
        //                     'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
        //                 ),
        //                 $rsDetail[$i]['itemkey'],
        //                 -$rsDetail[$i]['qty'],
        //                 0,
        //                 $this->tableName,
        //                 array(
        //                     'warehousekey' => $rsHeader[0]['warehousekey'],
        //                     'warehouselayoutkey' => $rsHeader[0]['warehouselayoutoriginkey']
        //                 ),
        //                 $note,
        //                 $rsHeader[0]['trdate']
        //             );
        //             $itemMovement->updateItemMovement(
        //                 array(
        //                     'refkey' => $id,
        //                     'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
        //                 ),
        //                 $rsDetail[$i]['itemkey'],
        //                 $rsDetail[$i]['qty'],
        //                 0,
        //                 $this->tableName,
        //                 array(
        //                     'warehousekey' => $rsHeader[0]['warehousekey'],
        //                     'warehouselayoutkey' => $rsHeader[0]['warehouselayoutkey']
        //                 ),
        //                 $note,
        //                 $rsHeader[0]['trdate']
        //             );
        //         }
        //     }

        // } else if ($this->typekey == 2) {

        //     $rsWarehouseLayoutFrom = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutoriginkey']);
        //     $rsWarehouseLayoutTo = $warehouseLayout->getDataRowById($rsHeader[0]['warehouselayoutkey']);
        //     $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

        //     $note = $rsHeader[0]['code'] . '. Perpindahan Zona dari ' . $rsWarehouseLayoutFrom[0]['name'] . ' ke ' . $rsWarehouseLayoutTo[0]['name'];

        //     for ($i = 0; $i < count($rsDetail); $i++) {
        //         $itemMovement->updateItemMovement(array(
        //             'refkey' => $id,
        //             'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
        //         ), $rsDetail[$i]['itemkey'], -$rsDetail[$i]['qty'], 0, $this->tableName, array(
        //             'warehousekey' => $rsHeader[0]['warehousekey'],
        //             'warehouselayoutkey' => $rsHeader[0]['warehouselayoutoriginkey']
        //         ), $note, $rsHeader[0]['trdate']);
        //         $itemMovement->updateItemMovement(array(
        //             'refkey' => $id,
        //             'refkey2' => $rsDetail[$i]['itemreceivingheaderkey'],
        //         ), $rsDetail[$i]['itemkey'], $rsDetail[$i]['qty'], 0, $this->tableName, array(
        //             'warehousekey' => $rsHeader[0]['warehousekey'],
        //             'warehouselayoutkey' => $rsHeader[0]['warehouselayoutkey']
        //         ), $note, $rsHeader[0]['trdate']);
        //     }

        // } else if ($this->typekey == 3) {

        // }

    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $id = $rsHeader[0]['pkey'];

        $itemMovement = new ItemMovement();
        $warehouseLayout = new WarehouseLayout();

        $rsDetail = $this->getDetailWithRelatedInformation($id);

        $arrErrMsg = array();

        for ($i = 0; $i < count($rsDetail); $i++) {

            $sql = '
                    select
                        ' . $itemMovement->tableName . '.pkey,
                        ' . $itemMovement->tableName . '.refkey,
                        ' . $itemMovement->tableName . '.refkey2,
                        ' . $itemMovement->tableName . '.itemkey,
                        ' . $itemMovement->tableName . '.warehousekey,
                        ' . $itemMovement->tableName . '.warehouselayoutkey
                    from
                        ' . $itemMovement->tableName . '
                    where
                     ' . $itemMovement->tableName . '.statuskey = 1 and
                      ' . $itemMovement->tableName . '.reftable = ' . $this->oDbCon->paramString($this->tableName) . ' and 
                      ' . $itemMovement->tableName . '.warehousekey = ' . $this->oDbCon->paramString($rsHeader[0]['warehousekey']) . ' and 
                      ' . $itemMovement->tableName . '.itemkey = ' . $this->oDbCon->paramString($rsDetail[$i]['itemkey']) . ' order by  ' . $itemMovement->tableName . '.pkey desc limit 1 
                ';

            $rs = $this->oDbCon->doQuery($sql);

            // if (!empty($rs)) {
            //     if ($rsHeader[0]['warehouselayoutkey'] != $rs[0]['warehouselayoutkey']) {
            //         $rsLayout = $warehouseLayout->getDataRowById($rs[0]['warehouselayoutkey']);
            //         array_push($arrErrMsg, '<strong>' . $rsDetail[$i]['itemcode'] . ' - ' . $rsDetail[$i]['itemname'] . '</strong>. ' . $this->errorMsg['putAway'][5] . ' ke <strong>' . $rsLayout[0]['name'] . '</strong>');
            //     }
            // }

        }

        if (!empty($arrErrMsg)) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br>' . implode(',<br>', $arrErrMsg));
        }
    }



    function cancelTrans($rsHeader, $copy)
    {
        $id = $rsHeader[0]['pkey'];

        $itemMovement = new ItemMovement();
        $itemMovement->cancelMovement($id, $this->tableName);

        if ($copy)
            $this->copyDataOnCancel($id);


    }

    function backConfirmTrans($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        $itemMovement = new ItemMovement();
        $itemMovement->cancelMovement($id, $this->tableName);

    }

    function afterStatusChanged($rsHeader)
    {
        $itemReceiving = new ItemReceiving();

        if ($this->typekey == 1) {
            $itemReceiving->updateQtyPutAway($rsHeader[0]['refkey']);
        } else if ($this->typekey == 2) {

        } else if ($this->typekey == 3) {

        }
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        if ($this->typekey == 2) {
            $arrParam['submissionNumber'] = $arrParam['refCode'];
        }
        $this->setLog($arrParam, true);

        $arrParam = parent::normalizeParameter($arrParam);

        return $arrParam;

    }



}
?>