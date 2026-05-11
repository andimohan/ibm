<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array("Warehouse.class.php","Brand.class.php","City.class.php"));
$warehouse = new Warehouse();
$brand = new Brand();
$city = new City();

$selectedCityKey = (isset($_GET) && !empty($_GET['citykey'])) ? $_GET['citykey'] : 0;
$_POST['selCity'] = $selectedCityKey;

$rsBrand = $brand->searchDataRow(array($brand->tableName.'.pkey',$brand->tableName.'.code',$brand->tableName.'.name'),
                                      ' and '.$brand->tableName.'.statuskey = 1',
                                      ' order by orderlist asc'
                                    );

$criteria = ' and '.$warehouse->tableName.'.statuskey = 1 and ('.$warehouse->tableName.'.location <> \'\' or  '.$warehouse->tableName.'.address <> \'\')';
if (!empty($selectedCityKey)) 
    $criteria .= ' and '.$warehouse->tableName.'.citykey in ('.$warehouse->oDbCon->paramString($selectedCityKey,',').')';

$selectedBrandKey = 0;
if(isset($_GET['brandkey']) && !empty($_GET['brandkey'])){  
    
    if(in_array($_GET['brandkey'], array_column($rsBrand,'pkey'))){ 
        $criteria .= ' and '.$warehouse->tableName.'.brandkey = ' . $warehouse->oDbCon->paramString($_GET['brandkey']);
        $selectedBrandKey = $_GET['brandkey'];
    }
        
}

$rsWarehouse = $warehouse->searchData('','',true,$criteria);
$rsAllWarehouse = $warehouse->searchData('','',true,' and '.$warehouse->tableName.'.statuskey = 1');

// utk pinpoint google map
$arrLocation = array();
$arrLocation['type'] = 'FeatureCollection';
$arrLocation['features'] = array();
    
foreach($rsAllWarehouse as $key=>$row){
    $location = explode(',',$row['location']);
    
    $temp = array(
        'geometry' => array(
                            'type' => 'Point',
                            'coordinates' => array(floatval($location[1]),floatval($location[0])), // ntah kenapa dibalik lat lng nya
                        ),
        'type' => 'Feature',
        'properties' => array(   
               'pkey' =>  $row['pkey'],
               'name' =>  $row['name'], 
               'address' => str_replace(chr(13),'<br>',$row['address']),
               'phone' => (!empty($row['phone'])) ? $row['phone'] : '',
               'storeid'=> $row['code']
        ) 
    );
    
    array_push($arrLocation['features'],$temp);
}


// available location

// jgn difilter, kal ogk nant iopsi searchny hilang 
//$arrCityKey = array_column($rsWarehouse,'citykey');
$rsCity = $city->searchDataRow(array($city->tableName.'.pkey',$city->tableName.'.name'),
                              ' and '.$city->tableName.'.statuskey = 1',
                              'order by '.$city->tableName.'.name asc');

//$class->setLog(json_encode($arrLocation),true);
$arrTwigVar['selectedBrandKey'] = $selectedBrandKey; 
$arrTwigVar['rsStores'] = $rsWarehouse; 
$arrTwigVar['rsBrand'] = $rsBrand; 
$arrTwigVar['location'] = json_encode($arrLocation);

$_POST['selBrand'] = $selectedBrandKey;

$arrCity = $class->generateComboboxOpt(array('data' => $rsCity ),'',$class->lang['allLocation']);  
$arrTwigVar ['selCity']  = $class->inputSelect('selCity',$arrCity);  
$arrTwigVar ['selectedCityKey']  = $selectedCityKey;

echo $twig->render('stores.html', $arrTwigVar);

?>