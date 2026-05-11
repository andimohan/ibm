<?php
	
include '../../_config.php';  
$obj= $item;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Barang</title>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>scrollToTop.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>easing.css"/>  
 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-1.11.1.js"></script>     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrapValidator.js"></script>    
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.js"charset="utf-8"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery.formatCurrency-1.4.0.min.js" ></script>  
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-scrollToTop.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath.REPORT_JS; ?>"></script>
<?php

$criteria = '';
if(isset($_POST) && !empty($_POST['itemCode'])){
	$criteria .= ' AND item.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
}
if(isset($_POST) && !empty($_POST['itemName'])){
	$criteria .= ' AND item.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
}

if(isset($_POST) && !empty($_POST['selStatus'])){
	$criteria .= ' AND item.statuskey = '.$class->oDbCon->paramString($_POST['selStatus']);
} 
 
$arrWarehouse = $warehouse->searchData('','','','order by name asc'); 

$catCriteria = ' and statuskey = 1 and isleaf = 1 ';
if(isset($_POST) && !empty($_POST['selCategory'])){
	$catCriteria .= ' AND pkey = '.$class->oDbCon->paramString($_POST['selCategory']);
} 

$arrCategory = $class->convertForCombobox($itemCategory->searchData('statuskey',1,true, ' and isleaf = 1', ' order by name asc'),'pkey','name',' --- Pilih Kategori ---- ');   
$arrStatus = $class->convertForCombobox($item->getAllStatus(),'pkey','status',' --- Pilih Status ---- ');   

$orderbyCategory = 'order by name asc'; 
if (isset ($_POST) && !empty($_POST['selOrderBy']) && $_POST['selOrderBy'] == 'categoryname')
	$orderbyCategory  = 'order by name ' . $_POST['selOrderType'];
	
$rsCategory = $itemCategory->searchData('','','',$catCriteria,$orderbyCategory); 
 
$arrOrder['code'] = 'Kode';
$arrOrder['name'] = 'Nama';
$arrOrder['categoryname'] = 'Kategori';
$arrOrder['qty'] = 'QOH';
$arrOrder['statusname'] = 'Status';
 
$arrOrderType['asc'] = 'Ascending';
$arrOrderType['desc'] = 'Descending';


if (empty($_POST['selOrderBy'])){
	$_POST['selOrderBy'] = 'name';
	$_POST['selOrderType'] = 'asc';
}

$report = '';
	for($ctrCat=0;$ctrCat<count($rsCategory);$ctrCat++){   
			
			$tempreport = '';
			
			$sql = 'select
						'.$obj->tableName.'.pkey,
						'.$obj->tableName.'.code,
						'.$obj->tableName.'.name,
						'.$obj->tableName.'.categorykey,
						coalesce(sum(qtyinbaseunit),0) as qty,
						'.$obj->tableCategory.'.name as categoryname,
						'.$obj->tableStatus.'.status as statusname
				   from 
				   		'.$obj->tableName.' left join '.$obj->tableItemInWarehouse.' on '.$obj->tableName.'.pkey = '.$obj->tableItemInWarehouse.'.itemkey,
						'.$obj->tableCategory.',
						'.$obj->tableStatus.'
				   where 
				   	  '.$obj->tableCategory.'.pkey = '.$obj->tableName.'.categorykey and
				   	  '.$obj->tableStatus.'.pkey = '.$obj->tableName.'.statuskey and 
					  categorykey = '. $obj->oDbCon->paramString ($rsCategory[$ctrCat]['pkey']) .' 
					  '.$criteria .'
			      group by '.$obj->tableName.'.pkey ';
				  
			if (isset ($_POST) && !empty($_POST['selOrderBy']) )
						$sql .= 'order by '.$_POST['selOrderBy'].' ' . $_POST['selOrderType'];
		   
			
				
			$rs = $obj->oDbCon->doQuery($sql);	
			  
			if (empty($rs)) 
				continue; 
			 
			$tempreport .= '<div class="group-title">'. $rsCategory[$ctrCat]['name'].'</div>';
			$tempreport .= '<table class="main-table">';
			$tempreport .= '<tr class="table-header"> ';
			$tempreport .= '<td style="width:3em; text-align:right;">No.</td> ';
            $tempreport .= '<td style="width:6em">Kode</td>'; 
			$tempreport .= '<td>Nama</td>';  
			$tempreport .= '<td style="width:4em; text-align:right">QOH</td> '; 
		    $tempreport .= '<td style="width:5em;">Status</td>'; 
			$tempreport .= '</tr> '; 
			
			 $totalQty = 0;
			 $totalCogs  = 0;
			 $temp = 1;
			 for( $i=0;$i<count($rs);$i++) {   
			 
			 	$temptablerow = '';
			   
				
				$temptablerow  .= '<tr> ';
                $temptablerow  .= '<td style="text-align:right;">'.$temp.'.</td>';  
				$temptablerow  .= '<td>'.$rs[$i]['code'].'</td>'; 
                $temptablerow  .= '<td>'. $rs[$i]['name'].'</td>';  
			   
				$warehousekey = ''; 
				for($ctrWarehouse=0;$ctrWarehouse<count($arrWarehouse);$ctrWarehouse++){
					if (isset($_POST) && !empty($_POST['chkWarehouse'. $arrWarehouse[$ctrWarehouse]['pkey']])) {
						if ($warehousekey <> '')
							$warehousekey .= ',';
						$warehousekey .=  $arrWarehouse[$ctrWarehouse]['pkey'];
					}	
				}
				
				$total = $itemMovement->sumItemMovement($rs[$i]['pkey'], $warehousekey ) ; 
				$totalQty += $total;  
				 
				$temptablerow  .= '<td style="text-align:right;">'. $class->formatNumber($total).'</td>'; 
  				$temptablerow  .= '<td>'.$rs[$i]['statusname'].'</td>';
				$temptablerow  .= '</tr>';
    			
				$tempreport .= $temptablerow; 
			   
			    $temp++; 
			 }
			 
				$tempreport .= '<tr class="subtotal"> ';  
				$tempreport .= '<td colspan="3"></td>';  
                $tempreport .= '<td  style="text-align:right">'.$class->formatNumber($totalQty).'</td>'; 
		    	$tempreport .= '<td ></td>';
					  

         	 $tempreport .= '</tr> ';
    	 $tempreport .= '</table>';
		 $tempreport .= '<div style="clear:both; height:20px;"></div>';
		
		  
		 if ($tempreport)
				$report .= $tempreport;	
		 
			 	
	}
	 
