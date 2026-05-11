<?php

class CarCategory extends Category{
 
   function __construct(){
		
      parent::__construct();

      $this->tableName = 'car_category'; 
      $this->tableItemPositionDetail = 'car_category_detail_item_position';
      $this->tableSparePartIntervalDetail = 'car_category_sparepart_type_interval_detail';
      $this->tableSparePartType = 'sparepart_type';
      $this->tableItemPosition = 'item_position'; 
      $this->tableSparePartTypeAccess = 'car_category_sparepart_type_access';
      $this->securityObject = 'CarCategory'; 

      $this->activeModule = $this->isActiveModule(array('CarServiceMaintenance'));
         
        $arrDetails = array(); 
        if($this->activeModule['carservicemaintenance']){
            $this->arrSparePartIntervalDetail = array();
            $this->arrSparePartIntervalDetail['pkey'] = array('hidDetailSparePartIntervalKey');
            $this->arrSparePartIntervalDetail['refkey'] = array('pkey','ref'); 
            $this->arrSparePartIntervalDetail['spareparttypekey'] = array('hidIntervalSparepartTypeKey');
            $this->arrSparePartIntervalDetail['mileage'] = array('mileage','number'); 
            $this->arrSparePartIntervalDetail['month'] = array('month','number'); 

            $this->arrItemPositionDetail = array();
            $this->arrItemPositionDetail['pkey'] = array('hidDetailItemPositionKey');
            $this->arrItemPositionDetail['refkey'] = array('pkey','ref'); 
            $this->arrItemPositionDetail['spareparttypekey'] = array('hidSparePartTypeKey');
            $this->arrItemPositionDetail['itempositionkey'] = array('selItemPosition'); 

            array_push($arrDetails, array('dataset' => $this->arrItemPositionDetail, 'tableName' => $this->tableItemPositionDetail)); 
            array_push($arrDetails, array('dataset' => $this->arrSparePartIntervalDetail, 'tableName' => $this->tableSparePartIntervalDetail));  
              
        }
         
  
      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey', array('dataDetail' =>  $arrDetails));
      $this->arrData['code'] = array('code'); 
      $this->arrData['name'] = array('name');
      $this->arrData['orderlist'] = array('orderList', 'number');
      $this->arrData['parentkey'] = array('selCategory');
      $this->arrData['isleaf'] = array('isLeaf'); 
      $this->arrData['file'] = array('fileName');
      $this->arrData['statuskey'] = array('selStatus');
      $this->arrData['shortdescription'] = array('trShortDesc'); 
      $this->arrData['description'] = array('txtDescription','raw');

      $this->arrLockedTable = array();
      $defaultFieldName = 'categorykey'; 
      array_push($this->arrLockedTable, array('table'=>'car','field'=>$defaultFieldName)); 


      $this->arrDataListAvailableColumn = array(); 
      array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

         
      $this->includeClassDependencies(array(
            'Category.class.php',
            'ItemPosition.class.php' 
      ));

      $this->overwriteConfig();
   }
 
      function afterUpdateData($arrParam, $action){
            $this->updateOrder ($arrParam['orderList'],$arrParam['pkey']); 
            $this->updateLeaf();
      }

      function getSparePartTypeAccess($pkey = '', $spareparttypekey = '')
      {
            //$sql = '
            //      select
            //            '.$this->tableSparePartTypeAccess.'.*,
            //            '.$this->tableSparePartType.'.name as spareparttypename
            //      from
            //            '. $this->tableSparePartTypeAccess.',
            //            '.$this->tableSparePartType.'
            //      where
            //            '.$this->tableSparePartTypeAccess.'.spareparttypekey = '.$this->tableSparePartType.'.pkey
            //';
//
            //if (!empty($pkey)) {
            //      $sql .= ' and ' . $this->tableSparePartTypeAccess . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
            //}
//
            //if(!empty($spareparttypekey)){
            //      $sql .= ' and ' . $this->tableSparePartTypeAccess .'.spareparttypekey in (' . $this->oDbCon->paramString($spareparttypekey,',').') ';
            //}
//
            //$this->setLog($sql,true);
            //return $this->oDbCon->doQuery($sql);
      }


