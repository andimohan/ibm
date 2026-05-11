<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 

$rsBrand = $brand->searchData($brand->tableName.'.statuskey',1);
$rsCategory = $itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true,'',' order by name asc');

foreach($rsBrand as $brandRow){
    echo '<b>'.strtoupper($brandRow['name']).'</b><br>';
    echo '<table cellspacing="0" style="font-size: 0.9em" >';
    echo '<tr>
        <td style="border:1px solid #dedede; padding:0.2em 0.5em; width: 250px; font-weight:bold;">Kategori</td>
        <td style="border:1px solid #dedede; padding:0.2em 0.5em; width: 120px; text-align:right; font-weight:bold;">Modal Min</td>
        <td style="border:1px solid #dedede; padding:0.2em 0.5em; width: 120px; text-align:right; font-weight:bold;">Modal Max</td>
        <td style="border:1px solid #dedede; padding:0.2em 0.5em; width: 120px; text-align:right; font-weight:bold;">Harga Jual Min</td>
        <td style="border:1px solid #dedede; padding:0.2em 0.5em; width: 120px; text-align:right; font-weight:bold;">Harga Jual Max</td>
      </tr>';

/*          <td style="border:1px solid #dedede; padding:0.2em 0.5em; width: 300px; font-weight:bold;">Catatan</td>
        <td style="border:1px solid #dedede; padding:0.2em 0.5em; width: 300px; font-weight:bold;">Catatan</td>*/
              
    foreach($rsCategory as $categoryRow){
        $minCOGS = 0;
        $maxCOGS = 0;
        $minSellingPrice = 0;
        $maxSellingPrice = 0;
        $arrMinItem = array();
        $arrMaxItem = array();
        
        $brandAndCategoryCriteria = ' and '.$item->tableName.'.brandkey = '.$brandRow['pkey'].' and '.$item->tableName.'.categorykey = '.$categoryRow['pkey'];
        
        $sql = 'select
                    coalesce(min(cogs),0) as mincogs, 
                    coalesce(max(cogs),0) as maxcogs, 
                    coalesce(min(sellingprice),0) as minsellingprice, 
                    coalesce(max(sellingprice),0) as maxsellingprice 
                from 
                    '.$item->tableName.' 
                where 
                    '.$item->tableName.'.statuskey = 1
                ';
        $sql .= $brandAndCategoryCriteria;
        
        $rsItem = $class->oDbCon->doQuery($sql);
        
        $minCOGS = $rsItem[0]['mincogs'];
        $maxCOGS = $rsItem[0]['maxcogs'];
        $minSellingPrice = $rsItem[0]['minsellingprice'];
        $maxSellingPrice = $rsItem[0]['maxsellingprice'];
        
        if($minSellingPrice != $maxSellingPrice){
                $sql = ' select concat(name,\' - \',sellingprice) as nameprice from  '.$item->tableName.' where statuskey = 1 and sellingprice = ' . $minSellingPrice;
                $sql .= $brandAndCategoryCriteria;

                $rsMinItem = $class->oDbCon->doQuery($sql);
                $arrMinItem = array_column($rsMinItem,'nameprice');

                $sql = ' select concat(name,\' - \',sellingprice) as nameprice from  '.$item->tableName.' where statuskey = 1 and sellingprice = ' . $maxSellingPrice;
                $sql .= $brandAndCategoryCriteria;

                $rsMaxItem = $class->oDbCon->doQuery($sql);
                $arrMaxItem = array_column($rsMaxItem,'nameprice');
        }
        
        //if($minSellingPrice == 0 && $maxSellingPrice ==0) continue;
        
                /*<td style="border:1px solid #dedede; padding:0.2em 0.5em; vertical-align:top;">'.implode('<br>',$arrMinItem).'</td>
                <td style="border:1px solid #dedede; padding:0.2em 0.5em; vertical-align:top;">'.implode('<br>',$arrMaxItem).'</td>*/
        
        echo '<tr>
                <td style="border:1px solid #dedede; padding:0.2em 0.5em; vertical-align:top;">'.$categoryRow['name'].'</td>
                <td style="border:1px solid #dedede; padding:0.2em 0.5em; text-align:right;  vertical-align:top;">'.$class->formatNumber($minCOGS).'</td>
                <td style="border:1px solid #dedede; padding:0.2em 0.5em; text-align:right;  vertical-align:top;">'.$class->formatNumber($maxCOGS).'</td>
                <td style="border:1px solid #dedede; padding:0.2em 0.5em; text-align:right;  vertical-align:top;">'.$class->formatNumber($minSellingPrice).'</td>
                <td style="border:1px solid #dedede; padding:0.2em 0.5em; text-align:right;  vertical-align:top;">'.$class->formatNumber($maxSellingPrice).'</td>
              </tr>';
    }
    echo '</table>';
    echo '<br>';
}


$class->oDbCon->endTrans();
 
?>