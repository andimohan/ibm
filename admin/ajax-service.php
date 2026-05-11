<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Service.class.php');
$service = createObjAndAddToCol(new Service());

$obj = $service;

//include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

        case 'searchData':
            $order = 'order by item.name asc';
            $term = '';
            $criteria = '';

            $arrCriteria = array();

            if (isset($_GET) && !empty($_GET['term'])) {
                $term = $_GET['term'];

                $criteria = $obj->tableName . '.name like ' . $obj->oDbCon->paramString('%' . $term . '%') . ' or 
									 			' . $obj->tableName . '.code like ' . $obj->oDbCon->paramString('%' . $term . '%');
                array_push($arrCriteria, '(' . $criteria . ')');
            }

            if (isset($_GET['pkey'])) {
                $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                array_push($arrCriteria, $obj->tableName . '.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']));
            }

            if (isset($_GET['itemtype'])) {
                $_GET['itemtype'] = (empty($_GET['itemtype'])) ? 1 : $_GET['itemtype'];
                $itemtype = explode(',', $_GET['itemtype']);
                array_push($arrCriteria, $obj->tableName . '.itemtype in (' . $obj->oDbCon->paramString($itemtype, ',') . ')');
            }


            // hanya ambil item yg aktif
            array_push($arrCriteria,  $obj->tableName . '.statuskey = 1 ');

            $criteria = implode(' and ', $arrCriteria);

            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            if (isset($_GET['limit']) && !empty($_GET['limit']) && is_numeric($_GET['limit']))
                $order .= ' limit ' . $_GET['limit'];

            $rsData = $obj->searchDataForAutoComplete('', '', false, $criteria, $order);

            echo json_encode($rsData);
            break;
          
        case 'getDetailArea':

            if (!isset($_GET['pkey'])) die;
            $pkey = $_GET['pkey'];
            $cityCategoryKey = $_GET['cityCategoryKey'];

            $rsData = $obj->getDetailArea($pkey, $cityCategoryKey);

            echo json_encode($rsData);
            break;
        case 'getDetailWaste':

            if (!isset($_GET['pkey'])) die;
            $pkey = $_GET['pkey'];
            $wasteCategoryKey = $_GET['wasteCategoryKey'];

            $arrCriteria = array();
            if (isset($_GET['term']) && !empty($_GET['term'])) {
                array_push($arrCriteria, '(' . $obj->tableWaste . '.code like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ' or ' . $obj->tableWaste . '.name like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ')');
            }
            $criteria = implode(' and ', $arrCriteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';


            $rsData = $obj->getDetailWaste($pkey, 'refkey', null, $wasteCategoryKey, $criteria);
            for($i=0;$i<count($rsData);$i++){
                $rsData[$i]['pkey'] = $rsData[$i]['wastekey'];
                $rsData[$i]['value'] = $rsData[$i]['wastecodename'];
            }

            echo json_encode($rsData);
            break;

            break;

        case 'getAssetGroupDetail':
            if (!isset($_GET['pkey'])) die;
            $pkey = $_GET['pkey'];
            $rsData = $obj->getAssetGroupDetail($pkey);
            echo json_encode($rsData);
            break;
        case 'getItemDetail':
            if (!isset($_GET['pkey'])) die;
            $pkey = $_GET['pkey'];
            $rsData = $obj->getItemDetail($pkey);
            echo json_encode($rsData);
            break;
    }
}
