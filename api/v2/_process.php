<?php 

require_once '_global.php';

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
$ACTION = $_SERVER['REQUEST_METHOD'];  
$RETURN_VALUE = array();

// POST / PUT 
$fileContent = file_get_contents("php://input");
parse_str($fileContent,$postVars);

//$OBJ->setLog($postVars,true);

// check token 
 /*
function getHeaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers; 
} 
*/
// check token  
// tambahan user, cek akses user
// gk bisa pake postVar karena kalo get data, ambilnya dr GET

// kemungkinan userkey sudah ad dr POST di _include.php
if(!isset($userkey) || empty($userkey))
    $userkey = (isset($_GET['_userkey']) && !empty($_GET['_userkey'])) ? $_GET['_userkey'] : '';

$actionkey = 10;
switch($ACTION){ 
    case 'POST' :  
    case 'PUT' :  $actionkey = 11; break;
    case 'DELETE' : $actionkey = 12; break;   
};

// cek Auth header bener atau gk, 
// dan punya akses atau gk
// kalo ad assign ke session ??
         
$security->APIGatePass($userkey, $fileContent, $OBJ->securityObject, $actionkey,true);

/*
if ($security->APIGatePass($userkey, $fileContent, $OBJ->securityObject, $actionkey)){
      
    // kalo credential ok 
    $_SESSION[$OBJ->loginAdminSession]['id'] = base64_encode($userkey);   // sementara tetep perlu utk catat setTransactionLog
    $OBJ->userkey = $userkey; 
}
*/
    

unset($postVars['_userkey']);  

// kalo post bagaimana ?
// echo '<br> userkey : '.$userkey;
// sudah termasuk cek pass nya bener atau gk ...
/*
if (!$OBJ->checkAPIAuth(getHeaders(),$fileContent, $userkey)){  
    $RETURN_VALUE['response_code'] = 401;
    $RETURN_VALUE['message'] = $OBJ->errorMsg[601]; 
    http_response_code($RETURN_VALUE['response_code'] ); 
    echo json_encode($RETURN_VALUE); 
    die;
}
*/

// utk ganti createdby kedepannya s..
// kalo cek credential sudah ok diatas
/*
if (isset($userkey) && !empty($userkey) ){  
    
    // kalo credential ok 
    $_SESSION[$OBJ->loginAdminSession]['id'] = base64_encode($userkey);   // sementara tetep perlu utk catat setTransactionLog
    $OBJ->userkey = $userkey; 
     
    
    unset($postVars['_userkey']); 
}
*/
  
