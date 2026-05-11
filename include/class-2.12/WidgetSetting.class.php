<?php
class WidgetSetting extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = '_widget_setting'; 
		$this->tableProperties = '_widget_properties'; 
		$this->tablePropertiesValue = 'widget_properties_values';  
		$this->tableEmployee = 'employee'; 
		$this->tableWidget = '_widget'; 
      
	}
	
 	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,  
					'.$this->tableWidget. '.pkey as widgetkey,
					'.$this->tableWidget. '.name,
					'.$this->tableWidget. '.title,
					'.$this->tableWidget. '.securityobject,
					'.$this->tableWidget. '.width,
					'.$this->tableWidget. '.height,
					'.$this->tableWidget. '.additionalstyle,
					'.$this->tableWidget. '.additionalclass,
					'.$this->tableWidget. '.isdefault,
					'.$this->tableWidget. '.usercategorykey,
					'.$this->tableWidget. '.orderlist
                    
				from
					'.$this->tableName.',
                    '.$this->tableWidget.' 
                where 
                    '.$this->tableName.'.refkey = '.$this->tableWidget.'.pkey 
                    
 		' .$this->criteria ; 
           
         return $sql;
    }
      
    function getWidgets($widgetKey = array(), $criteria = ''){
            
        $sql = 'select 
                    * ,
                    pkey as widgetkey
                from 
                    '.$this->tableWidget.'  
                where
                   (
                    usercategorykey like \''.PLAN_TYPE['categorykey'].',%\' or 
                    usercategorykey like \'%,'.PLAN_TYPE['categorykey'].',%\' or 
                    usercategorykey like \'%,'.PLAN_TYPE['categorykey'].'\' or 
                    usercategorykey like \''.PLAN_TYPE['categorykey'].'\' 
                   )
                   and '.$this->tableWidget.'.statuskey = 1 ';
        
        if (!empty($widgetKey))
            $sql .= ' and pkey in ('. $this->oDbCon->paramString($widgetKey,true).')';
        
        
        if (!empty($criteria))
            $sql .= $criteria;
        
        $sql .= ' order by orderlist asc, name asc';
        
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);
    }
    
   function updateSettings($arrParam){
       
       try{
	
			if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);


            $userWidget = $arrParam['employeekey'];
            $sql = 'delete from '.$this->tableName.' where userkey = '. $this->oDbCon->paramString($userWidget);
            $this->oDbCon->execute($sql);

            $rsWidget = $this->getWidgets();

            foreach($rsWidget as $row){

                  if(isset($arrParam['chkWidget-'.$row['pkey']]) && !empty($arrParam['chkWidget-'.$row['pkey']])){
                        $sql = 'insert into '.$this->tableName.' (
                                refkey,
                                userkey
                             ) values (
                                '.$this->oDbCon->paramString($row['pkey']).',
                                '.$this->oDbCon->paramString($userWidget).'
                            )';	 

                      $this->oDbCon->execute($sql);	  

                  }

            }
 
            $sql = 'update ' . $this->tableEmployee .' set widgetchanged = 1 where pkey = ' .  $this->oDbCon->paramString($userWidget);
            $this->oDbCon->execute($sql);	
           
            $this->oDbCon->endTrans();
 
        }catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}	 		
	}
    
    function removeWidget($userkey, $widgetkey){
        
         try{
	
			if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
 
            $sql = 'delete from '.$this->tableName.' where userkey = '. $this->oDbCon->paramString($this->userkey) .' and refkey = ' .  $this->oDbCon->paramString($widgetkey);
            $this->oDbCon->execute($sql);	
           
            $this->oDbCon->endTrans();
 
        }catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}	 		
        
    }

    function normalizeParameter($arrParam, $trim=false){ 
          
       
        return $arrParam; 
    }
 
    function getSelectedWidgets(){  
        $employee = new Employee();
        
        $rsEmployee = $employee->searchDataRow(array($employee->tableName.'.widgetchanged'),
										  ' and '.$employee->tableName.'.pkey = '. $this->oDbCon->paramString($this->userkey));
		
        $widgetchanged = ($rsEmployee[0]['widgetchanged'] == 1) ? true : false;
        
        $rsWidgetShowed = $this->searchData($this->tableWidget.'.statuskey',1,true,' and '.$this->tableName.'.userkey = '  . $this->oDbCon->paramString($this->userkey), '', ' order by orderlist asc, title asc');

        // ambil default
        if(empty($rsWidgetShowed) && !$widgetchanged) { 
            $rsWidgetShowed = $this->getWidgets('',' and isdefault = 1');
            $savedWidget = array_column($rsWidgetShowed,'pkey'); 
        }else{  
            $savedWidget = array_column($rsWidgetShowed,'refkey'); 
        }

        $arrWidgets = array();  
        foreach($rsWidgetShowed as $row) 
            $this->pushPanel($arrWidgets,array( 
                                        'pkey' => $row['widgetkey'], 
                                        'title' => $row['title'], 
                                        'securityObject' => $row['securityobject'], 
                                        'panel' => $row['name'], 
                                        'width' => $row['width'], 
                                        'height' => $row['height'], 
                                        'additionalClass' => $row['additionalclass'], 
                                        'additionalStyle' => $row['additionalstyle'], 
                                        'usercategorykey' => explode(',',$row['usercategorykey'])
                                       )
                            );


        return $arrWidgets;
    } 

    function pushPanel(&$arrWidgets,$arrPanelOptions){
         global $security; 

         if (!empty($arrPanelOptions['usercategorykey']) && !in_array(PLAN_TYPE['categorykey'], $arrPanelOptions['usercategorykey'] )) return ;

         $userkey = $security->userkey; 
        
         $arrSecurityObject = explode(',',$arrPanelOptions['securityObject']);

         foreach($arrSecurityObject as $row){ 
            $row = trim($row);
            if(!$security->hasSecurityAccess( $userkey ,$security->getSecurityKey($row),10)) return; 
         } 

        unset($arrPanelOptions['securityObject']);
        unset($arrPanelOptions['usercategorykey']);          
        array_push($arrWidgets ,$arrPanelOptions);
    }
    
    function getPropertiesValue($widgetkey = '', $userkey = ''){
        
        $userkey = (empty($userkey)) ? $this->userkey : $userkey;
        
        $sql  = 'select 
                   '.$this->tableWidget.'.name,
                   '.$this->tableProperties.'.properties,
                   '.$this->tableProperties.'.label,
                   '.$this->tableProperties.'.defaultvalue,
                   '.$this->tableProperties.'.opt,
                   '.$this->tablePropertiesValue.'.userkey,
                   '.$this->tablePropertiesValue.'.value
                from
                    '.$this->tableWidget.',
                    '.$this->tableProperties.' 
                        left join '.$this->tablePropertiesValue.' on  '.$this->tablePropertiesValue.'.refkey = '.$this->tableProperties.'.pkey
						and  '.$this->tablePropertiesValue.'.userkey = '.$this->oDbCon->paramString($userkey).' 
                where 
                    '.$this->tableProperties.'.refkey = '.$this->tableWidget.'.pkey  
                ';
		
		if ($widgetkey != '')
			 $sql  .= ' and '.$this->tableWidget.'.pkey = '.$this->oDbCon->paramString($widgetkey);
		
		// buat decode ulang kalo yg save jso ndr form
		// utk yg defaultvaalue, tetep aman meskipun pake "
	 	$rs = $this->oDbCon->doQuery($sql);
		 
		foreach($rs as $key=>$row){ 
            
			// update lang label
			if(isset($row['opt']) && !empty($row['opt'])){
				$opt = json_decode($row['opt'],true); 
				if(isset($opt['dataset']) && !empty($opt['dataset'])){ 
					$totalOpt = count($opt['dataset']);
					for($i=0;$i<$totalOpt;$i++){  
						$label = $opt['dataset'][$i]['label'];
						$opt['dataset'][$i]['label'] =  (isset($this->lang[$label])) ? $this->lang[$label] : $label;
					}
				}else if (isset($opt['select-opt']) && !empty($opt['select-opt'])){
					//$totalOpt = count($opt['select-opt']);
					foreach($opt['select-opt'] as $optKey => $optRow){  
						$label = $optRow['label'];
						$opt['select-opt'][$optKey]['label'] =  (isset($this->lang[$label])) ? $this->lang[$label] : $label;
					}
				}else if (isset($opt['select']) && !empty($opt['select'])){
					//$totalOpt = count($opt['select']);
					foreach($opt['select'] as $optKey => $optRow){  
						$label = $optRow;
						$opt['select'][$optKey] =  (isset($this->lang[$label])) ? $this->lang[$label] : $label;
					}
				}else if (isset($opt['dataset'])){
					 //$opt['dataset'][$optKey] 
				}
				
				$rs[$key]['opt'] = json_encode($opt);
			}
			
			$rs[$key]['label'] =   (isset($this->lang[$rs[$key]['label']])) ? $this->lang[$rs[$key]['label']] : $rs[$key]['label'];
			$rs[$key]['value'] = htmlspecialchars_decode($rs[$key]['value']??'');  
		}
		  
        return $rs;
        
    }
 
	function updateWidgetProperties($arrParam){

		$widgetkey = $arrParam['hidPanelKey'];
		   
	   try{
	
			if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
 
           // sementar hapus semua dulu
		   $sql = 'delete '.$this->tablePropertiesValue.'.* from 
		   			'.$this->tablePropertiesValue.' 
	  		  where '.$this->tablePropertiesValue.'.widgetkey = '.$this->oDbCon->paramString($widgetkey).'
			   and  '.$this->tablePropertiesValue.'.userkey = '.$this->oDbCon->paramString($this->userkey);
		
		   $this->oDbCon->execute($sql);
		  
		  // ambil ulang field2 properties
		  $sql = 'select pkey,properties, opt from '.$this->tableProperties.' where  '.$this->tableProperties.'.refkey = '.$this->oDbCon->paramString($widgetkey);
		  $rs =   $this->oDbCon->doQuery($sql);
        
		  $rs = array_column($rs,null,'properties');
		   
		  foreach($rs as $propertiesRow){
              $propertiesRow['opt'] = json_decode($propertiesRow['opt'],true);
              $type = array_keys($propertiesRow['opt'])[0];
              
			  $propertiesName = $propertiesRow['properties']; 
			  $pkey = $propertiesRow['pkey'];
			 
			  if (!isset($arrParam[$propertiesName])) continue;
              
              
			  if (is_array($arrParam[$propertiesName])){ 
 			    //hapus yg empty string
			    foreach( $arrParam[$propertiesName] as $key=>$row) if (empty($row)) unset($arrParam[$propertiesName][$key]);
				$arrParam[$propertiesName] = array_values($arrParam[$propertiesName]);
				$arrParam[$propertiesName] = json_encode($arrParam[$propertiesName]);  
			  }
                
			   
              switch($type){
                  // kaalo pake select, hrusnya sudah pasti array untuk saat ini
                  case 'select-opt' :  $typekey =  $arrParam['selOptType-'.$propertiesName];
                      
                                       // pecah per type
                                       $arrValueType=array();
                                       $opt = $propertiesRow['opt']['select-opt']; 
                      
                                       // balikin ulang ke array dulu
                                       $arrParam[$propertiesName] = json_decode($arrParam[$propertiesName]);

                                       foreach($opt as $optKey=>$optRow){ 
//                                        
                                           //loop ulang per sub opt     
                                           $totalSelectedSubOpt = $arrParam['hidTotalOpt-'.$propertiesName.'-'.$optKey]; 
                                           $arrValueType[$optKey] = array_slice($arrParam[$propertiesName],0,$totalSelectedSubOpt); 
                                           $arrParam[$propertiesName] = array_slice($arrParam[$propertiesName],$totalSelectedSubOpt); 
                                       }
                                         
                                       $arrParam[$propertiesName] = json_encode(array('typekey'=> $typekey, 'value' => $arrValueType)); 
                                       break;    
                         
              }
		 
               
              
			  $sql = 'insert into 
			  			'.$this->tablePropertiesValue.' (refkey,widgetkey,userkey,value) values 
			  			('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($widgetkey).','.$this->oDbCon->paramString($this->userkey).','.$this->oDbCon->paramString($arrParam[$propertiesName]).')';
			   
			  $this->oDbCon->execute($sql);
               
		  }
		  
		  
          $this->oDbCon->endTrans();
 
        }catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}	 		
		
			
	}
}
?>
