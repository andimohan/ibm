    <?php
    require_once '../../_config.php';
    require_once '_include.php';

    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';      
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderCategory.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Car.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/JobProgress.class.php';

    function getNewObj(){ 
        return new TruckingServiceWorkOrder(); 
    }

    $OBJ = getNewObj();

    require_once '_global.php';

    if (!in_array($ACTION, array('PUT')))
        endForRequestMethodError();

    $RETURN_VALUE = array();

    switch ($ACTION) {
        case 'PUT':

            $hasSuccessValue = false;
            $arrFailed = array();
            $ARR_RETURN_VALUE = array();
            
            for ($i = 0; $i < $totalPostVars; $i++) {
                $OBJ = getNewObj();

                $PARAM = array();
                $errorMessage = array();
                $postVars = $arrPostVars[$i];


                if (!empty($errorMessage)) {
                    addFailedRows($arrFailed, array(
                        'code' => $postVars['work_order_code'],
                        'message' => $errorMessage, // error disini,
                    ));
                } else {

                    try {

                        if (!$OBJ->oDbCon->startTrans(true))
                            throw new Exception($OBJ->errorMsg[100]);


                        $PARAM['woKey'] = $postVars['wo_key'];
                        //$PARAM['woCode'] = $postVars['work_order_code'];
                        $PARAM['jobProgressDetailKey'] = $postVars['work_order_progress_key'] ?? 0; //key work order driver progress
                        $PARAM['number'] = $postVars['number'] ?? 0; //driver progress key
                        $PARAM['jobProgressKey'] = $postVars['job_progress_key'] ?? 0; //driver progress key
                        $PARAM['jobProgressHeaderKey'] = $postVars['job_progress_header_key'] ?? 0; //driver progress header key
                        $PARAM['completedDate'] = date('d / m / Y H:i'); //completed date, selalu dari server, agar tdk dicurangi $postVars['completed_date'] ?? 
                        $PARAM['isCompleted'] = $postVars['is_completed'] ?? 0;
                        $PARAM['latitude'] = $postVars['latitude'] ?? 0;
                        $PARAM['longitude'] = $postVars['longitude'] ?? 0;
                        $PARAM['fileName'] = $postVars['file_name'] ?? '';
 
                        
                        $result = $OBJ->updateJobProgressWorkOrder($PARAM);


                        if (!$result[0]['valid'])
                            throw new Exception($result[0]['message']);

                        $OBJ->oDbCon->endTrans();

                    } catch (Exception $e) {
                        $OBJ->oDbCon->rollback();
                    }

                    $response = getResponseValue($result);

                    if (empty($response['errorMessage'])) { // isset buat handle kalo ad sql / php error

                        array_push(
                            $ARR_RETURN_VALUE,
                            array(
                                //'response_code' => 200,
                                'code' => $postVars['work_order_code'],
                                'message' => $response['successMessage'],
                            )
                        );

                        $hasSuccessValue = true;

                    } else {

                        addFailedRows($arrFailed, array(
                            'code' => $postVars['work_order_code'],
                            'message' => $response['errorMessage'],
                        ));

                    }

                }

            }
                        

            $RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
            //$RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
            $RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
            //$RETURN_VALUE['failed_rows'] = count($arrFailed);
            $RETURN_VALUE['failed_data'] = $arrFailed;

        break;
    }


    http_response_code($RETURN_VALUE['response_code']);
    echo json_encode($RETURN_VALUE);

    die;

    ?>