<?php
die('deprecated');

require_once '../../_config.php';  
require_once '_include.php';
require_once '_global.php'; 
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';       
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php'; 
 
function getNewObj($gatePassResponse = ''){ 
    $obj = new TruckingServiceOrder();
    
    if(isset($gatePassResponse['sessionId'])){ 
       $obj->loginAdminSession  = $gatePassResponse['sessionId'];
       $obj->userkey  = $gatePassResponse['userkey'];
    }
    
    return $obj;
                    
}

$OBJ = getNewObj($gatePassResponse);

$truckingCost =  new Service(TRUCKING_SERVICE,1);   

$headerCost = array( 
    'pkey' => array('paramName' => 'pkey'), 
    'costkey' =>  array('paramName' => 'cost_id', 'mandatory' => true, 'ref' => array('obj' => $truckingCost, 'field' => 'code' ), 'return' => array('paramName' => 'itemcode')), 
    'costname' =>  array('paramName' => 'cost_name','updatable' => false, 'return' => array('paramName' => 'itemname')),
    'qty'  =>  array('paramName' => 'qty','mandatory' => true ), 
    'requestamount'  =>  array('paramName' => 'request_amount','mandatory' => true ) 
);
  
$API_FIELDS = array_merge($API_FIELDS,array(
                'code' =>   array('paramName' => 'code'),   
                'additional_cost_detail' =>  array('paramName' => 'additional_cost',  'dataset' => $OBJ->arrHeaderCost, 'detail' =>  $headerCost)
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
    $OBJ = getNewObj($gatePassResponse); // karena field yg gk ad di unset di normalize, jd harus create ulang objextnya
        
    $PARAM = array();    
    $errorMessage = array(); 
    $postVars = $arrPostVars[$i];
    getDetailsAPIParam($PARAM,$postVars, $API_FIELDS, array(), $errorMessage);
    
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
            $arrCost = array(); 
            $totalCost = count($PARAM['hidItemKeyHeaderCost']);
            for($j=0;$j<$totalCost;$j++){
                if(empty($PARAM['hidItemKeyHeaderCost'][$j]) || $PARAM['qtyHeaderCost'][$j] <= 0 || $PARAM['requestPriceHeaderCost'][$j] <= 0) continue;
                array_push($arrCost,
                      array(
                        'costkey' => $PARAM['hidItemKeyHeaderCost'][$j],
                        'qty' => $PARAM['qtyHeaderCost'][$j],
                        'requestamount' => $PARAM['requestPriceHeaderCost'][$j],
                      )
                );
            }
             
            $result = $OBJ->addHeaderCost($rs['pkey'],$arrCost);  
            
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