?>
  
</head>
  
<body> 

<div class="report" >
    
    <div class="div-table report-table" > 
        <div class="div-table-row">
            <div class="div-table-col criteria-panel"> 
                <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                 <div class="title">Filter</div>
                 <div class="div-table"> 
                    <div class="form-group">
                        <label class="col-lg-3 control-label" >Kode</label>
                        <div class="col-lg-9">
                            <?php echo $class->input('text','itemCode'); ?>
                        </div>
                    </div>
                   <div class="form-group">
                        <label class="col-lg-3 control-label" >Nama</label>
                        <div class="col-lg-9">
                            <?php echo $class->input('text','itemName'); ?>
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="col-lg-3 control-label" >Kategori</label>
                        <div class="col-lg-9">
                            <?php echo  $class->inputSelect('selCategory', $arrCategory); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" >Status</label>
                        <div class="col-lg-9">
                            <?php echo  $class->inputSelect('selStatus', $arrStatus); ?>
                        </div>
                    </div>
                      <div class="form-group">
                        <label class="col-lg-3 control-label" >Gudang</label>
                        <div class="col-lg-9">
                            <ul>
                            <?php for($i=0;$i<count($arrWarehouse);$i++){ ?>
                              <li  style="width:180px;"><input  name="chkWarehouse<?php echo $arrWarehouse[$i]['pkey'] ?>" type="checkbox" value="1" <?php if(isset($_POST) && !empty($_POST['chkWarehouse'.$arrWarehouse[$i]['pkey']])) echo 'checked="checked"'; ?> /> <?php echo $arrWarehouse[$i]['name'] ?></li>
                            <?php } ?>
                            </ul>
                        </div>
                    </div>
                    
                 </div>	 
                 
                 
                 <div class="title">Order</div>
                 <div style="width:50%; float:left"><?php echo  $class->inputSelect('selOrderBy', $arrOrder); ?></div>
                 <div style="width:50%; float:left"><?php echo  $class->inputSelect('selOrderType', $arrOrderType); ?></div>
                 <div style="clear:both; height:2em"></div> 
                 <?php echo $class->input('submit','btnSubmit',true,'Submit', ' style="width:100%;"' ); ?>
                 </form>
                 
            </div>
            <div class="div-table-col data-panel">
            	<ul class="menu">
                <li class="toogle-criteria">Toggle Filter</li>
                <li>Print</li>
                </ul>
                <div style="clear:both"></div>
                <div class="title">Laporan Item</div>
                 <?php if (empty($report)) echo ' - data tidak tersedia - '; else echo $report; ?> 
            </div>
        </div>
    </div> 
      
	 
</div>
<div id="back-to-top"></div>
</body>

</html>

