<?php

require_once '../../_config.php';  
require_once '_include.php';
require_once '_global.php'; 
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php';  
/*require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';*/
 
function getNewObj(){ 
   return new TruckingServiceOrderInvoice(); 
}

$OBJ = getNewObj();
 
$API_FIELDS = array_merge($API_FIELDS,array(
                'code' =>   array('paramName' => 'code'),   
                'vanumber' =>  array('paramName' => 'va_number')
            ));  
 

/* RECEIVE VALUE */

$hasSuccessValue = false;
$arrFailed = array();
$ARR_RETURN_VALUE = array();

$code = array_column($arrPostVars,'code');

$rsCol = array();
if(!empty(($code))) {
 $rsCol = $OBJ->searchDataRow(array($OBJ->tableName.'.pkey',$OBJ->tableName.'.code',$OBJ->tableName.'.modifiedon'),
                            ' and ' .$OBJ->tableName.'.code in ('.$OBJ->oDbCon->paramString($code,',').')'
                          );   
 $rsCol = array_column($rsCol,null,'code');
}

for($i=0;$i<$totalPostVars;$i++){ 
    $OBJ = getNewObj(); // karena field yg gk ad di unset di normalize, jd harus create ulang objextnya
        
    $PARAM = array();    
    $errorMessage = array(); 
    $postVars = $arrPostVars[$i];
    getDetailsAPIParam($ACTION, $PARAM,$postVars, $API_FIELDS, array(), $errorMessage);
    
    if(!empty($errorMessage)){   
        addFailedRows($arrFailed, array(
                        'code' => $PARAM['code'],
                        'message' => $errorMessage, // error disini,
                )); 
    }else{ 
        $rs = (isset($rsCol[$PARAM['code']])) ? $rsCol[$PARAM['code']] : array();  
        if (empty($rs)){
             $result = array();
             $result[0] = array( 
                'valid' => false,
                'message' => $OBJ->errorMsg[213],
             );        
        } else{ 
            $OBJ->setLog($PARAM,true);
            $vaNumber = $PARAM['vanumber']; 
            $result = $OBJ->updateVANumber($rs['pkey'],$vaNumber);   
        } 
                
        $response = getResponseValue($result);   

        if(empty($response['errorMessage'])){ 
            array_push($ARR_RETURN_VALUE,
                     array(
                            //'response_code' => 200,
                            'code' => $PARAM['code'],
                            'message' => $response['successMessage'],
                        )
                    ); 

            $hasSuccessValue = true;

        }else{  
            addFailedRows($arrFailed, array(
                    'code' => $PARAM['code'],
                    'message' => $response['errorMessage'], 
            )); 
        }
    }
}
 
$RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
$RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
$RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
$RETURN_VALUE['failed_rows'] = count($arrFailed);
$RETURN_VALUE['failed_data'] = $arrFailed;

 
http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>