      function getItemPosition($carcategorykey, $spareparttypekey = '')
      {
            $sql = 'select 
                        '. $this->tableItemPositionDetail. '.*,
                        '. $this->tableSparePartType.'.name as spareparttypename,
                        ' . $this->tableItemPosition .'.name as positioname
                  from 
                        ' . $this->tableItemPositionDetail. ',
                        ' . $this->tableItemPosition. ',
                        ' . $this->tableSparePartType. '
                  where 
                        ' . $this->tableItemPositionDetail .'.spareparttypekey = ' . $this->tableSparePartType .'.pkey and
                        ' . $this->tableItemPositionDetail .'.itempositionkey = ' . $this->tableItemPosition .'.pkey and
                        ' . $this->tableItemPosition .'.statuskey = 1 and
                        '. $this->tableItemPositionDetail.'.refkey in (' . $this->oDbCon->paramString($carcategorykey,',').')
            ';

            if(!empty($spareparttypekey)){
                  $sql .= ' and ' . $this->tableItemPositionDetail .'.spareparttypekey in (' . $this->oDbCon->paramString($spareparttypekey,',').') ';
            }

            $sql .= 'order by  ' . $this->tableItemPosition .'.pkey asc';
            
            $rs = $this->oDbCon->doQuery($sql);
      
            return $rs;
      }


      function getSparePartIntervalDetail($carcategorykey, $spareparttypekey = '')
      {
            $sql = 'select 
                        '. $this->tableSparePartIntervalDetail. '.*,
                        '. $this->tableSparePartType.'.name as spareparttypename
                  from 
                        ' . $this->tableSparePartIntervalDetail. ',
                        ' . $this->tableSparePartType. '
                  where 
                        ' . $this->tableSparePartIntervalDetail .'.spareparttypekey = ' . $this->tableSparePartType .'.pkey  and
                        '. $this->tableSparePartIntervalDetail.'.refkey in (' . $this->oDbCon->paramString($carcategorykey,',').')
            ';

            if(!empty($spareparttypekey)){
                  $sql .= ' and ' . $this->tableSparePartIntervalDetail .'.spareparttypekey in (' . $this->oDbCon->paramString($spareparttypekey,',').') ';
            }
            
            $rs = $this->oDbCon->doQuery($sql); 
      
            return $rs;
      }
      


      function normalizeParameter($arrParam, $trim=false){
            
            if($this->activeModule['carservicemaintenance']){
                  
                  if(isset($arrParam['hidSparepartTypeKey'])) {
                        //position
                        $detailKeyList = [];
                        $typeKeyList   = [];
                        $positionKeyList    = [];

                        foreach ($arrParam['hidSparepartTypeKey'] as $i => $typeKey) {

                              $detailItemPositionKeys        = $arrParam['hidDetailItemPositionKey_' . $typeKey][0] ?? '';
                              $detailKeys = explode(',', $detailItemPositionKeys);
                              $positionKeys  = $arrParam['selItemPosition_' . $typeKey] ?? [];

                              foreach ($positionKeys as $index => $positionRow) {
                                    $positionKeyList[] = $positionRow;
                                    $typeKeyList[] = $typeKey;
                                    $detailKeyList[] = $detailKeys[$index] ?? '';
                              }
                        }
                        $arrParam['selItemPosition']         = $positionKeyList;
                        $arrParam['hidDetailItemPositionKey'] = $detailKeyList;
                        $arrParam['hidSparePartTypeKey']     = $typeKeyList;
                  }
                  
                  if(isset($arrParam['hidIntervalSparepartTypeKey'])) {
                  //interval
                   foreach ($arrParam['hidIntervalSparepartTypeKey'] as $i => $typeKey) {

                        $arrParam['hidIntervalSparepartTypeKey'][$i] = $typeKey;

                        // Assign mileage & month, ambil index 0 karena form input hanya 1 per type
                        $arrParam['mileage'][$i] = $arrParam['mileage_'.$typeKey][0] ?? null;
                        $arrParam['month'][$i]   = $arrParam['month_'.$typeKey][0] ?? null;

                        $arrParam['hidDetailSparePartIntervalKey'][$i] = $arrParam['hidDetailSparePartIntervalKey_'.$typeKey][0] ?? null;
                  }
            }


            }
            

            $arrParam = parent::normalizeParameter($arrParam,true); 
            return $arrParam; 

      }

}

?>
