<?php 

class ActivityProgress extends BaseClass
{
    function __construct()
    {
        parent::__construct();

        $this->tableName = 'activity_progress_header';
        $this->tableNameDetail = 'activity_progress_detail';
        $this->tableActivityTemplate = 'template_activity';
        $this->tableJobOrder = 'emkl_job_order_header';
        $this->tableStatus = 'transaction_status';

        $this->isTransaction = true;
        $this->newLoad = true;
        $this->securityObject = 'ActivityProgress';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['date'] = array('detailDate', 'date');
        $this->arrDataDetail['activitykey'] = array('hidActivityKey');
        $this->arrDataDetail['response'] = array('response');
        $this->arrDataDetail['trdesc'] = array('detailNote');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['joborderkey'] = array('hidJobOrderKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 120, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobOrder', 'title' => 'jobOrder', 'dbfield' => 'jobordercode', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Kode Job Order', $this->tableJobOrder . '.code'));

        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/activityProgress')); 

        $this->includeClassDependencies(array(
            'EMKLJobOrder.class.php',
            'TemplateActivity.class.php'
        ));

        $this->overwriteConfig();
    }   

    function getQuery() 
    {
        $sql = '
            select
                '. $this->tableName .'.*,
                '. $this->tableStatus .'.status as statusname,
                '. $this->tableJobOrder .'.code as jobordercode
            from
                '. $this->tableName .'
                    left join '. $this->tableJobOrder .' on '. $this->tableName .'.joborderkey = '. $this->tableJobOrder .'.pkey,
                    '. $this->tableStatus .'
            where
                '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey
        ' . $this->criteria;

        return $sql;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   		        '.$this->tableNameDetail .'.*,
                    '. $this->tableActivityTemplate .'.code as activitycode,
                    '. $this->tableActivityTemplate .'.name as activityname,
                    CONCAT('. $this->tableActivityTemplate .'.code, \' - \', ' . $this->tableActivityTemplate . '.name) as activitycodename
                from
                    '. $this->tableNameDetail .'
                        left join '. $this->tableActivityTemplate .' on '. $this->tableNameDetail .'.activitykey = '. $this->tableActivityTemplate .'.pkey
                where
			  	    '. $this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') 
                ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function validateForm($arr, $pkey = '') 
    {
        $arrayToJs = parent::validateForm($arr, $pkey);

        $templateActivity = new TemplateActivity();
        $emklJobOrder = new EMKLJobOrder();

        $jokey = $arr['hidJobOrderKey'];
        $detailkey = $arr['hidDetailKey'];
        $arrActivityKey = $arr['hidActivityKey'];

        if(empty($jokey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['jobOrder'][1]);
        }
    
        if(empty($arrActivityKey[0])) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
        } else {

            $arrDetailKey = array();
            $arrErrMsg = array();
            $arrErrDuplicate = array();
            
            for($i=0; $i < count($arrActivityKey); $i++) {
                if (empty($arrActivityKey[$i])) {
                    array_push( $arrErrMsg, $this->errorMsg['activityProgress'][4]);
                }

                if (in_array($arrActivityKey[$i],$arrDetailKey)){   
                    $rsTemplateActivity = $templateActivity->getDataRowById($arrActivityKey[$i]);
                    array_push($arrErrDuplicate, $rsTemplateActivity[0]['name']);
                }else{ 
                    array_push($arrDetailKey, $arrActivityKey[$i]);
                }
                
            }
            if (!empty($arrErrMsg)) {
                $this->addErrorList($arrayToJs, false, '<b>'.$this->errorMsg[501].'</b><br> '. implode('<br>', $arrErrMsg) .' ');
            }

            if (!empty($arrErrDuplicate)) {
                $this->addErrorList($arrayToJs, false, '<strong>'. $this->errorMsg[215] .'</strong><br>' . implode('<br>', $arrErrDuplicate));
            }	 
        }


        return $arrayToJs;
    }

    function validateConfirm($rsHeader) 
    {
        $emklJobOrder = new EMKLJobOrder();
    

        $id = $rsHeader[0]['pkey'];

        $rsData = $this->getDataRowById($id);
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        
        $jokey = $rsData[0]['joborderkey'];
        if(empty($jokey)) {
            $this->addErrorLog(false, '<b>' . $rsData[0]['code'] .'</b>. ' . $this->errorMsg['jobOrder'][1]);
        }

        $arrDetailKey = array();
        $arrErrMsg = array();
        $arrErrDuplicate = array();
        $errMsgResponse = array();
        for($i=0; $i<count($rsDetail); $i++) {
            if (empty($rsDetail[$i]['activitykey'])) {
                array_push($arrErrMsg, '<strong>'. $rsData[0]['code'] .'. </strong>' . $this->errorMsg['activityProgress'][1]);
            }

            if (in_array($rsDetail[$i]['activitykey'], $arrDetailKey)) {
                array_push($arrErrDuplicate, $rsDetail[$i]['activityname']);
            } else {
                array_push($arrDetailKey, $rsDetail[$i]['activitykey']);
            }

            //cek respon tidak boleh kosong
            if (empty($rsDetail[$i]['response'])) {
                array_push($errMsgResponse, '<b>'. $rsDetail[$i]['activityname'] .'. </b>' . $this->errorMsg['activityProgress'][3]);
            }
        }

        if (!empty($arrErrMsg)) {
            $this->addErrorLog(false, '<b>'.$this->errorMsg[501].'</b><br> '. implode('<br>', $arrErrMsg) .' ');
        }

        if (!empty($arrErrDuplicate)) {
            $this->addErrorLog(false, '<strong>'. $this->errorMsg[215] .'</strong><br>' . implode('<br>', $arrErrDuplicate));
        }	

        if(!empty($errMsgResponse)) {
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] .'. </strong>'. $this->errorMsg[201] .'<br>' . implode('<br>', $errMsgResponse));
        }


