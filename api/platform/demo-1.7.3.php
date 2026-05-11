<?php   
// secretkey for logoldemo 
define('SECRET_KEY', '123456'); 
//define('SECRET_KEY', 'bf49958d3b238b11d1a520e6899216db'); 
 
define('API_URL', 'https://minerva.local/api/logol/');
//define('API_URL', 'https://logoldemo.wintera.co.id/api/logol/');

// ==================================== START REQUEST
 
//getCustomers(); 
//updateCustomers();  
//getSuppliers(); 
//updateSuppliers();  
//getLocations();  
//updateLocations();  
//getServices();
//updateServices();

//getJO();
//addJO();
//addWO();
//getWO();
updateJO();
//changeStatusJO();
//cancelJO();
//getCN();
//getInvoice();
//getInvoicePDF();    
//addInvoice(); 
//updateVANumber();
//changeStatusInvoice();   

//updateInvoice();


function getCustomers(){
    $url = API_URL.'customers/code=CUST-20191115-0002,DEMO0001'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function updateCustomers(){
    $url = API_URL.'customers/';
    $payload = array();
    
    array_push($payload,
                array(
                    'code' => 'CUST-20210505-0001',
                    'name' => 'CUST-20210505-OK', 
                    'tax_id' => 'TX0000021',
                    'address' => 'Gedung Mangga Dua',
                    'category_name' => 'End User', 
                ) 
              ); 
     
    // gagal
   /* array_push($payload,
                array(
                    'code' => 'CUST-20210414-0004',
                    'name' => 'PT SIGMA JAYA PRATAMA', 
                    'address' => 'Sahari Batu Timur',
                    'category_name' => 'End User', 
                    'status' => 'Aktif', 
                ) 
              ); 
     
    array_push($payload,
                array(
                    'code' => 'API-0001-'.time(),
                    'tax_id' => 'TX03432423',
                    'name' => 'Merry Siregar 03'.time(), 
                    'address' => 'Sahari Batu Barat',
                    'category_name' => 'End User', 
                    'status' => 'Aktif', 
                ) 
              ); 
     
    array_push($payload,
                array(
                    'code' => 'API-0002-'.time(),
                    'tax_id' => 'TX03432423',
                    'name' => 'Merry Siregar 023'.time(), 
                    'address' => 'Sahari Batu Barat',
                    'category_name' => 'End User', 
                    'status' => 'Aktif', 
                ) 
              ); 
     
         
    // gagal
    array_push($payload,
                array(
                    'code' => 'CUST-20210329-0001',
                    'name' => 'PT LEMO UTAMA', 
                    'address' => 'Sahari Batu Timur',
                    'category_name' => 'End User', 
                    'status' => 'Aktif', 
                ) 
              ); 
    
    array_push($payload,
                array(
                    'code' => 'API-0003-'.time(),
                    'tax_id' => 'TX03432423',
                    'name' => 'Merry Siregar A '.time(), 
                    'address' => 'Sahari Batu Barat',
                    'category_name' => 'End User', 
                    'status' => 'Aktif', 
                ) 
              ); */
      
    $action = 'PUT';

    execute($url, $payload, $action);
}
 
function getSuppliers(){
    $url = API_URL.'suppliers/code=VEND-20200603-0006,VEND-20200603-0003'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function updateSuppliers(){
    $url = API_URL.'suppliers/';
    $payload = array();
    
    array_push($payload,
                array(
                    'code' => 'SUPP-0001',
                    'name' => 'PT. ABC', 
                    'address' => 'Gedung Mangga Dua', 
                ) 
              ); 
     
    array_push($payload,
                array(
                    'code' => 'SUPP-0002',
                    'name' => 'PT. Antaloka', 
                    'address' => 'Gedung Sarina', 
                ) 
              ); 
     
    array_push($payload,
                array(
                    'code' => 'SUPP-0003',
                    'name' => 'PT. Antaloka', 
                    'address' => 'Gedung Sarina', 
                ) 
              ); 
     
    $action = 'PUT';

    execute($url, $payload, $action);
}

function getLocations(){
    $url = API_URL.'locations/code=DIST-0139,DIST-0135,DIST-01019'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function getCitiesCategory(){
    $url = API_URL.'customer-categories/code=CCAT00002,123213,CCAT00001'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function updateLocations(){
    $url = API_URL.'locations/';
    $payload = array();
    
    array_push($payload,
                array(
                    'code' => 'LOC-0001',
                    'name' => 'DKI Jakarta',  
                ) 
              ); 
     
    array_push($payload,
                array(
                    'code' => 'LOC-0002',
                    'name' => 'Bali', 
                    'status' => 'Non Aktif', 
                ) 
              ); 
      
    array_push($payload,
                array(
                    'code' => 'LOC-0003',
                    'name' => 'Bali Timur',   
                ) 
              ); 
      
     
    $action = 'PUT';

    execute($url, $payload, $action);
}

function getServices(){
    $url = API_URL.'trucking-services/code=CNTP-19,CNTP-12'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function updateServices(){
    $url = API_URL.'trucking-services/';
    $payload = array();
    
    array_push($payload,
                array(
                    'code' => 'SRV-0001',
                    'name' => 'Port Handling',  
                    'category_name' => 'Export',  
                ) 
              ); 
     
    array_push($payload,
                array(
                    'code' => 'SRV-0002',
                    'name' => 'Demorage', 
                    'category_name' => 'Umum', 
                ) 
              );
     
    $action = 'PUT';

    execute($url, $payload, $action);
}

function getJO(){
    $url = API_URL.'trucking-service-order/statuskey=6&show_detail=1'; 
    //$url = API_URL.'trucking-service-order/code=LG/202106/E0088'; 
    
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function addJO(){
    $url = API_URL.'trucking-service-order/'; // create job order
    //   $url = API_URL.'trucking-so-cost/'; // add costing
    
    $payload = array();
      
    array_push($payload,
           array(
                'code' => 'API-'.time(),
                'date' =>  1617963197, // Unix timestamp
                'request_id' => 'H0001',
                'customer_id' => 'CUST-20201223-0001',
                'consignee_name' => 'Eka Distributor 1',
                'category_name' => 'EXPORT - MANUAL',
                'cargo_type' => 'DRY', 
                'depot_id' => 'DP00034', 
                'terminal_id' => 'TER00005', 
                'description' => 'API TESTING', 
                //'change_status_to' => 5,
                // trucking charges
                'service_detail' => array( 
                                        array( 
                                            'request_id' => 'D-0001',
                                            'service_id' => 'CNTP-0007', // 40 GP
                                            'qty' => 2,    
                                            'shipment_date' => '1617963197000', // Unix timestamp
                                            'price' => 1500000,        
                                            'group' => 1,       
                                          )   
                                    ),
               
                // additional charges
               /* 'additional_selling_detail' => array(
                                            array( 
                                            'request_id' => 'SD-0001',
                                            'cost_id' => 'REIM-0031', // handling fee, tax
                                            'qty' => 2,     
                                            'price' => 15000 
                                          ),
                                           array( 
                                            'request_id' => 'SD-0002',
                                            'cost_id' => 'REIM-0068', // LIFT OFF / GATEPASS
                                            'qty' => 2,     
                                            'price' => 350000
                                          ),
                                        ), 
               
                // costing 
                //$url = API_URL.'trucking-so-cost/';
                'additional_cost_detail' => array(
                                            array( 
                                            'request_id' => 'CD-0001',
                                            'cost_id' => 'REIM-0068', // LIFT OFF ID / GATEPASS
                                            'qty' => 2,     
                                            'request_amount' => 350000 
                                          ), 
                                        ), */ 
               
            )
    ); 
          
      
    $action = 'POST';

    execute($url, $payload, $action);
}

function addJOCost(){ 
    $url = API_URL.'trucking-so-cost/';
    
    $payload = array();
    
    array_push(  $payload,
                array(
                    'code' => 'DEMO-0031', 
                    'additional_cost' => array(
                                            array(
                                                    'cost_id' => 'REIM-0024',
                                                    'qty' => 3,     
                                                    'request_amount' => 10000,         
                                            ),
                                            array(
                                                    'cost_id' => 'REIM-0031',
                                                    'qty' => 4,     
                                                    'request_amount' => 20000,         
                                            ) 
                                        )
                ),
               
                array(
                    'code' => 'DEMO-0030', 
                    'additional_cost' => array(
                                            array(
                                                    'cost_id' => 'REIM-0031',
                                                    'qty' => 7,     
                                                    'request_amount' => 25000,         
                                            ),
                                            array(
                                                    'cost_id' => 'REIM-0024',
                                                    'qty' => 8,     
                                                    'request_amount' => 30000,         
                                            ) 
                                        )
                ),
               
                /*array(
                    'code' => 'DEMO-LG-ERR', 
                    'additional_cost' => array(
                                            array(
                                                    'cost_name' => 'Rush Handling Pelayaran',
                                                    'qty' => 7,     
                                                    'request_amount' => 70000,         
                                            ),
                                            array(
                                                    'cost_name' => 'Biaya Stacking Awal',
                                                    'qty' => 8,     
                                                    'request_amount' => 80000,         
                                            ) 
                                        )
                )*/
    );
    
    $action = 'POST';
    execute($url, $payload, $action);
}

function addWO(){
    $url = API_URL.'trucking-work-order/'; 
     
    $payload = array();
      
    array_push($payload,
           array(
                'code' => 'y'.time(),
                'date' =>  1617963197, // Unix timestamp
                'stuffing_date' => 1617963197,
                'job_order_id' => 'LG/202104/I0073', 
                'is_outsource' => 1, 
                'supplier_id' => 'VEND-20200724-9999', 
                'description' => 'jobs description in here', 
                'goods_description' => 'heli', 
                'vehicle_detail' => array( 
                                        array( 
                                            'service_id' => 'CNTP-0007',
                                            'vehicle_registration_number' => 'B 1234 RT',    
                                            'container_number' => '12313123', // Unix timestamp
                                            'seal_number' => 'S00000001',        
                                            'qty' => 1,           
                                            'price' => 1000000,           
                                            'tax_percentage' => 10,       
                                            'tax23_percentage' => 2,       
                                          )  ,
                                         array( 
                                            'service_id' => 'CNTP-0007',
                                            'vehicle_registration_number' => 'B 578 RT',    
                                            'container_number' => 'CO12345', // Unix timestamp
                                            'seal_number' => 'S00000002',        
                                            'qty' => 1,           
                                            'price' => 2000000,           
                                            'tax_percentage' => 0,       
                                            'tax23_percentage' => 4,       
                                          )   
                                    ) 
            )
    ); 
      
    
    array_push($payload,
           array(
                'code' => 'x'.time(),
                'date' =>  1617963197, // Unix timestamp
                'stuffing_date' => 1617963197,
                'job_order_id' => 'LG/202104/I0073',
               
                'is_outsource' => 1, 
                'supplier_id' => 'VEND-20201215-0001', 
                'description' => 'jobs description in here', 
                'goods_description' => 'heli 2', 
                'auto_proceed' => 1,
                'vehicle_detail' => array( 
                                        array( 
                                            'service_id' => 'CNTP-0006',
                                            'vehicle_registration_number' => 'B 1234 FV',    
                                            'container_number' => 'FV0001', // Unix timestamp
                                            'seal_number' => 'FVS0001',        
                                            'qty' => 1,           
                                            'price' => 1400000,           
                                            'tax_percentage' => 0,       
                                            'tax23_percentage' => 2,       
                                          )  ,
                                         array( 
                                            'service_id' => 'CNTP-0006',
                                            'vehicle_registration_number' => 'B 578 FV',    
                                            'container_number' => 'FV00002', // Unix timestamp
                                            'seal_number' => 'FVS0002',        
                                            'qty' => 1,           
                                            'price' => 1500000,           
                                            'tax_percentage' => 0,       
                                            'tax23_percentage' => 4,       
                                          )   
                                    ) 
            )
    ); 
      
    
    $action = 'POST';

    execute($url, $payload, $action);
}

function getWO(){
    $url = API_URL.'trucking-work-order/rows_per_page=25&offset=1&show_detail=1'; 
    
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function getInvoicePDF(){
      
    $url = API_URL.'print/module=trucking-invoice&code=INV-00015/2021&customer_id=CUST-20210226-0001';  
    $payload = array(); 
    $action = 'GET';
    
    header('Content-type: application/pdf;'); 
    header('Content-Disposition: inline;'); 
               
    execute($url, $payload, $action, false);
}

function getInvoice(){
      
    // you can use "show_detail" or "_detail" as param for showing detail
    $url = API_URL.'trucking-so-invoice/rows_per_page=25&offset=1&show_detail=1&statuskey=2,3';
    
    $payload = array(); 
    $action = 'GET'; 
               
    execute($url, $payload, $action);
}


function getCN(){
      
    // you can use "show_detail" or "_detail" as param for showing detail
    $url = API_URL.'ap/show_detail=1';
    
    $payload = array(); 
    $action = 'GET'; 
               
    execute($url, $payload, $action);
}


function cancelJO(){
    $url = API_URL.'trucking-service-order/cancel'; // cancel job order
    
    $payload = array();
    array_push($payload, 'LG/210827/E0196');
    
    $action = 'POST';

    execute($url, $payload, $action);
}

function changeStatusJO(){
     $url = API_URL.'trucking-service-order/change-status/3';
    
    $payload = array();
    array_push($payload, 'LG/210825/E0185');
    array_push($payload, 'LG/210824/E0172');
    
    $action = 'POST';

    execute($url, $payload, $action);
}

function changeStatusInvoice(){
    $url = API_URL.'trucking-so-invoice/change-status/2';
    
    $payload = array();
    array_push($payload, 'DN/EX/2021/04/00047');  
    
    $action = 'POST';

    execute($url, $payload, $action);
}

function addInvoice(){
    $url = API_URL.'trucking-so-invoice/'; // create invoice
    
    $payload = array();
      
    array_push($payload,
           array(
                'code' => 'API-'.time(),
                'date' =>  1617963197, // Unix timestamp 
                'customer_id' => 'CUST-20200805-0001',
                'invoice_type_key' => 1, // ex / im / egate / etc...
                'term_of_payment_key' => 1, // 1 for cash (paynow)
                'company_bank_key' => 5, // VA channel Key
                'va_number' => 'VA1234', 
                'description' => 'API TESTING', 
                'container_number' => 'CT0001, CT0002, CT0003', 
                'request_id' => 'R0001', 
                'job_order_detail' => array(
                                            array( 
                                                'transaction_type_key' => 1,
                                                'job_order_id' => 'LG/210825/I0051', 
                                                'service_detail' => array(
                                                                        array(
                                                                            'qty' => 1,
                                                                            'service_id' => 'REIM-0085',
                                                                            'service_alias' => 'Biaya Reimburse', 
                                                                            'vat_percentage' => '10',
                                                                            'vat_include' => 1,
                                                                            'tax_article_23' => 1,  
                                                                        ),
                                                                        array(
                                                                            'qty' => 1,
                                                                            'service_id' => 'REIM-0001',
                                                                            'vat_percentage' => '1',
                                                                            'vat_include' => 0,
                                                                        ),
                                                                    )
                                            ), 
                                        ),   
                'payment_channel_detail' => array(
                                        array(
                                            'payment_channel_key' => 5,
                                            'amount' => '4735800',
                                        ),
                            ),
            )
    );
    
    
    $action = 'POST';

    execute($url, $payload, $action);
}

function updateInvoice(){
    $url = API_URL.'trucking-so-invoice/';
    $payload = array();
    
    array_push($payload,
                array(
                    'code' => 'DN/EX/2021/09/00095', 
                    'description' => 'testing api', 
                    'job_order_detail' => array(
                        array(
                            'key' => 9453,
                            'description' => 'catatan detail'
                        )
                    )
                ) 
              );  
     
    $action = 'PUT';

    execute($url, $payload, $action);
}
 

function updateJO(){
    $url = API_URL.'trucking-service-order/';
    $payload = array();
    
    array_push($payload,
                array(
                    'code' => 'LG-0000004', 
                    //'request_id' => 'H0001',
                    //'key' => 11885,
                    'description' => 'update api - '.time() ,
                    'service_detail' => array(
                        array(
                            'request_id' => 'D-0003',
                            'qty' => '33',
                        ),
                        array(
                            'request_id' => 'D-0002',
                            'qty' => '22',
                        )
                    ),
                    
                    'additional_cost_detail' => array(
                        array(
                            'request_id' => 'CD-00021',
                            'qty' => '21',
                        ),
                        array(
                            'request_id' => 'CD-00011',
                            'qty' => '11',
                        )
                    ),
                    
                    'additional_selling_detail' =>  array(
                        array(
                            'request_id' => 'RD-0002',
                            'cost_id' => 'REIM-0031',
                            'price' => 22000,
                            'qty' => 2
                        ),
                        array(
                            'request_id' => 'RD-0001',
                            'cost_id' => 'REIM-0014',
                            'price' => 11000,
                            'qty' => 10
                        )
                    )
                    
                )  
              );  
     
    $action = 'PUT';

    execute($url, $payload, $action);
}
 
function updateVANumber(){
    $url = API_URL.'trucking-invoice-va/';
    $payload = array();
    
    array_push($payload,
                array(
                    'code' => 'DN/GT/2021/09/00001', 
                    'va_number' => 'testing api VA', 
                ), 
                array(
                    'code' => 'DN/EX/2021/09/00094/A01', 
                    'va_number' => 'testing api 2', 
                )  
              );  
     
    $action = 'POST';

    execute($url, $payload, $action);
}
 

function execute($url, $payload, $action, $pretty = true){ 
    
    $payload = json_encode($payload); 
    //echo  $payload.'<br>';   
        
    $auth = hash_hmac('sha256', $url, SECRET_KEY);
    echo $url.'<br>';
    echo SECRET_KEY.'<br>';
    echo $auth.'<br>';

    $header = array(
        'Content-Type: application/json',  
        'Authorization: '.$auth
    );

    $connection = curl_init(); 
 
    if ($action <> 'GET') 
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload);
    
    curl_setopt($connection, CURLOPT_URL, $url); 
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_HTTPHEADER,$header); 
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $action);

    $response = curl_exec($connection);
     
    curl_close($connection);   
     
    if($pretty){ 
        $response = json_decode($response,true);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }else{ 
         echo $response; 
    }

    return $response;
}

?>