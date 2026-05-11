<?php

function customGenerateQuickView($obj, $id)
{
    $item = new Item();

    $detail = '';
    $rs = $obj->searchData($obj->tableName . '.pkey', $id, true);
    $rsDetail = $obj->getDetailWithRelatedInformation($id);

    $discount = $rs[0]['finaldiscount'];
    $discountType = $rs[0]['finaldiscounttype'];
    $subtotal = $rs[0]['subtotal'];

    $discountValue = ($discount != 0 && $discountType == 2) ? $discount / 100 * $subtotal : $discount;
    $rs[0]['finaldiscount'] = $discountValue;


    $basicInformation = ' <div class="data-card border-orange">
						<h1>' . ucwords($obj->lang['generalInformation']) . '</h1> 
						<div class="content">
						<div class="div-table general-information-table">
							<div class="div-table-row">
								<div class="div-table-col " style="width:50%">' . ucwords($obj->lang['status']) . '</div> 
								<div class="div-table-col">' . $rs[0]['statusname'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col ">' . ucwords($obj->lang['code']) . '</div> 
								<div class="div-table-col">' . $rs[0]['code'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['date']) . '</div> 
								<div class="div-table-col">' . $obj->formatDBDate($rs[0]['trdate']) . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['warehouse']) . '</div> 
								<div class="div-table-col">' . $rs[0]['warehousename'] . '</div> 
							</div>
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['supplier']) . '</div> 
								<div class="div-table-col">' . $rs[0]['suppliername'] . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['subtotal']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['subtotal']) . '</div> 
							</div> 
							
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['beforeTax']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['beforetaxtotal']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['tax']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['taxvalue']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['shippingFee']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['shipmentfee']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['others']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['etccost']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['total']) . '</div> 
								<div class="div-table-col">' . $obj->formatNumber($rs[0]['grandtotal']) . '</div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col" style="height:1em"></div> 
								<div class="div-table-col"></div> 
							</div> 
							<div class="div-table-row">
								<div class="div-table-col">' . ucwords($obj->lang['note']) . '</div> 
								<div class="div-table-col">' . $rs[0]['trdesc'] . '</div> 
							</div> 
						</div>
						</div>
					</div>  
		';

    $detailInformation = ' <div class="data-card border-green">
            <h1>' . ucwords($obj->lang['itemDetail']) . '</h1> 
            <div class="content">
            <div class="div-table quick-view-table" >
                  <div class="div-table-row"> 
                        <div class="div-table-col detail-col-header">' . ucwords($obj->lang['itemName']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">' . ucwords($obj->lang['qty']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:60px;">' . ucwords($obj->lang['unit']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">' . ucwords($obj->lang['qty']) . ' (PCS)</div>
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['receivedQty']) . '</div>
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['receivedQty']) . ' (PCS)</div>
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['price']) . ' @</div> 
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">' . ucwords($obj->lang['subtotal']) . '</div> 
                </div>';

    for ($i = 0; $i < count($rsDetail); $i++) {

        $discount = $rsDetail[$i]['discount'];
        $discountType = $rsDetail[$i]['discounttype'];
        $priceInUnit = $rsDetail[$i]['priceinunit'];

        $discountValue = ($discount != 0 && $discountType == 2) ? $discount / 100 * $priceInUnit : $discount;
        $rsDetail[$i]['discount'] = $discountValue;

        $receivedQty = $item->splitQtyBaseOnUnit($rsDetail[$i]['itemkey'], $rsDetail[$i]['receivedqtyinbaseunit']);

        $detailInformation .= '
				<div class="div-table-row"> 
					<div class="div-table-col">' . $rsDetail[$i]['itemname'] . '</div>
					<div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['qty']) . '</div>
                    <div class="div-table-col">' . $rsDetail[$i]['unitname'] . '</div>
                    <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['qtyinpcs']) . '</div>
                    <div class="div-table-col" style="text-align:right;">' . $receivedQty . '</div>
                    <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['receivedqtyinpcs']) . '</div>
                    <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['priceinunit']) . '</div> 
                    <div class="div-table-col" style="text-align:right;">' . $obj->formatNumber($rsDetail[$i]['total']) . '</div> 
                </div>';
    }

    $detailInformation .= ' </div>
						</div>
					</div>  
		';

    $detail .= '<div class="div-table" style="width:100%; ">
							<div class="div-table-row">
								<div class="div-table-col-5"  style="width:25%; text-align:center;">
								' . $basicInformation . '
								</div> 
								<div class="div-table-col-5"  style="text-align:center; ">
								 ' . $detailInformation . '
								</div>  
							</div>
					</div>';

    $detail .= '<div style="clear:both;"></div>';


    return $detail;
}

?>