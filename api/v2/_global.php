<?php

function endForInvalidLoginError($errMsg = ''){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $errMsg;
    http_response_code($RETURN_VALUE['response_code'] ); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function endForInvalidTokenError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $class->errorMsg[601];
    http_response_code($RETURN_VALUE['response_code'] ); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function endForDataNotFoundError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code'] = 400;
    $RETURN_VALUE['message'] = $class->errorMsg[213];
    http_response_code($RETURN_VALUE['response_code'] ); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function endForRequestMethodError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code'] = 400;
    $RETURN_VALUE['message'] = $class->errorMsg[213];
    http_response_code($RETURN_VALUE['response_code'] ); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function getResponseValue($result,$showReturnData = false){
     
    $valid = true;  
    $errorMessage = array(); 
    $successMessage = array(); 


    foreach($result as $row){ 
        $row['message'] = strip_tags($row['message']);
        if(!$row['valid']){  
            $valid = false;
            array_push($errorMessage, $row['message']); 
        }else{
            array_push($successMessage, $row['message']); 
        }
    }

    $RETURN_VALUE = array();
    if(!empty($errorMessage)){ 
        $RETURN_VALUE['response_code'] = 409;
        $RETURN_VALUE['message'] = $errorMessage;
    }else{ 
        $RETURN_VALUE['response_code'] = 200;
        $RETURN_VALUE['message'] = $successMessage;
        if($showReturnData)
            $RETURN_VALUE['data'] = $result[0]['data'];
            
    } 

    return array(
                    'returnValue' => $RETURN_VALUE,
                    'successMessage' => $successMessage,
                    'errorMessage' => $errorMessage,
                );
}

function getAPIValue($postVars,$row, $dataset, &$errorMessage){ 
    global $class;
    
    $paramName = $row['paramName'];
    $value =  $postVars[$paramName]; 
     
    // validasi field2 yg wajib diisi
    if ( isset($row['mandatory']) && $row['mandatory'] && empty($value)){  
        array_push($errorMessage, $paramName.'. ' . $class->errorMsg[603]);
        return '';
    }
 
    // kalo ada error return saja
    // if (!empty($errorMessage)) return '';
     
    //kalo value kosong langsung return saja
    if (empty($value)) return $value;
   
    $format = '';
      
    if(isset($dataset[1])){ 
        if(!is_array($dataset[1])){
             $format = (isset($dataset[1])) ? $dataset[1] : ''; 
        }else{
             $format = (isset($dataset[1]['datatype'])) ? $dataset[1]['datatype'] : ''; 
        }
    } 
     
    $value = formatAPIValue($value, $format); 
     
    if(isset($row['ref']) && !empty($row['ref'])){
         
        $tempName = $value;
        
        // kalo ad obj,
        if (isset($row['ref']['obj'])  && !empty($row['ref']['obj'])){
            $refObj = $row['ref']['obj']; 
            $refField = (isset($row['ref']['field']) && !empty($row['ref']['field'])) ?  $refObj->tableName.'.'.$row['ref']['field'] : $refObj->tableName.'.name';
            
            //$refObj->setTimeLog("search",true);
            //$refObj->setTimeLog($refField,true);
            
           // $rs = $refObj->searchData($refField,$value); 
            $rs = $refObj->searchDataRow( array( $refObj->tableName.'.pkey') , 
                                                '   and '.$refField .'='.$refObj->oDbCon->paramString($value)  
                                                ); 	
            //$refObj->setTimeLog("done",true);
            
        }else if (isset($row['ref']['tableName'])  && !empty($row['ref']['tableName'])){
            $tableName = $row['ref']['tableName']; 
            $refField = (isset($row['ref']['field']) && !empty($row['ref']['field'])) ? $tableName.'.'.$row['ref']['field'] : $tableName.'.name';
                
            $sql = 'select pkey from '.$tableName.' where '.$refField.' = ' . $class->oDbCon->paramString($value);
            $rs = $class->oDbCon->doQuery($sql); 
        }else if (isset($row['ref']['dataset'])  && !empty($row['ref']['dataset'])){
            $dataset = $row['ref']['dataset']; 
            $rs[0]['pkey'] = $dataset[strtolower($value)]; 
        }
        
        $value = (!empty($rs)) ? $rs[0]['pkey'] : '';    
         
        // validasi field2 yg wajib diisi, utk yg ref
        if ( isset($row['mandatory']) && $row['mandatory'] && empty($value)) 
            array_push($errorMessage, $tempName.'. ' . $class->errorMsg[213]); 
         
    } 
    
    return urldecode($value);
}

function getDetailsAPIParam(&$PARAM,$postVars,$apiFields, $currAPIFieldsRow = array(), &$errorMessage){ 
    global $OBJ;
     
    // variable dibutuhkan di _global
    $arrExcludeField = array('createdby', 'client_id', 'timestamp');
    
    foreach($apiFields as $key=>$row){
        $paramName = $row['paramName']; 
        $values  = (isset($postVars[$paramName]) && !empty($postVars[$paramName]))  ? $postVars[$paramName] : array();
         
        //$OBJ->setLog($paramName,true);
        
        // khusus pkey
        if($key == 'pkey' && !isset($postVars[$paramName])) 
            $postVars[$paramName] = 0;
              
        // kalo api mandatory tp kosong 
		 
        if (!isset($postVars[$paramName]) &&  isset($row['mandatory']) && $row['mandatory'])   
            array_push($errorMessage, $paramName.'. ' . $OBJ->errorMsg[603]);  
        

        if(!isset($postVars[$paramName]) || ( isset($postVars['updatable']) && !$postVars['updatable'] ) ) continue; 
        if(in_array($key,$arrExcludeField)) continue;
        
         if (isset($row['dataset'])){  
            
            if(empty($currAPIFieldsRow))
                 $PARAM['hidTotalRows'][0][0] = count($postVars[$paramName]);
            else
                 $PARAM['hidDetailItemKeyTotalRows'][1][ count($PARAM['hidDetailItemKeyTotalRows'][1]) ] = count($postVars[$paramName]);
                
            foreach($values as $valueRow)
                getDetailsAPIParam($PARAM,$valueRow,$row['detail'], $row ,$errorMessage);
          
        }else{
             
            // untuk data header
            if(empty($currAPIFieldsRow)){ 
                $value = getAPIValue($postVars,$row, $OBJ->arrData[$key], $errorMessage);  
                $PARAM[ $OBJ->arrData[$key][0] ] = $value;   
            }else{ 
                  
                $dataset = $currAPIFieldsRow['dataset'];
                
                $elName =  $dataset[$key][0];
                    
                if (!isset($PARAM[$elName])) 
                   $PARAM[$elName] = array();
                
                $value = ($key == 'pkey') ? 0 : getAPIValue($postVars, $row, $dataset[$key], $errorMessage);  
                array_push($PARAM[$elName], $value);
                 
            }
        }
        
         
    } 
 
}
 
function formatAPIValue($value, $format){
    global $class;
       
    $value = trim($value);
    
    // jenis format diambil dr CLASS
    // kalo string dikirim
    switch(strtolower($format)){
        case 'date' : $value = date("d / m / Y",$value);
                        break;
        case 'datetime' : $value = date("d / m / Y H:i",$value);
                        break;
        case 'number' : $value = $class->unformatNumber($value);
                        break;
        default : break;    
    }
    
    return $value;
}

?>