        //cek ada JO yang sama tidak
        $rsActivity = $this->searchDataRow(array(
                    $this->tableName.'.pkey',
                    $this->tableName.'.code',
                    $this->tableName.'.joborderkey',
                    $this->tableName.'.statuskey'
                ),' and ' . $this->tableName.'.joborderkey = ('. $this->oDbCon->paramString($jokey) .') and '. $this->tableName.'.statuskey in (2,3) ');

        if(!empty($rsActivity)) {
            $rsJO = $emklJobOrder->getDataRowById($rsActivity[0]['joborderkey']);
            $this->addErrorLog(false, '<strong>'. $this->errorMsg[215] .'</strong> <br> <strong>'. $rsJO[0]['code'] .'. </strong>'. $this->errorMsg['activityProgress'][1]);
        }

        //cek JO valid tidak
        $rsJO = $emklJobOrder->searchDataRow(array(
                    $emklJobOrder->tableName.'.pkey',
                    $emklJobOrder->tableName.'.code',
                    $emklJobOrder->tableName.'.statuskey'   
                ), ' and ' . $emklJobOrder->tableName.'.pkey = ('. $this->oDbCon->paramString($rsHeader[0]['joborderkey']) .') and '. $emklJobOrder->tableName .'.statuskey in (1,2,3)');

        if(empty($rsJO)) {
            $this->addErrorLog(false, '<b>'. $rsHeader[0]['code'] .' </b>. ' . $this->errorMsg['jobOrder'][2] );
        }
        

    }

    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];
    }

    function cancelTrans($rsHeader, $copy)
    {
        $id = $rsHeader[0]['pkey'];

        if ($copy) {
            $this->copyDataOnCancel($id);
        }
    } 

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
    
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam = parent::normalizeParameter($arrParam);


        return $arrParam;
    }

    function getActivityProgressByJobOrder($pkey)
    {
        $sql = '
            select
                '. $this->tableNameDetail .'.*,
                '. $this->tableActivityTemplate .'.code as activitytemplatecode,
                '. $this->tableActivityTemplate .'.name as activityname,
                '. $this->tableActivityTemplate .'.typekey as activitytypekey,
                '. $this->tableName .'.code,
                '. $this->tableName .'.trdate as headerdate,
                '. $this->tableName .'.joborderkey,
                '. $this->tableJobOrder .'.code as jobordercode
            from
                '. $this->tableNameDetail .'
                left join '. $this->tableActivityTemplate .' on '. $this->tableNameDetail .'.activitykey = '. $this->tableActivityTemplate .'.pkey,
                '. $this->tableName .',
                '. $this->tableJobOrder .'
            where
                '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey and 
                '. $this->tableName .'.joborderkey = '. $this->tableJobOrder .'.pkey and
                '. $this->tableName .'.joborderkey in ('. $this->oDbCon->paramString($pkey, '') .') 
                order by '. $this->tableNameDetail .'.date desc limit 10
        ';

        $result = $this->oDbCon->doQuery($sql);
        
        return $result;
    }

}

?>