switch($ACTION){
      
    case 'POST' :  
           
            $errorMessage = array(); 
            $PARAM = array();     
             
            getDetailsAPIParam($PARAM,$postVars, $API_FIELDS, array(), $errorMessage);
  
            // kalo ad error jgn diproses  
            if(!empty($errorMessage)){ 
                $RETURN_VALUE['response_code'] = 409;
                $RETURN_VALUE['message'] = $errorMessage;
                break;
            } 
        
            // kalo tipe code nya auto, diisi nilai default saja
            $usecode = $OBJ->useAutoCode($OBJ->tableName);  
            if($usecode) $PARAM['code'] = '[auto code]';
        
            //$OBJ->setLog($PARAM,true);
            $result = $OBJ->addData($PARAM);
            //$OBJ->setLog($result,true);
        
            $RETURN_VALUE = getResponseValue($result,true)['returnValue']; 

            break;
      
    case 'PUT' :  
        
            $errorMessage = array(); 
            $PARAM = array();    
         
            // kalo transaksi sementara jgn dulu
            if($OBJ->isTransaction){ 
                $RETURN_VALUE['response_code'] = 212;
                $RETURN_VALUE['message'] = 'temporary unavailable';
                break;
            } 
        
            getDetailsAPIParam($PARAM,$postVars, $API_FIELDS, array(), $errorMessage); 
        
            // kalo ad error jgn diproses  
            if(!empty($errorMessage)){ 
                $RETURN_VALUE['response_code'] = 409;
                $RETURN_VALUE['message'] = $errorMessage;
                break;
            }
        
        
            //get ID nya dulu  

            $rs = (isset($PARAM['code']) && !empty($PARAM['code'])) ? $OBJ->searchData($OBJ->tableName.'.code',$PARAM['code']) : array();  
    
            if (empty($rs)){
                $RETURN_VALUE['response_code'] = 409;
                $RETURN_VALUE['message'] = $OBJ->errorMsg[213];
                break;
            }
          
        
            $PARAM['hidId'] = $rs[0]['pkey']; 
            $PARAM['hidModifiedOn'] = $rs[0]['modifiedon'];  
            
            $result = $OBJ->editData($PARAM);
           
            $RETURN_VALUE = getResponseValue($result)['returnValue']; 
            break;
   
    case 'GET' :  
            if(!isset($_GET) || empty($_GET)){ 
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg[213];  
                break;
            }
          
       /*     if (!$security->isAdminLogin($OBJ->securityObject)){ 
                $RETURN_VALUE['response_code'] = 401;
                $RETURN_VALUE['message'] =  $class->errorMsg[250];  
                break;
            }*/
        
            //$criteria = (!empty($criteria)) ? ' AND ' . $criteria : '';

            $criteria = array();
        
            $orderby = (!empty($_GET['orderby'])) ? $OBJ->oDbCon->paramOrder($_GET['orderby']) : $OBJ->tableName.'.pkey'; // order by harus dr kolom yg terdaftar saja
            $ordertype = (isset($_GET['ordertype']) && !empty($_GET['ordertype']) && $_GET['ordertype'] != 1) ? 'asc' : 'desc'; 
        
            $quickSearchKey = (isset($_GET['keyword']) && !empty($_GET['keyword'])) ?  $_GET['keyword'] :  ''; 
        
            $quickSearchKey = trim($quickSearchKey);
        
            if(!empty($quickSearchKey)){
                
                // blm semua dipinhdakan ke class
                if (isset($OBJ->arrSearchColumn)){
                    $arrSearchColumn = $OBJ->arrSearchColumn;
                        
                    $quicksearchcriteria = array();
                    for($i=0;$i<count($arrSearchColumn);$i++){
                        array_push($quicksearchcriteria, $arrSearchColumn[$i][1] .' like ('.$OBJ->oDbCon->paramString( '%'.$quickSearchKey.'%' ).') ');	 
                    }
                    $quicksearchcriteria = '(' .implode(' OR ', $quicksearchcriteria).')'; 
                    array_push($criteria, $quicksearchcriteria);
                }
            }  

            //statuskey 
            if(isset($_GET['statuskey']) && !empty($_GET['statuskey'])){
                 // harus explode dulu agar lebih aman
                 $arrStatus = explode(',',$_GET['statuskey']); 
                 array_push($criteria, $OBJ->tableName.'.statuskey in ('.$OBJ->oDbCon->paramString($arrStatus,',').')' );
            }
             
            
            $order =' order by '.  $orderby  .' '. $ordertype;
            
            // kalo parameter pasti kode
            if (isset($_GET['code']) && !empty($_GET['code']))  
                array_push($criteria, $OBJ->tableName.'.code = '.$OBJ->oDbCon->paramString($_GET['code'])); 
            
           /* if(empty($criteria)){ 
                $RETURN_VALUE['response_code'] = 409;
                $RETURN_VALUE['message'] = $class->errorMsg[213];  
                break;
            }
            */
        
            $criteria  =  implode(' AND ', $criteria);
            if (!empty($criteria))
                    $criteria = ' AND ' . $criteria;
          
            // LIMIT   
            $totalRows = isset($_GET['rowPerPage']) ? $_GET['rowPerPage']: $OBJ->loadSetting('adminTotalRowsPerPage');
            $offset = isset($_GET['offset']) ? $_GET['offset'] : 1; 
            if($offset <= 0) $offset = 1;
            $limitFrom = ($offset - 1) * $totalRows;  
            $limit = ' limit '.$limitFrom.','.$totalRows; 
            

            $OBJ->setCriteria($criteria);  
            $query = $OBJ->getQueryForList();
            if (empty($query))
                $query = $OBJ->getQuery();
        
        
            $rs =  $OBJ->oDbCon->doQuery( $query . $order . $limit  );  

            //ganti semua model refkey dengan refcode
            foreach($RETURN_IN_CODE as $key=>$row){
                for($i=0;$i<count($rs);$i++){ 
                    $rsTemp = $row['obj']->getDataRowById($rs[$i][$key]);
                    $rs[$i][$key] = (isset($rsTemp[0]['code']) && !empty($rsTemp[0]['code'])) ? $rsTemp[0]['code'] : USER_SYSTEM['code'];
                }
            }

            // test 
            if  (!empty($RETURN_FIELDS))
                $API_FIELDS = array_merge($API_FIELDS,$RETURN_FIELDS); 
                
            $showDetail = (isset($_GET['detail']) && !empty($_GET['detail'])) ? true : false;
          
            $rs = $OBJ->compileAPIField($rs,$API_FIELDS,$showDetail);
         
            //$rs = array_column($rs,null,'code');

            // compile array for return 
            $RETURN_VALUE['response_code'] = 200;
            $RETURN_VALUE['data'] = $rs;  
            $RETURN_VALUE['message'] = '';  

            break;
         
    case 'DELETE' :    
            if(!isset($postVars) || empty($postVars['code'])){ 
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg['code'][1];  
                break;
            } 

            $code = $postVars['code']; 
          
            $rs = $OBJ->searchData('','',true,' and '.$OBJ->tableName.'.code = '.$OBJ->oDbCon->paramString($code));  
            if(empty($rs)){ 
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg[213];  
                break;
            }
          
            $result = $OBJ->delete($rs[0]['pkey']);

            // compile array for return 
            $RETURN_VALUE = getResponseValue($result)['returnValue'];
         
            break;
         
     default : break;    
}

http_response_code($RETURN_VALUE['response_code'] ); 
echo json_encode($RETURN_VALUE); 
  

?>