<?php 
$PRINT_SETTINGS =  array(   
    'showPrintHeader' => true,
    'showPrintFooter' => false,
);
// includeClass(array('Car.class.php','CarCategory.class.php','Item.class.php'));
$car = createObjAndAddToCol(new Car()); 
$carServiceMaintenance = createObjAndAddToCol(new CarServiceMaintenance());
$carCategory = createObjAndAddToCol(new CarCategory());
$item = createObjAndAddToCol(new Item());

$obj = $car;
$generateReportContent = function ($dataset) {

    $obj = new Car();
    $item = new Item();

    $rs = $dataset['rs'];   
    
    $rsSparePartType = $item->getSparePartType();
    $obj->setLog($rsSparePartType, true);
    $rsItemCar = $obj->getCarItemDetailForPrint($rs[0]['pkey']);
    $rsItemCar = $obj->reindexDetailCollections($rsItemCar,'spareparttypekey');
    $obj->setLog($rsItemCar, true);

    $html = $obj->printSetting['defaultStyle'];

    $html .= ' 
    <table cellpadding="2" > 
        <tr><td><div class="title">' . $obj->lang['carItem'] . '</div></td></tr>
        <tr><td><div class="subtitle">' . $rs[0]['codepolicenumber'] . '</div></td></tr>
    </table> 
    <div style="clear:both"></div>
    ';

    $html .= '<table cellpadding="2">
                <tr>
                    <td style="width:300px;" ><table cellpadding="2">
                            <tr><td class="header-row-header" style="width:120px">'.$obj->lang['category'].'</td><td style="width:10px; text-align:center">:</td><td style="width:170px">' . $rs[0]['categoryname'] . '</td></tr>
                        </table>
                    </td>
                    <td style="width:370px;"> 
                        
                    </td>
                </tr>
            </table>';


    $html .= '<div style="clear:both"></div>';

    $html .= '<table cellpadding="4" class="table-transaction">
                <tr class="col-header">
                    <td style="text-align:center;width:120px">'.$obj->lang['date'].'</td>
                    <td style="text-align:left;width:230px">'.$obj->lang['itemName'].'</td>
                    <td style="text-align:left;width:160px">'.$obj->lang['serialNumber'] .'</td>
                    <td style="text-align:left;width:170px">'. $obj->lang['partsPosition'].'</td>
                </tr>';

    for ($i = 0; $i < count($rsSparePartType); $i++) {
        
        if(!isset($rsItemCar[$rsSparePartType[$i]['pkey']])) continue;

        $html .= '<tr>
                    <td colspan="7" style="background-color:#f0f0f0; font-weight:bold; padding-left:5px;">' . $rsSparePartType[$i]['name'] . '</td>
                </tr>';
        
        $rsItemCarCol = $rsItemCar[$rsSparePartType[$i]['pkey']];
                
        foreach($rsItemCarCol as $itemRow){

            $html .= '<tr>
                <td style="text-align:center;">' . $obj->formatDBDate($itemRow['trdate'], 'd / m / Y') . '</td>
                <td>' . $itemRow['itemname'] . '</td>
                <td>' . $itemRow['serialnumber'] . '</td>
                <td>' . $itemRow['positionname'] . '</td>
            </tr>';

        }

    }

    $html .= '</table>  
    <div style="clear:both"></div> ';

    return $html;

};
?>