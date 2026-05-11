<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  
includeClass(array('WidgetSetting.class.php'));
$widgetSetting = new WidgetSetting();
$obj = $widgetSetting;  
include 'ajax-general.php';

if (isset($_POST) && !empty($_POST['action'])) {
			switch ( $_POST['action']){ 
                case 'removeWidget' :
                     
                    $widgetkey = $_POST['widgetkey'];
                    $obj->removeWidget($obj->userkey,$widgetkey); 
                    
                    echo json_encode($widgetSetting->getSelectedWidgets()); 
                    break;
                    
                case 'updateSettings' : 
                    
                    $_POST['employeekey'] = $obj->userkey; 
                    $obj->updateSettings($_POST); 
                      
                    echo json_encode($widgetSetting->getSelectedWidgets()); 
 
                    break;
					
                case 'updateWidgetProperties' : 
                    $_POST['employeekey'] = $obj->userkey; 
                    $obj->updateWidgetProperties($_POST); 
                        
                    break;
					
				case 'getPropertiesValue':
					if(!isset($_POST['widgetkey']) || empty($_POST['widgetkey'])) die; 
                    $return = json_encode($widgetSetting->getPropertiesValue($_POST['widgetkey']));   
					echo $return;
					break;
					
				case 'getPropertiesByFunc':
					if(!isset($_POST['key']) || empty($_POST['key'])) die; 
					
					switch ( $_POST['key']){ 
							case 'serviceList' : 
								includeClass(array('Service.class.php'));
								$service = new Service(SERVICE);
								$rs = $service->searchDataRow(array($service->tableName.'.pkey as \'key\'', $service->tableName.'.name as label'), 
															   'and  '.$service->tableName.'.statuskey = 1  
																and '.$service->tableName.'.itemtype = '.SERVICE.'  
																and '.$service->tableName.'.servicecost = 0 ' ); 


								echo json_encode($rs);
								break;
                            
                            case 'categoryList' : 
                                // sementara level root dulu
								includeClass(array('Category.class.php','ServiceCategory.class.php'));
								$serviceCategory = new ServiceCategory();
								$rs = $serviceCategory->searchDataRow(array($serviceCategory->tableName.'.pkey as \'key\'', $serviceCategory->tableName.'.name as label'), 
															   'and '.$serviceCategory->tableName.'.statuskey = 1  
                                                                and '.$serviceCategory->tableName.'.parentkey = 0' ); 


								echo json_encode($rs);
								break;
					}
					
					break;
            } 
}
die; 
?>
