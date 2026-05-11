 <?php

    require_once '../../_config.php';
    require_once '_include.php';

    
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php'; 

    function getNewObj(){ 
        return new Employee();
    }

    $obj = getNewObj();

    require_once '_global.php';


    if (!in_array($ACTION, array('GET')))
        endForRequestMethodError();

    $RETURN_VALUE = array();

    switch ($ACTION)    {

        case 'GET':

            
            $hasSuccessValue = false;
            $arrFailed = array();
            $ARR_RETURN_VALUE = array();

            $result = [];

            $obj = getNewObj();
            
            if (!empty($errorMessage)) {
                    addFailedRows($arrFailed, array(
                        'code' => $_GET['code'],
                        'message' => $errorMessage, // error disini,
                ));
            } else {

                $code = $_GET['code'] ?? null;

                try {
                    if (!$obj->oDbCon->startTrans(true))
                        throw new Exception($obj->errorMsg[100]);

                    if (empty($code)) {
                        throw new Exception($obj->errorMsg['code'][1]);
                    }


                    $code = $_GET['code'];

                    $rsEmployee = $obj->searchData('','',true, ' and ' . $obj->tableName.'.code = '.$obj->oDbCon->paramString($code));
                    if (empty($rsEmployee)) {
                        throw new Exception('Employee not found.');
                    }
                    
                    $rsWarehouseAccess = $obj->getWarehouseAccessForAPI($rsEmployee[0]['pkey']);
                    $rsSalesAccess = $obj->getSalesAccessForAPI($rsEmployee[0]['pkey']);
                    $rsPaymentMethodAccess = $obj->getPaymentMethodAccessForAPI($rsEmployee[0]['pkey']);
                    $rsCOAAccess = $obj->getCOAAccessForAPI($rsEmployee[0]['pkey']);
                    $rsUserPrivileges = $obj->getUserPrivilegesForAPI($rsEmployee[0]['pkey']);

                    $result = [
                        'key' => $rsEmployee[0]['pkey'],
                        'code' => $rsEmployee[0]['code'],
                        'name' => $rsEmployee[0]['name'],
                        'warehouse_privilege' => $rsWarehouseAccess,
                        'salesman_privilege' => $rsSalesAccess,
                        'payment_method_privilege' => $rsPaymentMethodAccess,
                        'chart_of_account_privilege' => $rsCOAAccess,
                        'security_privilege' => $rsUserPrivileges
                    ];


                    array_push(
                        $ARR_RETURN_VALUE,
                        array(
                            'code' => $_GET['code'],
                            'message' => 'success',
                            'data' => $result
                        )
                    );

                    $hasSuccessValue = true;

                $obj->oDbCon->endTrans();

                } catch (Exception $e) {
                    $obj->oDbCon->rollback();
                    addFailedRows($arrFailed, [
                        'code' => $code ?? null,
                        'message' => $e->getMessage(),
                    ]);
                     $hasSuccessValue = false;
                }

            }


    
            $RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
            $RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
            $RETURN_VALUE['failed_data'] = $arrFailed;


            break;
    }


    http_response_code($RETURN_VALUE['response_code']);
    echo json_encode($RETURN_VALUE);

    die;

?>