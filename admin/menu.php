<?php  
    
    // DEFINE ALL ACCESSIBLE MENU
    
    $rsAccessibleMenu = $security->getAllAccessibleMenu();
    define('ACCESSIBLE_MENU', array_column($rsAccessibleMenu,'modulecode'));

    function pushMenuItem(&$arrMenuItem,$newMenuItem, $categoryType = array()){
        global $security; 
          
        if (!empty($categoryType)){
            if (!in_array(PLAN_TYPE['categorykey'],$categoryType ))
                return ;
        }
             
         //$userkey =  base64_decode($_SESSION[$security->loginAdminSession]['id']); 
      
         if ( isset($newMenuItem['menu']) && count($newMenuItem['menu'][0]) == 0)  
             return;
        
         /*if ( !empty($newMenuItem['securityObject']) &&  !$security->hasSecurityAccess( $userkey ,$security->getSecurityKey($newMenuItem['securityObject']),10) ) 
                 return;*/
        if ( !empty($newMenuItem['securityObject']) &&  !in_array(strtolower($newMenuItem['securityObject']),ACCESSIBLE_MENU) ) 
                 return;
     
         array_push($arrMenuItem ,$newMenuItem);
        
    }

    function buildMenu($arrMenu,$parent = '' ){ 
          
    $menu = ''; 
	        
            foreach ($arrMenu as $key=>$menuItem) {  
                $class = "submenu";
                
                if (empty($parent))
                        $class="root";
                else if (isset($menuItem['menu']))
                         $class .= " submenu-header";
                
                $icon = '';
                if (!empty($menuItem['icon']))
                    $icon = '<div class="'.$menuItem['icon'].' icon"></div>';
                
                if (!empty($menuItem['phplist'])){
                    $menu .= '<li class="'.$class.' menu-child clickable" rel="'.$key.'" reladdr="'.$menuItem['phplist'].'" reltarget="'.$menuItem['target'].'">'.$icon.$menuItem['label'].'</li>';
                }else {
                    $menu .= '<li class="'.$class.' clickable" rel="'.$key.'">'
                          .$icon.'<span class="menu-label">'.$menuItem['label'].'</span>';
                
                    // Jika memiliki submenu, dan ini adalah menu utama (bukan submenu child)
                    if (isset($menuItem['menu'])) {
                        if (empty($parent)) {
                            $menu .= '<ul class="submenu-panel-'.$key.' submenu-panel ui-widget">';
                        }
                
                        foreach ($menuItem['menu'] as $menuItemRow) {
                            $menu .= buildMenu($menuItemRow, $menuItem); 
                        }
                
                        if (empty($parent)) {
                            $menu .= '</ul>';
                        }
                    }
                
                    $menu .= '</li>';
                } 
                 
            }
             

            if (empty($parent)) 
                $menu  .= '<div class="main-menu-closer"></div>';

            return $menu;
    }

	$menu = '';	 

	// kedepan bisa munculin
	$arrActiveModule = $class->isActiveModule(array('EMKLJobOrder'));
	$showEximServices = $class->loadSetting('splitCOAByJobCategory');
	
 
    $arrMenu = array(); 
  
    // BUSINESS PARTNER 
    $arrBusinessPartner = array ('label' => $class->lang['businessPartner'], 'icon' => 'fas fa-users' );  
    $arrBusinessPartner['menu'] = array(); 

	$menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['supplier'],   'securityObject' => 'Supplier',   'phplist' => 'supplierList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['supplierCategory'],   'securityObject' => 'SupplierCategory',   'phplist' => 'supplierCategoryList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['customer'] ,   'securityObject' => 'Customer',   'phplist' => 'customerList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['policy'] ,   'securityObject' => 'CustomerInsurancePolicy',   'phplist' => 'customerInsurancePolicyList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['customerCategory'],   'securityObject' => 'CustomerCategory',   'phplist' => 'customerCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['consignee'],   'securityObject' => 'Consignee',   'phplist' => 'consigneeList', 'target' => 'tab' ), array(2,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['employee'],   'securityObject' => 'Employee',   'phplist' => 'employeeList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['employeeDivision'],   'securityObject' => 'EmployeeCategory',   'phplist' => 'employeeCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['subsidiaries'],   'securityObject' => 'Subsidiaries',   'phplist' => 'subsidiariesList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['buildingUnit'],   'securityObject' => 'BuildingUnit',   'phplist' => 'buildingUnitList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['PICGroup'],   'securityObject' => 'PersonInChargeGroup',   'phplist' => 'personInChargeGroupList', 'target' => 'tab' ));

    // biasanya utk front end
    pushMenuItem($menuitem , array ('label' => $class->lang['managementStructure'],   'securityObject' => 'ManagementStructure',   'phplist' => 'managementStructureList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['managementTeam'],   'securityObject' => 'ManagementTeam',   'phplist' => 'managementTeamList', 'target' => 'tab' ));

    if (PLAN_TYPE['usefrontend'] == 1){
         pushMenuItem($menuitem , array ('label' => $class->lang['partners'],   'securityObject' => 'Partners',   'phplist' => 'partnersList', 'target' => 'tab' ));
         pushMenuItem($menuitem , array ('label' => $class->lang['partnersCategory'],   'securityObject' => 'PartnersCategory',   'phplist' => 'partnersCategoryList', 'target' => 'tab' ));
    }
    pushMenuItem($menuitem , array ('label' => $class->lang['jobPosition'],   'securityObject' => 'JobPosition',   'phplist' => 'jobPositionList', 'target' => 'tab' ));

   // pushMenuItem($menuitem , array ('label' => $class->lang['company'],   'securityObject' => $company->securityObject,   'phplist' => 'companyList', 'target' => 'tab' ));

    $arrSubMenu = array ('label' => $class->lang['company']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();

    pushMenuItem($submenuitem , array ('label' => $class->lang['company'],   'securityObject' => 'Company',   'phplist' => 'companyList', 'target' => 'tab' )); 
    pushMenuItem($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu);    


    // OTHERS
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['templateCustomer'],   'securityObject' => 'Customer',   'phplist' => 'templateCustomerList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['templateSupplier'],   'securityObject' => 'Supplier',   'phplist' => 'templateSupplierList', 'target' => 'tab' ));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['businessCategory'],   'securityObject' => 'BusinessCategory',   'phplist' => 'businessCategoryList', 'target' => 'tab' ));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['businessCategorySuggestion'],   'securityObject' => 'BusinessCategorySuggestion',  'phplist' => 'businessCategorySuggestionList', 'target' => 'tab' ),array(1));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu);  
    $arrPurchase['menu'] = array();

    pushMenuItem ($arrBusinessPartner['menu'], $menuitem);    
    pushMenuItem ($arrMenu, $arrBusinessPartner); 
  

    // MEMBERSHIP 
    $arrMembership = array ('label' => $class->lang['membership'], 'icon' => 'fas fa-user-tag' );  
    $arrMembership['menu'] = array(); 

	$menuitem = array(); 
	pushMenuItem($menuitem , array ('label' => $class->lang['membershipRegistration'],   'securityObject' => 'MembershipSubscription',   'phplist' => 'membershipSubscriptionList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['membershipType'],   'securityObject' => 'MembershipLevel',   'phplist' => 'membershipLevelList', 'target' => 'tab' ));
//    pushMenuItem($menuitem , array ('label' => $class->lang['membershipType'],   'securityObject' => 'Membership',   'phplist' => 'membershipList', 'target' => 'tab' ));
//    pushMenuItem($menuitem , array ('label' => $class->lang['membershipAttendance'],   'securityObject' => 'MembershipAttendance',   'phplist' => 'membershipAttendanceList', 'target' => 'tab' ));
    
  
    pushMenuItem ($arrMembership['menu'], $menuitem);    
    pushMenuItem ($arrMenu, $arrMembership); 
  
 
    // INVENTORY
    $arrInventory = array ('label' => $class->lang['productAndService'], 'icon' => 'fas fa-box');  

    $arrInventory['menu'] = array(); 
 
    $menuitem = array();

    $arrSubMenu = array ('label' => $class->lang['productManagement']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemList'],   'securityObject' => 'Item',   'phplist' => 'itemList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemDepotList'],   'securityObject' => 'ItemDepot',   'phplist' => 'itemDepotList', 'target' => 'tab' ), array(2));
   
    //pushMenuItem($submenuitem , array ('label' => $class->lang['itemList'],   'securityObject' => $item->securityObject,   'phplist' => 'pawnItemList', 'target' => 'tab' ), array(4));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemPackage'],   'securityObject' => 'ItemPackage',   'phplist' => 'itemPackageList', 'target' => 'tab' ), array(1,3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemCategory'],   'securityObject' => 'ItemCategory',   'phplist' => 'itemCategoryList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemUnit'],   'securityObject' => 'ItemUnit',   'phplist' => 'itemUnitList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['timeUnit'],   'securityObject' => 'TimeUnit',   'phplist' => 'timeUnitList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemCondition'],   'securityObject' => 'ItemCondition',   'phplist' => 'itemConditionList', 'target' => 'tab' ), array(1,2,3,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemConversion'],   'securityObject' => 'ItemConversion',   'phplist' => 'itemConversionList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemProportional'],   'securityObject' => 'ItemProportional',   'phplist' => 'itemProportionalList', 'target' => 'tab' ));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);  

    
    $arrSubMenu = array ('label' => $class->lang['unit']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['assetItemList'],   'securityObject' => 'AssetItem',   'phplist' => 'assetItemList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['categoryAssetItem'],   'securityObject' => 'CategoryAssetItem',   'phplist' => 'categoryAssetItemList', 'target' => 'tab' ));
    // pushMenuItem($submenuitem , array ('label' => $class->lang['cogsAdjustment'],   'securityObject' => 'AssetItemCOGSAdjustment',   'phplist' => 'assetItemCOGSAdjustmentList', 'target' => 'tab' ));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 



    $arrSubMenu = array ('label' => $class->lang['serviceManagement']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceList'],   'securityObject' => 'Service',   'phplist' => 'serviceList', 'target' => 'tab' ));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['workshopServiceList'],   'securityObject' => $service->securityObject,   'phplist' => 'workshopServiceList', 'target' => 'tab' ), array(3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingServiceList'],   'securityObject' => 'TruckingService',   'phplist' => 'truckingServiceList', 'target' => 'tab' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostList'],   'securityObject' => 'TruckingCost',   'phplist' => 'truckingCostList', 'target' => 'tab' ), array(2));
    // sama dengan yg bawahnya, cuma beda bahasa
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceCategory'],   'securityObject' => 'ServiceCategory',   'phplist' => 'serviceCategoryList', 'target' => 'tab' ), array(1,3,4,6,7,8));
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceAndCostCategory'],   'securityObject' => 'ServiceCategory',   'phplist' => 'serviceCategoryList', 'target' => 'tab' ), array(2,5));

    pushMenuItem($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu);  

    // SUB MOVEMENT
     $arrSubMenu = array ('label' => $class->lang['itemMovement']);  
     $submenuitem = array();
     $arrSubMenu['menu'] = array();
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemIn'],   'securityObject' => 'ItemIn',   'phplist' => 'itemInList', 'target' => 'tab' ));
     pushMenuItem($submenuitem, array('label' => $class->lang['itemReceiving'],   'securityObject' => 'ItemReceiving',   'phplist' => 'itemReceivingList', 'target' => 'tab'));
     pushMenuItem($submenuitem, array('label' => $class->lang['putAway'],   'securityObject' => 'PutAway',   'phplist' => 'putAwayList', 'target' => 'tab'));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemInReceive'],   'securityObject' => 'ItemInReceive',   'phplist' => 'itemInReceiveList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemOut'],   'securityObject' => 'ItemOut',   'phplist' => 'itemOutList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemOutDelivery'],   'securityObject' => 'ItemOutDelivery',   'phplist' => 'itemOutDeliveryList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemAdjustment'],   'securityObject' => 'ItemAdjustment',   'phplist' => 'itemAdjustmentList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['warehouseTransfer'],   'securityObject' => 'WarehouseTransfer',   'phplist' => 'warehouseTransferList', 'target' => 'tab' ));
     pushMenuItem($arrSubMenu['menu'], $submenuitem);  
     pushMenuItem($menuitem , $arrSubMenu);  


     // OTHERS
     $arrSubMenu = array ('label' => $class->lang['others']);  
     $submenuitem = array();
     $arrSubMenu['menu'] = array();
     pushMenuItem($submenuitem , array ('label' => $class->lang['brandList'],   'securityObject' => 'Brand',   'phplist' => 'brandList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['warehouse'],   'securityObject' => 'Warehouse',   'phplist' => 'warehouseList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['warehouseLayout'],   'securityObject' => 'WarehouseLayout',   'phplist' => 'warehouseLayoutList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['warehouseLocation'],   'securityObject' => 'warehouseLocation',   'phplist' => 'warehouseLocationList', 'target' => 'tab' ));
     pushMenuItem($submenuitem, array('label' => $class->lang['pallet'],   'securityObject' => 'Pallet',   'phplist' => 'palletList', 'target' => 'tab'));
     pushMenuItem($submenuitem , array ('label' => $class->lang['car'],   'securityObject' => 'Car',   'phplist' => 'carList', 'target' => 'tab' ), array(2,3,8));
     pushMenuItem($submenuitem , array ('label' => $class->lang['carCategory'],   'securityObject' => 'CarCategory',   'phplist' => 'carCategoryList', 'target' => 'tab' ), array(2,3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['carSeries'],   'securityObject' => 'CarSeries',   'phplist' => 'carSeriesList', 'target' => 'tab' ), array(1,2,3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['chassis'],   'securityObject' => 'Chassis',   'phplist' => 'chassisList', 'target' => 'tab' ), array(2));
     pushMenuItem($submenuitem , array ('label' => $class->lang['chassisCategory'],   'securityObject' => 'ChassisCategory',   'phplist' => 'chassisCategoryList', 'target' => 'tab' ), array(2));
     //pushMenuItem($submenuitem , array ('label' => $class->lang['oilType'],   'securityObject' => 'OilType',   'phplist' => 'oilTypeList', 'target' => 'tab' ), array(3));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemSpecification'],   'securityObject' => 'ItemSpecification',   'phplist' => 'itemSpecificationList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['waste'],   'securityObject' => 'Waste',   'phplist' => 'wasteList', 'target' => 'tab' ));
 	 pushMenuItem($submenuitem , array ('label' => $class->lang['GPS'],   'securityObject' => 'gps',   'phplist' => 'gpsList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['pricingCategory'],   'securityObject' => 'PricingCategory',   'phplist' => 'jewelryPricingCategoryList', 'target' => 'tab' ), array(9));
     pushMenuItem($submenuitem , array ('label' => $class->lang['priceUpdate'],   'securityObject' => 'PriceUpdate',   'phplist' => 'priceUpdateList', 'target' => 'tab' ), array(9));
     pushMenuItem($submenuitem , array ('label' => $class->lang['itemVariation'],   'securityObject' => 'ItemVariation',   'phplist' => 'itemVariationList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['ringSize'],   'securityObject' => 'RingSize',   'phplist' => 'ringSizeList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['material'],   'securityObject' => 'Material',   'phplist' => 'materialList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['plating'],   'securityObject' => 'Plating',   'phplist' => 'platingList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['texture'],   'securityObject' => 'Texture',   'phplist' => 'textureList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['model'],   'securityObject' => 'Model',   'phplist' => 'modelList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['character'],   'securityObject' => 'Character',   'phplist' => 'characterList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['color'],   'securityObject' => 'Color',   'phplist' => 'colorList', 'target' => 'tab' ));
     pushMenuItem($submenuitem , array ('label' => $class->lang['packaging'],   'securityObject' => 'Packaging',   'phplist' => 'packagingList', 'target' => 'tab' ));
          

    if (PLAN_TYPE['usefrontend'] == 1){
         pushMenuItem($submenuitem , array ('label' => $class->lang['itemFilter'],   'securityObject' => 'ItemFilter',   'phplist' => 'itemFilterList', 'target' => 'tab' ), array(1));
         pushMenuItem($submenuitem , array ('label' => $class->lang['filterCategory'],   'securityObject' => 'FilterCategory',   'phplist' => 'filterCategoryList', 'target' => 'tab' ), array(1));
    }

     pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

     pushMenuItem($menuitem , $arrSubMenu);  

    pushMenuItem ($arrInventory['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrInventory); 

 
    // PURCHASE
    $arrPurchase = array ('label' => $class->lang['purchase'],'icon' => 'fas fa-shopping-basket'  );  
    $menuitem = array();


    $arrSubMenu = array ('label' => $class->lang['purchaseTransaction']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseRequest'],   'securityObject' => 'PurchaseRequest',   'phplist' => 'purchaseRequestList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrder'],   'securityObject' => 'PurchaseOrder',   'phplist' => 'purchaseOrderList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrder'],   'securityObject' => 'DisposalPurchaseOrder',   'phplist' => 'disposalPurchaseOrderList', 'target' => 'tab' ), array(8));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseCategory'],   'securityObject' => 'PurchaseCategory',   'phplist' => 'purchaseCategoryList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseReceive'],   'securityObject' => 'PurchaseReceive',   'phplist' => 'purchaseReceiveList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrder'],   'securityObject' => 'PurchaseOrderJewelry',   'phplist' => 'purchaseOrderJewelryList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseReceive'],   'securityObject' => 'ReceivingPurchaseJewelry',   'phplist' => 'receivingPurchaseJewelryList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseReceive'] . ' (Checking)',   'securityObject' => 'ReceivingPurchaseJewelry',   'phplist' => 'dashboard/receiving-purchase-jewelry', 'target' => '_blank' ));

    pushMenuItem($submenuitem , array ('label' => $class->lang['purchasePricing'],   'securityObject' => 'PurchasePricing',   'phplist' => 'purchasePricingList', 'target' => 'tab' ));

    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);  

    $arrSubMenu = array ('label' => $class->lang['service']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderTrucking'],   'securityObject' => 'TruckingPurchase',   'phplist' => 'truckingPurchaseList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingPurchaseRefund'],   'securityObject' => 'TruckingPurchaseRefund',   'phplist' => 'truckingPurchaseRefundList', 'target' => 'tab' ));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);  


//    $arrSubMenu = array ('label' => $class->lang['guaranteeLetter']);  
//    $submenuitem = array();
//    $arrSubMenu['menu'] = array();
//    pushMenuItem($submenuitem , array ('label' => $class->lang['guaranteeLetter'],   'securityObject' => 'MedicalPurchaseOrder',   'phplist' => 'medicalPurchaseOrderList', 'target' => 'tab' ));
//    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
//    pushMenuItem($menuitem , $arrSubMenu);  

    // FF
    $arrSubMenu = array ('label' => 'Freight Forwarding');  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 


	if (DOMAIN_NAME != 'niagara.wintera.co.id')
    	pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderImport'],   'securityObject' => 'EMKLPurchaseOrder',   'phplist' => 'emklPurchaseOrderImportList', 'target' => 'tab' ),array(2,5));
    
	pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderExport'],   'securityObject' => 'EMKLPurchaseOrder',   'phplist' => 'emklPurchaseOrderExportList', 'target' => 'tab' ),array(2,5));
     
	// khusus FORTIS sementara
	if (in_array(DOMAIN_NAME, array('fortis.wintera.co.id','thewhale.wintera.co.id','niagara.wintera.co.id','emkofi.wintera.co.id'))){
        pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderDomestic'],   'securityObject' => 'EMKLPurchaseOrder',   'phplist' => 'emklPurchaseOrderDomesticList', 'target' => 'tab' ),array(2,5)); 
    }

   if (in_array(DOMAIN_NAME, array('emkofi.wintera.co.id'))){
        pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderWarehouse'],   'securityObject' => 'EMKLPurchaseOrder',   'phplist' => 'emklPurchaseOrderWarehouseList', 'target' => 'tab' ),array(2,5));
        pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderTrucking'],   'securityObject' => 'EMKLPurchaseOrder',   'phplist' => 'emklPurchaseOrderTruckingList', 'target' => 'tab' ),array(2,5));
   }
    

    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseRefund'],   'securityObject' => 'EMKLCommission',   'phplist' => 'emklCommissionList', 'target' => 'tab' ),array(2,5));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 

    // OTHERS
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['templatePurchaseItem'],   'securityObject' => 'TemplateEMKLPurchaseItem',   'phplist' => 'templateEMKLPurchaseItemList', 'target' => 'tab' ),array(2,5));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrPurchase['menu'] = array();
    pushMenuItem ($arrPurchase['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrPurchase); 
 
   
    // SALES
    $arrSales = array ('label' => $class->lang['sales'],'icon' => 'fas fa-shopping-cart');  
    $menuitem = array(); 

    $arrSubMenu = array ('label' => 'Freight Forwarding');  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();    

    pushMenuItem($submenuitem , array ('label' => $class->lang['templateEMKLJobOrderImport'],   'securityObject' => 'TemplateEMKLJobOrder',   'phplist' => 'templateEMKLJobOrderImportList', 'target' => 'tab' ),array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['templateEMKLJobOrderExport'],   'securityObject' => 'TemplateEMKLJobOrder',   'phplist' => 'templateEMKLJobOrderExportList', 'target' => 'tab' ),array(2,5));

	// sementara khusus niagara
	if (DOMAIN_NAME != 'niagara.wintera.co.id')
		pushMenuItem($submenuitem , array ('label' => $class->lang['quotationOrderImport'],   'securityObject' => 'EMKLQuotationOrder',   'phplist' => 'emklQuotationOrderImportList', 'target' => 'tab' ),array(2,5));
	
	pushMenuItem($submenuitem , array ('label' => $class->lang['quotationOrderExport'],   'securityObject' => 'EMKLQuotationOrder',   'phplist' => 'emklQuotationOrderExportList', 'target' => 'tab' ),array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['quotationOrderDomestic'],   'securityObject' => 'EMKLQuotationOrder',   'phplist' => 'emklQuotationOrderDomesticList', 'target' => 'tab' ),array(2,5));

	 
	if (DOMAIN_NAME != 'niagara.wintera.co.id'){
		pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderImport'],   'securityObject' => 'EMKLOrder',   'phplist' => 'emklJobOrderImportHeaderList', 'target' => 'tab' ),array(2,5));
		pushMenuItem($submenuitem , array ('label' => $class->lang['importOrderSheet'],   'securityObject' => 'EMKLJobOrder',   'phplist' => 'emklJobOrderImportList', 'target' => 'tab' ),array(2,5));
	}
	
	pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderExport'],   'securityObject' => 'EMKLOrder',   'phplist' => 'emklJobOrderExportHeaderList', 'target' => 'tab' ),array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['exportOrderSheet'],   'securityObject' => 'EMKLJobOrder',   'phplist' => 'emklJobOrderExportList', 'target' => 'tab' ),array(2,5));
    

	// khusus FORTIS sementara
	if (in_array(DOMAIN_NAME, array('fortis.wintera.co.id','thewhale.wintera.co.id','niagara.wintera.co.id','emkofi.wintera.co.id'))){
		pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderDomestic'],   'securityObject' => 'EMKLOrder',   'phplist' => 'emklJobOrderDomesticHeaderList', 'target' => 'tab' ),array(2,5));
		pushMenuItem($submenuitem , array ('label' => $class->lang['domesticOrderSheet'],   'securityObject' => 'EMKLJobOrder',   'phplist' => 'emklJobOrderDomesticList', 'target' => 'tab' ),array(2,5));
	}

    if (in_array(DOMAIN_NAME, array('emkofi.wintera.co.id'))){
        pushMenuItem($submenuitem , array ('label' => $class->lang['warehouseOrderSheet'],   'securityObject' => 'EMKLJobOrder',   'phplist' => 'emklJobOrderWarehouseList', 'target' => 'tab' ),array(2,5));
        pushMenuItem($submenuitem , array ('label' => $class->lang['truckingOrderSheet'],   'securityObject' => 'EMKLJobOrder',   'phplist' => 'emklJobOrderTruckingList', 'target' => 'tab' ),array(2,5));
    }
    
	pushMenuItem($submenuitem , array ('label' => $class->lang['HouseBL'],   'securityObject' => 'EMKLHouseBL',   'phplist' => 'emklHouseBLList', 'target' => 'tab' ),array(2,5));
    
	pushMenuItem($menuitem , array ('label' => $class->lang['templateActivity'],   'securityObject' => 'templateActivity',   'phplist' => 'templateActivityList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['activityProgress'],   'securityObject' => 'ActivityProgress',   'phplist' => 'activityProgressList', 'target' => 'tab' ));

    //pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'].' (EMKL)',   'securityObject' => $emklSalesOrder->securityObject,   'phplist' => 'emklSalesOrderList', 'target' => 'tab' ),array(2));
    
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 

    // TRUCKING
    $arrSubMenu = array ('label' => 'EMKL / '.  $class->lang['trucking']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();   
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobType'],   'securityObject' => 'TruckingJob',   'phplist' => 'truckingJobList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderQuotation'],   'securityObject' => 'TruckingQuotation',   'phplist' => 'truckingQuotationList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderCategory'],   'securityObject' => 'TruckingServiceOrderCategory',   'phplist' => 'truckingServiceOrderCategoryList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobProgress'],   'securityObject' => 'JobProgress',   'phplist' => 'jobProgressList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrder'],   'securityObject' => 'TruckingServiceOrder',   'phplist' => 'truckingServiceOrderList', 'target' => 'tab' ),array(2));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['multiPointJobOrder'],   'securityObject' => 'TruckingServiceOrder',   'phplist' => 'multiPointJobOrderList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingServiceWorkOrder'],   'securityObject' => 'TruckingServiceWorkOrder',   'phplist' => 'truckingServiceWorkOrderList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingServiceWorkOrder'] . ' (Batch)',   'securityObject' => 'TruckingServiceWorkOrder',   'phplist' => 'dashboard/trucking-service-work-order-update', 'target' => '_blank' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingServiceWorkOrder'] . ' (Scan QR)',   'securityObject' => 'TruckingServiceWorkOrder',   'phplist' => 'dashboard/driver-assign', 'target' => '_blank' ),array(2));

	pushMenuItem($submenuitem , array ('label' => $class->lang['GPSTracker'],   'securityObject' => 'TruckingServiceWorkOrder',   'phplist' => '/admin/dashboard/truckingworkorder', 'target' => '_blank' ),array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 


    // DUMP TRUCK
//    $arrSubMenu = array ('label' => 'Dumper');  
//    $submenuitem = array();
//    $arrSubMenu['menu'] = array();    
//
//    pushMenuItem($submenuitem , array ('label' => $class->lang['project'],   'securityObject' => 'ProjectDumper',   'phplist' => 'projectDumperList', 'target' => 'tab' ),array(2));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'] . ' (Dumper)',   'securityObject' => 'SalesOrderDumper',   'phplist' => 'salesOrderDumperList', 'target' => 'tab' ),array(2));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['invoice'] . ' (Dumper)',   'securityObject' => 'SalesOrderDumperInvoice',   'phplist' => 'salesOrderDumperInvoiceList', 'target' => 'tab' ),array(2));
//       
//    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
//    pushMenuItem($menuitem , $arrSubMenu); 



    // Logistic
    $arrSubMenu = array ('label' => 'Logistic');  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();    

    pushMenuItem($submenuitem , array ('label' => $class->lang['shippingRate'],   'securityObject' => 'ShippingRate',   'phplist' => 'shippingRateList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'] . ' (Logistic)',   'securityObject' => 'LogisticSalesOrder',   'phplist' => 'logisticSalesOrderList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['manifest'] ,   'securityObject' => 'LogisticSalesOrderManifest',   'phplist' => 'logisticSalesOrderManifestList', 'target' => 'tab' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobContract'],   'securityObject' => 'DisposalContract',   'phplist' => 'disposalContractList', 'target' => 'tab' ),array(8));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrder'],   'securityObject' => 'DisposalJobOrder',   'phplist' => 'disposalJobOrderList', 'target' => 'tab' ),array(8));
    pushMenuItem($submenuitem , array ('label' => $class->lang['workOrderDispatcher'],  'securityObject' => 'DisposalWorkOrderDispatcher',   'phplist' => 'disposalWorkOrderDispatcherList', 'target' => 'tab' ),array(8));
    pushMenuItem($submenuitem , array ('label' => $class->lang['workOrder'],  'securityObject' => 'DisposalWorkOrder',   'phplist' => 'disposalWorkOrderList', 'target' => 'tab' ),array(8));
  
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 



    // RETAIL & BENGKEL
    $arrSubMenu = array ('label' => $class->lang['salesTransaction']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
	pushMenuItem($submenuitem , array ('label' => $class->lang['recurringPeriod'],   'securityObject' => 'RecurringPeriod',   'phplist' => 'recurringPeriodList', 'target' => 'tab' ),array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderRecurringSubscription'],   'securityObject' => 'SalesOrderRecurringSubscription',   'phplist' => 'salesOrderRecurringSubscriptionList', 'target' => 'tab' ),array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderRecurringTermination'],   'securityObject' => 'SalesOrderRecurringTermination',   'phplist' => 'salesOrderRecurringTerminationList', 'target' => 'tab' ),array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'],   'securityObject' => 'SalesOrder',   'phplist' => 'salesOrderList', 'target' => 'tab' ),array(1,2,4,6,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderWorkshop'],   'securityObject' => 'SalesOrderCarService',   'phplist' => 'salesOrderCarServiceList', 'target' => 'tab' ),array(2,3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'].' SC',   'securityObject' => 'SalesOrderSubscription',   'phplist' => 'salesOrderSubscriptionList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['installationWorkOrder'],   'securityObject' => 'InstallationWorkOrder',   'phplist' => 'installationWorkOrderList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['BAST'],   'securityObject' => 'InstallationBAST',   'phplist' => 'installationBASTList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['invoice'],   'securityObject' => 'InvoiceOrderSubscription',   'phplist' => 'invoiceOrderSubscriptionList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['termination'],   'securityObject' => 'Termination',   'phplist' => 'terminationList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ticketSupport'],   'securityObject' => 'TicketSupport',   'phplist' => 'ticketSupportList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['supportWorkOrder'],   'securityObject' => 'TicketSupportWorkOrder',   'phplist' => 'ticketSupportWorkOrderList', 'target' => 'tab' ),array(6));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesDelivery'],   'securityObject' => 'SalesDelivery',   'phplist' => 'salesDeliveryList', 'target' => 'tab' ),array(1,3,4));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesReturn'],   'securityObject' => 'SalesCarServiceReturn',   'phplist' => 'salesCarServiceReturnList', 'target' => 'tab' ),array(3));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['membershipRegistration'],   'securityObject' => 'CustomerMembership',   'phplist' => 'customerMembershipList', 'target' => 'tab' ));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['offerSimulator'],   'securityObject' => 'OfferSimulator',   'phplist' => 'offerSimulatorList', 'target' => 'tab' ),array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderCategory'],   'securityObject' => 'SalesOrderPropertyType',   'phplist' => 'salesOrderPropertyTypeList', 'target' => 'tab' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrder'] . ' ('.$class->lang['property'].')',   'securityObject' => 'SalesOrderProperty',   'phplist' => 'salesOrderPropertyList', 'target' => 'tab' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['salesReturn'],   'securityObject' => 'SalesOrderReturn',   'phplist' => 'salesOrderReturnList', 'target' => 'tab' ));

	pushMenuItem($submenuitem , array ('label' => $class->lang['newRequest'],   'securityObject' => 'MedicalRequestClaim',   'phplist' => 'medicalRequestClaimList', 'target' => 'tab' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrder'],   'securityObject' => 'MedicalJobOrder',   'phplist' => 'medicalJobOrderList', 'target' => 'tab' ));
	
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 


    $arrSubMenu = array ('label' => $class->lang['quotation']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
 	pushMenuItem($submenuitem , array ('label' => $class->lang['priceQuotation'],   'securityObject' => 'MedicalSalesOrderQuotation',   'phplist' => 'medicalSalesOrderQuotationList', 'target' => 'tab' ));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 



    $arrSubMenu = array ('label' => $class->lang['rentalSales']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesRentalQuotation'],   'securityObject' => 'SalesRentalQuotation',   'phplist' => 'salesRentalQuotationList', 'target' => 'tab' ) );
    pushMenuItem($submenuitem , array ('label' => $class->lang['rentalSales'],   'securityObject' => 'SalesOrderRental',   'phplist' => 'salesOrderRentalList', 'target' => 'tab' ) );
    pushMenuItem($submenuitem , array ('label' => $class->lang['deliveryWorkOrder'],   'securityObject' => 'SalesOrderRentalWorkOrder',   'phplist' => 'salesOrderRentalWorkOrderList', 'target' => 'tab' ) );
    pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 



    // OTHERS
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();     
    pushMenuItem($submenuitem , array ('label' => $class->lang['carRevenue'],   'securityObject' => 'CarRevenue',   'phplist' => 'carRevenueList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['marketplace'],   'securityObject' => 'Marketplace',   'phplist' => 'marketplaceList', 'target' => 'tab' ),array(1));
    pushMenuItem($submenuitem , array ('label' => $class->lang['storefront'],   'securityObject' => 'Storefront',   'phplist' => 'storefrontList', 'target' => 'tab' ), array(1)); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderReminder'],   'securityObject' => 'EMKLReminderJobOrder',   'phplist' => 'emklReminderJobOrderList', 'target' => 'tab' ),array(2));
   	pushMenuItem($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem($menuitem , $arrSubMenu); 


    $arrSubMenu = array ('label' => $class->lang['rateList']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
    pushMenuItem($submenuitem , array ('label' => $class->lang['sellingRate'],   'securityObject' => 'TruckingSellingRate',   'phplist' => 'truckingSellingRateList', 'target' => 'tab' ),array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costRate'],   'securityObject' => 'CostRate',   'phplist' => 'costRateList', 'target' => 'tab' ),array(2));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

//    udah tdk terpakai
//    $arrSubMenu = array ('label' => $class->lang['others']);  
//    $submenuitem = array();
//    $arrSubMenu['menu'] = array();  
//    pushMenuItem($submenuitem , array ('label' => $class->lang['driverProgressStep'],   'securityObject' => 'WorkProgressStep',   'phplist' => 'workProgressStepList', 'target' => 'tab' ),array(2));
//    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
//    pushMenuItem($menuitem , $arrSubMenu); 

  
    $arrSubMenu = array ('label' => $class->lang['maintenance']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
    pushMenuItem($submenuitem , array ('label' => $class->lang['maintenanceChecklist'],   'securityObject' => 'CarMaintenanceChecklist',   'phplist' => 'carMaintenanceChecklistList', 'target' => 'tab' ),array(3));
    pushMenuItem($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

    $arrSales['menu'] = array();
    pushMenuItem ($arrSales['menu'], $menuitem);   

    pushMenuItem ($arrMenu, $arrSales); 
  

    // FACTORY
	$arrAssembly = array ('label' => $class->lang['assembly'],'icon' => 'fas fa-industry-alt'  );  
	$arrAssembly['menu'] = array(); 

	$menuitem = array();
	pushMenuItem($menuitem , array ('label' => $class->lang['billOfMaterials'],   'securityObject' => 'BillOfMaterials',   'phplist' => 'billOfMaterialsList', 'target' => 'tab' ), array(1,3));
	pushMenuItem($menuitem , array ('label' => $class->lang['assemblyItem'],   'securityObject' => 'Assembly',   'phplist' => 'assemblyList', 'target' => 'tab' ), array(1,3));
    pushMenuItem ($arrAssembly['menu'], $menuitem);    
    pushMenuItem ($arrMenu, $arrAssembly); 


    // MAINTENANCE
    $arrMaintenance = array ('label' => $class->lang['maintenance'],'icon' => 'fas fa-wrench'  );  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['carMaintenanceRequest'],   'securityObject' => 'CarServiceMaintenanceRequest',   'phplist' => 'carServiceMaintenanceRequestList', 'target' => 'tab' ),array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['carMaintenance'],   'securityObject' => 'CarServiceMaintenance',   'phplist' => 'carServiceMaintenanceList', 'target' => 'tab' ),array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['partsPosition'],   'securityObject' => 'ItemPosition',   'phplist' => 'itemPositionList', 'target' => 'tab' ),array(2));
    $arrMaintenance['menu'] = array();
    pushMenuItem ($arrMaintenance['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrMaintenance); 
 
   
    // AFTER SALES
/*    $arrSubMenu = array ('label' => $class->lang['afterSales'],'icon' => 'fas fa-tasks'  );  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();
    pushMenuItem($submenuitem , array ('label' => $class->lang['warrantyClaim'],   'securityObject' => $warrantyClaim->securityObject,   'phplist' => 'warrantyClaimList', 'target' => 'tab' ),array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['warrantyClaimProgress'],   'securityObject' => $warrantyClaimProgress->securityObject,   'phplist' => 'warrantyClaimProgressList', 'target' => 'tab' ),array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['vendorWarrantyClaim'],   'securityObject' => $vendorWarrantyClaim->securityObject,   'phplist' => 'vendorWarrantyClaimList', 'target' => 'tab' ),array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['vendorWarrantyClaimReceive'],   'securityObject' => $vendorWarrantyClaimReturn->securityObject,   'phplist' => 'vendorWarrantyClaimReturnList', 'target' => 'tab' ),array(5));
    
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);   
    pushMenuItem ($arrMenu, $arrSubMenu); */
  
    //pushMenuItem($menuitem , array ('label' => $class->lang['pointofsales'],   'securityObject' => $salesOrder->securityObject,   'phplist' => 'pointofsales', 'target' => '_blank' ));
    //pushMenuItem($menuitem , array ('label' => $class->lang['preorderSales'],   'securityObject' => $preorder->securityObject,   'phplist' => 'preorderList', 'target' => 'tab' ));
   

    // DEPOT
    $arrDepot = array ('label' => $class->lang['depot'],'icon' => 'fas fa-warehouse'  );  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['itemIn'],   'securityObject' => 'ItemInDepot',   'phplist' => 'itemInDepotList', 'target' => 'tab' ),array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['itemOut'],   'securityObject' => 'ItemOutDepot',   'phplist' => 'itemOutDepotList', 'target' => 'tab' ),array(2));
    $arrDepot['menu'] = array();
    pushMenuItem ($arrDepot['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrDepot); 
 
	// ASSET

    $arrAsset = array ('label' => $class->lang['asset'], 'icon' => 'fas fa-car-building'); 
 
    $menuitem = array(); 

	// ASSETS

	$arrSubMenu['menu'] = array(); 
	pushMenuItem($menuitem , array ('label' => $class->lang['assetList'],   'securityObject' => 'Asset',   'phplist' => 'assetList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['assetCategory'],   'securityObject' => 'AssetCategory',   'phplist' => 'assetCategoryList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['assetGroup'],   'securityObject' => 'AssetGroup',   'phplist' => 'assetGroupList', 'target' => 'tab' ));
	

	$arrSubMenu = array ('label' => $class->lang['transaction']);  
	$submenuitem = array();
    $arrSubMenu['menu'] = array(); 
	pushMenuItem($submenuitem , array ('label' => $class->lang['assetPurchaseOrder'],   'securityObject' => 'AssetPurchase',   'phplist' => 'assetPurchaseList', 'target' => 'tab' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['assetDepreciation'],   'securityObject' => 'AssetDepreciation',   'phplist' => 'assetDepreciationList', 'target' => 'tab' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['amortization'],   'securityObject' => 'Amortization',   'phplist' => 'amortizationList', 'target' => 'tab' ));
	pushMenuItem($arrSubMenu['menu'], $submenuitem); 
	pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrAsset['menu'] = array();
    pushMenuItem ($arrAsset['menu'], $menuitem);  

    pushMenuItem ($arrMenu, $arrAsset); 

    // FINANCE
    $arrFinance = array ('label' => $class->lang['finance'], 'icon' => 'fas fa-money-check-alt'); 
 
    $menuitem = array(); 
    
        // SALES
        $arrSubMenu = array ('label' => $class->lang['sales']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array();  
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoice'],   'securityObject' => 'TruckingServiceOrderInvoice',   'phplist' => 'truckingServiceOrderInvoiceList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesWasteInvoice'],   'securityObject' => 'DisposalSalesInvoice',   'phplist' => 'disposalSalesInvoiceList', 'target' => 'tab' ),array(8));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoice'],   'securityObject' => 'DisposalSalesWasteInvoice',   'phplist' => 'disposalSalesWasteInvoiceList', 'target' => 'tab' ),array(8));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceipt'],   'securityObject' => 'SalesOrderInvoiceReceipt',   'phplist' => 'salesOrderInvoiceReceiptList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoice'],   'securityObject' => 'EMKLOrderInvoice',   'phplist' => 'emklOrderInvoiceList', 'target' => 'tab' ),array(2,5));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceipt'],   'securityObject' => 'EMKLInvoiceReceipt',   'phplist' => 'emklInvoiceReceiptList', 'target' => 'tab' ),array(2,5));
        pushMenuItem($submenuitem , array ('label' => $class->lang['invoiceTaxNumber'],   'securityObject' => 'InvoiceTax',   'phplist' => 'invoiceTaxList', 'target' => 'tab' ),array(2,5));
        pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoiceRental'],   'securityObject' => 'SalesOrderRentalInvoice',   'phplist' => 'salesOrderRentalInvoiceList', 'target' => 'tab' ) );
		pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoice'],   'securityObject' => 'MedicalSalesInvoice',   'phplist' => 'medicalSalesInvoiceList', 'target' => 'tab' ));
   
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
        pushMenuItem($menuitem , $arrSubMenu); 

 
        // KAS BANK
        $arrSubMenu = array ('label' => $class->lang['cashBank']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['costList'],   'securityObject' => 'CostCashOut',   'phplist' => 'costCashOutList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['revenueList'],   'securityObject' => 'RevenueCashIn',   'phplist' => 'revenueCashInList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvance'],   'securityObject' => 'CashAdvance',   'phplist' => 'cashAdvanceList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvanceRealization'],   'securityObject' => 'cashAdvanceRealization',   'phplist' => 'cashAdvanceRealizationList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankIn'],   'securityObject' => 'CashBankIn',   'phplist' => 'cashBankInList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankOut'],   'securityObject' => 'CashBankOut',   'phplist' => 'cashBankOutList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashIn'],   'securityObject' => 'CashIn',   'phplist' => 'cashInList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashOut'],   'securityObject' => 'CashOut',   'phplist' => 'cashOutList', 'target' => 'tab' ));
         //pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostCashIn'],   'securityObject' => $truckingCostCashIn->securityObject,   'phplist' => 'truckingCostCashInList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['truckingAdditionalCost'],   'securityObject' => 'truckingAdditionalCost',   'phplist' => 'truckingAdditionalCostList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostCashOut'],   'securityObject' => 'TruckingCostCashOut',   'phplist' => 'truckingCostCashOutList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCashOutRequest'],   'securityObject' => 'TruckingCashOutRequest',   'phplist' => 'truckingCashOutRequestList', 'target' => 'tab' ),array(2));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankTransfer'],   'securityObject' => 'CashBankTransfer',   'phplist' => 'cashBankTransferList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankRealization'],   'securityObject' => 'CashBankRealization',   'phplist' => 'cashBankRealizationList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['cashAndBankVoucher'],   'securityObject' => 'CashBank',   'phplist' => 'cashBankList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['bankReconsiliation'],   'securityObject' => 'BankReconsiliation',   'phplist' => 'bankReconsiliationList', 'target' => 'tab' ));
		pushMenuItem($submenuitem , array ('label' => $class->lang['accountStatementImport'],   'securityObject' => 'AccountStatementImport',   'phplist' => 'accountStatementImportList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['pettyCash'],   'securityObject' => 'PettyCash',   'phplist' => 'dashboard/petty-cash', 'target' => '_blank' ),array(2));
 
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

        // DOWNPAYMENT
        $arrSubMenu = array ('label' => $class->lang['downpayment']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['customerDownpayment'],   'securityObject' => 'CustomerDownpayment',   'phplist' => 'customerDownpaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['customerDownpaymentSettlement'],   'securityObject' => 'CustomerDownpaymentSettlement',   'phplist' => 'customerDownpaymentSettlementList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['supplierDownpayment'],   'securityObject' => 'SupplierDownpayment',   'phplist' => 'supplierDownpaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['supplierDownpaymentSettlement'],   'securityObject' => 'SupplierDownpaymentSettlement',   'phplist' => 'supplierDownpaymentSettlementList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 


        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['accountsPayable']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsPayable'],   'securityObject' => 'AP',   'phplist' => 'apList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsPayablePayment'],   'securityObject' => 'APPayment',   'phplist' =>  'apPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23'],   'securityObject' => 'APPayableTax23',   'phplist' => 'apPayableTax23List', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23Payment'],   'securityObject' => 'APPayableTax23Payment',   'phplist' => 'apPayableTax23PaymentList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['accountsReceivable']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsReceivable'],   'securityObject' => 'AR',   'phplist' => 'arList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['accountsReceivablePayment'],   'securityObject' => 'ARPayment',   'phplist' => 'arPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['arapNetting'],   'securityObject' => 'ARAPNetting',   'phplist' => 'arapNettingList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['ARDiscountApproval'],   'securityObject' => 'ARDiscountApproval',   'phplist' => 'arDiscountApprovalList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23'],   'securityObject' => 'ARPrepaidTax23',   'phplist' => 'arPrepaidTax23List', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23Payment'],   'securityObject' => 'ARPrepaidTax23Payment',   'phplist' => 'arPrepaidTax23PaymentList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

       
        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['employeeARAP']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAP'],   'securityObject' => 'APEmployee',   'phplist' => 'apEmployeeList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAPPayment'],   'securityObject' => 'APEmployeePayment',   'phplist' => 'apEmployeePaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAR'],   'securityObject' => 'AREmployee',   'phplist' => 'arEmployeeList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeARPayment'],   'securityObject' => 'AREmployeePayment',   'phplist' => 'arEmployeePaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeARAPNetting'],   'securityObject' => 'ARAPEmployeeNetting',   'phplist' => 'arapEmployeeNettingList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

       
        // AR/AP
        $arrSubMenu = array ('label' => $class->lang['commission']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        
        // perhitungan komisi (oceanus)
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommission'],   'securityObject' => 'employeeCommission',   'phplist' => 'employeeCommissionList', 'target' => 'tab' ));
        
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommissionAP'],   'securityObject' => 'APEmployeeCommission',   'phplist' => 'apEmployeeCommissionList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommissionAPPayment'],   'securityObject' => 'APEmployeeCommissionPayment',   'phplist' => 'apEmployeeCommissionPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['apCommission'],   'securityObject' => 'APCommission',   'phplist' => 'apCommissionList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['apCommissionPayment'],   'securityObject' => 'APCommissionPayment',   'phplist' => 'apCommissionPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['apCustomerCommission'],   'securityObject' => 'APCustomerCommission',   'phplist' => 'apCustomerCommissionList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['apCustomerCommissionPayment'],   'securityObject' => 'APCustomerCommissionPayment',   'phplist' => 'apCustomerCommissionPaymentList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 


// CN DN

        $arrSubMenu = array ('label' => $class->lang['CN/DN']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['debitNote'],   'securityObject' => 'debitNote',   'phplist' => 'debitNoteList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['creditNote'],   'securityObject' => 'creditNote',   'phplist' => 'creditNoteList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem); 
  
        pushMenuItem($menuitem , $arrSubMenu); 

       
        // Pepaid Expense
        $arrSubMenu = array ('label' => $class->lang['prepaidCost']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidCost'],   'securityObject' => 'prepaidExpense',   'phplist' => 'prepaidExpenseList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['costReconsiliation'],   'securityObject' => 'CostReconsile',   'phplist' => 'costReconsileList', 'target' => 'tab' ));
        pushMenuItem($arrSubMenu['menu'], $submenuitem);
        pushMenuItem($menuitem , $arrSubMenu); 
  	
        // TAX
        $arrSubMenu = array ('label' => $class->lang['tax']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
//        pushMenuItem($submenuitem , array ('label' => $class->lang['vatIn'],   'securityObject' => 'VatIn',   'phplist' => 'vatInList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['vatOut'],   'securityObject' => 'VatOut',   'phplist' => 'vatOutList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['taxType'],   'securityObject' => 'Tax',   'phplist' => 'taxList', 'target' => 'tab' ));
        pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

        pushMenuItem($menuitem , $arrSubMenu); 

        // GL
        $arrSubMenu = array ('label' => $class->lang['GL']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 
        pushMenuItem($submenuitem , array ('label' => $class->lang['chartOfAccount'],   'securityObject' => 'ChartOfAccount',   'phplist' => 'chartOfAccountList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['generalJournal'],   'securityObject' => 'GeneralJournal',   'phplist' => 'generalJournalList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['journalBalancing'],   'securityObject' => 'JournalBalancing',   'phplist' => 'journalBalancingList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['coalink'],   'securityObject' => 'COALink',   'phplist' => 'coaLinkForm', 'target' => 'tab' ));
        pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

        pushMenuItem($menuitem , $arrSubMenu); 

        // OTHERS
        $arrSubMenu = array ('label' => $class->lang['others']);  
        $submenuitem = array();
        $arrSubMenu['menu'] = array(); 

        if (PLAN_TYPE['usefrontend'] == 1){
            pushMenuItem($submenuitem , array ('label' => $class->lang['paymentConfirmation'],   'securityObject' => 'PaymentConfirmation',   'phplist' => 'paymentConfirmationList', 'target' => 'tab' ));
        }

       	pushMenuItem($submenuitem , array ('label' => $class->lang['termofpayment'],   'securityObject' => 'TermOfPayment',   'phplist' => 'termOfPaymentList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['invoicePeriod'],   'securityObject' => 'InvoicePeriod',   'phplist' => 'invoicePeriodList', 'target' => 'tab' ));   
        pushMenuItem($submenuitem , array ('label' => $class->lang['paymentMethod'],   'securityObject' => 'PaymentMethod',   'phplist' => 'paymentMethodList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['currency'],   'securityObject' => 'Currency',   'phplist' => 'currencyList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['currencyRate'],   'securityObject' => 'CurrencyRate',   'phplist' => 'currencyRateList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['currencyRateMaster'],   'securityObject' => 'CurrencyRateMaster',   'phplist' => 'currencyRateMasterList', 'target' => 'tab' ));
        
        //pushMenuItem($submenuitem , array ('label' => $class->lang['leasing'],   'securityObject' => $leasing->securityObject,   'phplist' => 'leasingList', 'target' => 'tab' ));
        pushMenuItem($submenuitem , array ('label' => $class->lang['routineCost'],   'securityObject' => 'RoutineCost',   'phplist' => 'routineCostList', 'target' => 'tab' ));
        pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

    pushMenuItem($menuitem , $arrSubMenu); 

    $arrFinance['menu'] = array();
    pushMenuItem ($arrFinance['menu'], $menuitem);  

    pushMenuItem ($arrMenu, $arrFinance); 
 

    // MEDICAL
    $arrMedical = array ('label' => $class->lang['medicalRecord'], 'icon' => 'fas fa-money-check-alt');  
    $menuitem = array();
    $arrSubMenu['menu'] = array();    
    pushMenuItem($menuitem , array ('label' => $class->lang['medicalRecord'],   'securityObject' => 'MedicalRecord',   'phplist' => 'medicalRecordList', 'target' => 'tab' ));       
    
    $arrMedical['menu'] = array();
    pushMenuItem ($arrMedical['menu'], $menuitem);  

    pushMenuItem ($arrMenu, $arrMedical); 



    // MEDIA 
        $arrMedia = array ('label' => $class->lang['articleNewsAndMedia'], 'icon' => 'fas fa-newspaper');  
        $menuitem = array();

    if (PLAN_TYPE['usefrontend'] == 1){
        pushMenuItem($menuitem , array ('label' => $class->lang['article'],   'securityObject' => 'Article',   'phplist' => 'articleList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['articleCategory'],   'securityObject' => 'ArticleCategory',   'phplist' => 'articleCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['news'],   'securityObject' => 'News',   'phplist' => 'newsList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['newsCategory'],   'securityObject' => 'NewsCategory',   'phplist' => 'newsCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['portfolio'],   'securityObject' => 'Portfolio',   'phplist' => 'portfolioList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['portfolioCategory'],   'securityObject' => 'PortfolioCategory',   'phplist' => 'portfolioCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['gallery'],   'securityObject' => 'Gallery',   'phplist' => 'galleryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['galleryCategory'],   'securityObject' => 'GalleryCategory',   'phplist' => 'galleryCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['galleryHumanResource'],   'securityObject' => 'GalleryHumanResource',   'phplist' => 'galleryHumanResourceList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['galleryHumanResourceCategory'],   'securityObject' => 'GalleryHumanResourceCategory',   'phplist' => 'galleryHRCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['youtube'],   'securityObject' => 'Youtube',   'phplist' => 'youtubeList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['banner'],   'securityObject' => 'Banner',   'phplist' => 'bannerList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['eventCategory'],   'securityObject' => 'EventCategory',   'phplist' => 'eventCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['event'],   'securityObject' => 'Event',   'phplist' => 'eventList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['corporateValues'],   'securityObject' => 'CorporateValues',   'phplist' => 'corporateValuesList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['companyHistory'],   'securityObject' => 'CompanyHistory',   'phplist' => 'companyHistoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['awardsAndAchievements'],   'securityObject' => 'Achievement',   'phplist' => 'achievementList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['CSR'],   'securityObject' => 'CSR',   'phplist' => 'CSRList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['CSRCategory'],   'securityObject' => 'CSRCategory',   'phplist' => 'CSRCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['GCG'],   'securityObject' => 'GoodCorporateGovernment',   'phplist' => 'goodCorporateGovernmentList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['GCGReport'],   'securityObject' => 'GoodCorporateGovernment',   'phplist' => 'goodCorporateGovernmentReportList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['GCGCategory'],   'securityObject' => 'GoodCorporateGovernmentCategory',   'phplist' => 'goodCorporateGovernmentCategoryList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->getLang('features'),   'securityObject' => 'Features',   'phplist' => 'featuresList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->getLang('newRelease'),   'securityObject' => 'NewRelease',   'phplist' => 'newReleaseList', 'target' => 'tab' ));
	
    }	
	
		pushMenuItem($menuitem , array ('label' => $class->lang['customerNews'],   'securityObject' => 'customerNews',   'phplist' => 'customerNewsList', 'target' => 'tab' ));


//        $arrMedia['menu'] = array();
//        pushMenuItem ($arrMedia['menu'], $menuitem);   
//        pushMenuItem ($arrMenu, $arrMedia); 
//        
//        $arrMedia = array ('label' => $class->lang['investorRelations'], 'icon' => 'far fa-handshake');  
//        $menuitem = array();
//        pushMenuItem($menuitem , array ('label' => $class->lang['investorRelations'],   'securityObject' => 'InvestorRelations',   'phplist' => 'investorRelationsList', 'target' => 'tab' ));
//        pushMenuItem($menuitem , array ('label' => $class->lang['investorNews'],   'securityObject' => 'InvestorNews',   'phplist' => 'investorNewsList', 'target' => 'tab' ));
//        pushMenuItem($menuitem , array ('label' => $class->lang['investorNewsCategory'],   'securityObject' => 'InvestorNewsCategory',   'phplist' => 'investorNewsCategoryList', 'target' => 'tab' ));
//        pushMenuItem($menuitem , array ('label' => $class->lang['investorReport'],   'securityObject' => 'InvestorReport',   'phplist' => 'investorReportList', 'target' => 'tab' ));
//    
        $arrMedia['menu'] = array();
        pushMenuItem ($arrMedia['menu'], $menuitem);   
        pushMenuItem ($arrMenu, $arrMedia); 
  
	

    // EVENT 

     

    // HRD 
    $arrOthers = array ('label' => $class->lang['HRD'], 'icon' => 'far fa-folder-open');  
    $menuitem = array();
    
    pushMenuItem($menuitem , array ('label' => $class->lang['employeeAttendance'] . ' (Import)',   'securityObject' => 'EmployeeAttendance',  'phplist' => 'employeeAttendanceImportList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['employeeAttendance'], 'securityObject' => 'EmployeeAttendance',  'phplist' => 'employeeAttendanceList', 'target' => 'tab' ));
    
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    


    // COURSE 
    $arrOthers = array ('label' => $class->lang['course'], 'icon' => 'fas fa-bullhorn');  
    $menuitem = array();
    
    pushMenuItem($menuitem , array ('label' => $class->lang['courseCategory'],   'securityObject' => 'CourseCategory',  'phplist' => 'courseCategoryList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['courseList'],   'securityObject' => 'Course',   'phplist' => 'courseList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['quiz'],   'securityObject' => 'Quiz',   'phplist' => 'quizList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['quizResult'],   'securityObject' => 'Quiz',   'phplist' => 'quizResultList', 'target' => 'tab' ),array(1));
 
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    


    // MEETING POINT 
    $arrOthers = array ('label' => $class->lang['meeting'], 'icon' => 'fas fa-bullhorn');  
    $menuitem = array();
    
    pushMenuItem($menuitem , array ('label' => $class->lang['meetingPoint'],   'securityObject' => 'MeetingPoint',  'phplist' => 'meetingPointList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['meetingSchedule'],   'securityObject' => 'MeetingSchedule',   'phplist' => 'meetingScheduleList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['meetingPointSuggestion'],   'securityObject' => 'MeetingPointSuggestion',  'phplist' => 'meetingPointSuggestionList', 'target' => 'tab' ),array(1));
    
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    
	// GIVE OPPORTUNITY 
    $arrOthers = array ('label' => $class->lang['opportunity'], 'icon' => 'fas fa-handshake');  
    $menuitem = array();
    
    pushMenuItem($menuitem , array ('label' => $class->lang['opportunity'],   'securityObject' => 'GiveOpportunity',  'phplist' => 'giveOpportunityList', 'target' => 'tab' ),array(1));
    
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 

    // CAREER 
    $arrOthers = array ('label' => $class->lang['jobOpportunities'], 'icon' => 'fas fa-building');  
    $menuitem = array();

    pushMenuItem($menuitem , array ('label' => $class->lang['careerCategory'],   'securityObject' => 'CareerCategory',   'phplist' => 'careerCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['careerField'],   'securityObject' => 'CareerField',   'phplist' => 'careerFieldList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['careerDepartment'],   'securityObject' => 'CareerDepartment',   'phplist' => 'careerDepartmentList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['jobExperience'],   'securityObject' => 'JobExperience',   'phplist' => 'jobExperienceList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['careerReference'],   'securityObject' => 'CareerReference',   'phplist' => 'careerReferenceList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['joiningConsideration'],   'securityObject' => 'JoiningConsideration',   'phplist' => 'joiningConsiderationList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['jobOpportunities'],   'securityObject' => 'JobOpportunities',   'phplist' => 'jobOpportunitiesList', 'target' => 'tab' ));
//    pushMenuItem($menuitem , array ('label' => $class->lang['jobApplication'],   'securityObject' => 'JobApplication',   'phplist' => 'jobApplicationList', 'target' => 'tab' ));
//  deprecated  pushMenuItem($menuitem , array ('label' => $class->lang['recruitment'],   'securityObject' => 'recruitment',   'phplist' => 'recruitmentList', 'target' => 'tab' ));
    
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    
    //PROMO & CAMPAIGN
    
    $arrOthers = array ('label' => $class->lang['promoAndCampaign'], 'icon' => 'fas fa-bullhorn');  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['receiptValidation'],   'securityObject' => 'ReceiptValidation',   'phplist' => 'itemUploadReceiptList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['campaign'],  'securityObject' => 'Campaign',   'phplist' => 'campaignList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['campaignNewsletter'],  'securityObject' => 'CampaignNewsletter',   'phplist' => 'campaignNewsletterList', 'target' => 'tab' ),array(1));
    pushMenuItem($menuitem , array ('label' => $class->lang['voucher'], 'securityObject' => 'Voucher',   'phplist' => 'voucherList', 'target' => 'tab' ),array(1,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['voucherTransaction'],   'securityObject' => 'VoucherTransaction',   'phplist' => 'voucherTransactionList', 'target' => 'tab' ),array(1,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['cashback'], 'securityObject' => 'PointCashback',   'phplist' => 'pointCashbackList', 'target' => 'tab' ));
    
    
    pushMenuItem($menuitem , array ('label' => $class->lang['discountScheme'],   'securityObject' => 'DiscountScheme',   'phplist' => 'discountSchemeList', 'target' => 'tab' ));
    // pushMenuItem($menuitem , array ('label' => $class->lang['rewardPoints'],   'securityObject' => $rewardsPoint->securityObject,   'phplist' => 'rewardsPointList', 'target' => 'tab' ));
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
    

    /*
    // PORTFOLIO
    $arrOthers = array ('label' => $class->lang['portfolio'], 'icon' => 'fas fa-file-archive-o');  
    $menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['portfolio'],   'securityObject' => $portfolio->securityObject,   'phplist' => 'portfolioList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['portfolioCategory'],   'securityObject' => $portfolioCategory->securityObject,   'phplist' => 'portfolioCategoryList', 'target' => 'tab' ));
    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers, true); 
    */

    // SECURITY PRIVILAGES
    $arrBusinessPartner = array ('label' => $class->lang['securityPrivileges'], 'icon' => 'fas fa-lock' );  
	$menuitem = array();
    pushMenuItem($menuitem , array ('label' => $class->lang['userPrivileges'],   'securityObject' => 'SecurityPrivileges',   'phplist' => 'securityPrivilegesList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['roleTemplate'],   'securityObject' => 'RoleTemplate',   'phplist' => 'roleTemplateList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['customCode'],   'securityObject' => 'customCode',   'phplist' => 'customCodeList', 'target' => 'tab' ));
    $arrBusinessPartner['menu'] = array();
    pushMenuItem ($arrBusinessPartner['menu'], $menuitem);  
    pushMenuItem ($arrMenu, $arrBusinessPartner); 
 

  
    // REPORT

    $reportPath = 'report/';

    $arrReport = array ('label' => $class->lang['report'], 'icon' => 'fas fa-clipboard-list');  
    $menuitem = array();
  
    // BUSSINESS PARTNER REPORT 
    $arrSubMenu = array ('label' => $class->lang['businessPartner']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeReport'],   'securityObject' => 'reportEmployee',   'phplist' => $reportPath.'reportEmployee', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['customerReport'],   'securityObject' => 'reportCustomer',   'phplist' => $reportPath.'reportCustomer', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['supplierReport'],   'securityObject' => 'reportSupplier',   'phplist' => $reportPath.'reportSupplier', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['consigneeReport'],   'securityObject' => 'reportConsignee',   'phplist' => $reportPath.'reportConsignee', 'target' => '_blank' ));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['businessCategorySuggestionReport'],   'securityObject' => 'ReportBusinessCategorySuggestion',   'phplist' => $reportPath.'reportBusinessCategorySuggestion', 'target' => '_blank' ));
//    pushMenuItem($submenuitem , array ('label' => 'Laporan ILC Member',   'securityObject' => 'reportILCMember',   'phplist' => $reportPath.'reportILCMember', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['buildingUnitReport'],   'securityObject' => 'reportBuildingUnit',   'phplist' => $reportPath.'reportBuildingUnit', 'target' => '_blank' ));
   
	pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 
 
  	// MEETING REEPORT
//    $arrSubMenu = array ('label' => $class->lang['meeting']);  
//    $submenuitem = array();
//    $arrSubMenu['menu'] = array(); 
//    pushMenuItem($submenuitem , array ('label' => $class->lang['meetingScheduleReport'],   'securityObject' => 'reportMeetingSchedule',   'phplist' => $reportPath.'reportMeetingSchedule', 'target' => '_blank' ));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['meetingPointReport'],   'securityObject' => 'reportMeetingPoint',   'phplist' => $reportPath.'reportMeetingPoint', 'target' => '_blank' ));
//    pushMenuItem($submenuitem , array ('label' => $class->lang['meetingPointSuggestionReport'],   'securityObject' => 'reportMeetingPointSuggestion',   'phplist' => $reportPath.'reportMeetingPointSuggestion', 'target' => '_blank' ));
//    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
//    pushMenuItem($menuitem , $arrSubMenu); 
 

    $arrSubMenu = array ('label' => $class->lang['logistic']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['contractReport'],   'securityObject' => 'reportDisposalContract',   'phplist' => $reportPath.'reportDisposalContract', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderReport'],   'securityObject' => 'reportDisposalJobOrder',   'phplist' => $reportPath.'reportDisposalJobOrder', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['workOrderDispatcherReport'],   'securityObject' => 'reportDisposalWork',   'phplist' => $reportPath.'reportDisposalWork', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['workOrderReport'],   'securityObject' => 'reportDisposalWorkOrder',   'phplist' => $reportPath.'reportDisposalWorkOrder', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 

    // INVENTORY
    $arrSubMenu = array ('label' => $class->lang['inventory']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemReport'],   'securityObject' => 'reportItem',   'phplist' => $reportPath.'reportItem', 'target' => '_blank' ), array(1,2,3,5,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceReport'],   'securityObject' => 'reportServices',   'phplist' => $reportPath.'reportServices', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemPackageReport'],   'securityObject' => 'reportItem',   'phplist' => $reportPath.'reportItemPackage', 'target' => '_blank' ), array(1,2,3,5));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['itemFilterReport'],   'securityObject' => 'reportItemFilter',   'phplist' => 'reportItemFilter', 'target' => '_blank' ),true);
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemInReport'],   'securityObject' => 'reportItemIn',   'phplist' => $reportPath.'reportItemIn', 'target' => '_blank' ), array(1,2,3,5,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemOutReport'],   'securityObject' => 'reportItemOut',   'phplist' => $reportPath.'reportItemOut', 'target' => '_blank' ), array(1,2,3,5,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemAdjustmentReport'],   'securityObject' => 'reportItemAdjustment',   'phplist' => $reportPath.'reportItemAdjustment', 'target' => '_blank' ), array(1,2,3,5,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['warehouseTransferReport'],   'securityObject' => 'reportWarehouseTransfer',   'phplist' => $reportPath.'reportWarehouseTransfer', 'target' => '_blank' ), array(1,2,3,5,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemAgingReport'],   'securityObject' => 'reportItem',   'phplist' => $reportPath.'reportItemAging', 'target' => '_blank' ), array(1,2,3,5,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['stockCardReport'],   'securityObject' => 'reportStockCard',   'phplist' => $reportPath.'reportStockCard', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['snMovementReport'],   'securityObject' => 'reportItemMovementSN',   'phplist' => $reportPath.'reportItemMovementSN', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array('label' => $class->lang['packagingCodeReport'], 'securityObject' => 'reportPackagingCode', 'phplist' => $reportPath . 'reportPackagingCode', 'target' => '_blank'));
    pushMenuItem($submenuitem , array ('label' => 'SN Gudang',   'securityObject' => 'reportItemMovementSN',   'phplist' => $reportPath.'reportSNInWarehouse', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

	// ASSEMBLY REEPORT
    $arrSubMenu = array ('label' => $class->lang['assembly']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['billOfMaterialsReport'],   'securityObject' => 'reportBillOfMaterials',   'phplist' => $reportPath.'reportBillOfMaterials', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['assemblyReport'],   'securityObject' => 'reportAssembly',   'phplist' => $reportPath.'reportAssembly', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 

    // PURCHASE REPORT 
    $arrSubMenu = array ('label' => $class->lang['purchase']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderReport'],   'securityObject' => 'reportPurchaseOrder',   'phplist' => $reportPath.'reportPurchaseOrder', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderImportReport'],   'securityObject' => 'reportPurchaseOrderImportFF',   'phplist' => $reportPath.'reportEMKLPurchaseOrderImport', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderExportReport'],   'securityObject' => 'reportPurchaseOrderExportFF',   'phplist' => $reportPath.'reportEMKLPurchaseOrderExport', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderDomesticReport'],   'securityObject' => 'reportPurchaseOrderDomesticFF',   'phplist' => $reportPath.'reportEMKLPurchaseOrderDomestic', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderWarehouseReport'],   'securityObject' => 'reportPurchaseOrderWarehouseFF',   'phplist' => $reportPath.'reportEMKLPurchaseOrderWarehouse', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseOrderTruckingReport'],   'securityObject' => 'reportPurchaseOrderTruckingFF',   'phplist' => $reportPath.'reportEMKLPurchaseOrderTrucking', 'target' => '_blank' ), array(2,5));

    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseReceiveReport'],   'securityObject' => 'reportPurchaseReceive',   'phplist' => $reportPath.'reportPurchaseReceive', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['purchaseRefundReport'],   'securityObject' => 'ReportEMKLCommission',   'phplist' => $reportPath.'reportEMKLCommission', 'target' => '_blank' ), array(2,5)); 	
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingPurchaseUnInvoicedReport'],   'securityObject' => 'ReportTruckingPurchaseUnInvoiced',   'phplist' => $reportPath.'reportTruckingPurchaseUnInvoiced', 'target' => '_blank' ), array(2)); 	
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 

 
    // SALES REPORT 
    $arrSubMenu = array ('label' => $class->lang['sales']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 

	if (DOMAIN_NAME != 'niagara.wintera.co.id')
		pushMenuItem($submenuitem, array('label' => $class->lang['importQuotaRealizationReport'], 'securityObject' => 'reportExportQuotaRealization', 'phplist' => $reportPath . 'reportRealizationQuotaImport', 'target' => '_blank'), array(2, 5));

	pushMenuItem($submenuitem, array('label' => $class->lang['exportQuotaRealizationReport'], 'securityObject' => 'reportImportQuotaRealization', 'phplist' => $reportPath . 'reportRealizationQuotaExport', 'target' => '_blank'), array(2, 5));



    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrder', 'target' => '_blank' ), array(1,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['monthlySalesSummaryReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportMonthlySalesSummary', 'target' => '_blank' ), array(1,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['shipmentManifestReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrderForShipment', 'target' => '_blank' ), array(1,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderByGroupReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrderItem', 'target' => '_blank' ), array(1,9));
    pushMenuItem($submenuitem , array ('label' => $class->lang['rentalTimesheetReport'],   'securityObject' => 'reportRentalTimesheet',   'phplist' => $reportPath.'reportRentalSchedule', 'target' => '_blank' ));
       
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderDumperReport'],   'securityObject' => 'reportSalesOrderDumper',   'phplist' => $reportPath.'reportSalesOrderDumper', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderSubscriptionReport'],   'securityObject' => 'reportSalesOrderSubscription',   'phplist' => $reportPath.'reportSalesOrderSubscription', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['installationWorkOrderReport'],   'securityObject' => 'reportInstallationWorkOrder',   'phplist' => $reportPath.'reportInstallationWorkOrder', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['installationBASTReport'],   'securityObject' => 'reportInstallationBAST',   'phplist' => $reportPath.'reportInstallationBAST', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['invoiceOrderSubscriptionReport'],   'securityObject' => 'reportInvoiceOrderSubscription',   'phplist' => $reportPath.'reportInvoiceOrderSubscription', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ticketSupportReport'],   'securityObject' => 'reportTicketSupport',   'phplist' => $reportPath.'reportTicketSupport', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ticketSupportWorkOrderReport'],   'securityObject' => 'reportTicketSupportWorkOrder',   'phplist' => $reportPath.'reportTicketSupportWorkOrder', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['logisticSalesOrderReport'],   'securityObject' => 'reportLogisticSalesOrder',   'phplist' => $reportPath.'reportLogisticSalesOrder', 'target' => '_blank' ));
    
    // FF
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderImportReport'] ,   'securityObject' => 'reportEmklJobOrderHeaderImport',   'phplist' => $reportPath.'reportEMKLJobOrderImportHeader', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderImportReport'] ,   'securityObject' => 'reportSalesOrderImportFF',   'phplist' => $reportPath.'reportEMKLJobOrderImport', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderExportReport'] ,   'securityObject' => 'reportEmklJobOrderHeaderExport',   'phplist' => $reportPath.'reportEMKLJobOrderExportHeader', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderExportReport'] ,   'securityObject' => 'reportSalesOrderExportFF',   'phplist' => $reportPath.'reportEMKLJobOrderExport', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderHeaderDomesticReport'] ,   'securityObject' => 'reportEmklJobOrderHeaderDomestic',   'phplist' => $reportPath.'reportEMKLJobOrderDomesticHeader', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderDomesticReport'] ,   'securityObject' => 'reportSalesOrderDomesticFF',   'phplist' => $reportPath.'reportEMKLJobOrderDomestic', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderWarehouseReport'] ,   'securityObject' => 'reportSalesOrderWarehouseFF',   'phplist' => $reportPath.'reportEMKLJobOrderWarehouse', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderTruckingReport'] ,   'securityObject' => 'reportSalesOrderTruckingFF',   'phplist' => $reportPath.'reportEMKLJobOrderTrucking', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceOrderInvoiceReport'],   'securityObject' => 'reportSalesOrderInvoiceFF',   'phplist' => $reportPath.'reportEMKLSalesOrderInvoice', 'target' => '_blank' ), array(2,5));
	pushMenuItem($submenuitem , array ('label' => $class->lang['uninvoicedSOReport'] ,   'securityObject' => 'reportSalesOrderExportFF',   'phplist' => $reportPath.'reportEMKLUninvoicedJobOrder', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderToInvoiceReport'] ,   'securityObject' => 'reportSalesOrderExportFF',   'phplist' => $reportPath.'reportEMKLJobOrderToInvoice', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['transactionCashFlowReport'],   'securityObject' => 'reportTransactionCashFlow',   'phplist' => $reportPath.'reportEMKLTransactionCashFlow', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesDeliveryReport'],   'securityObject' => 'reportSalesDelivery',   'phplist' => $reportPath.'reportSalesDelivery', 'target' => '_blank' ), array(1,2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceiptReport'],   'securityObject' => 'reportEMKLInvoiceReceipt',   'phplist' => $reportPath.'reportEMKLInvoiceReceipt', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderSummaryReport'] ,   'securityObject' => 'reportSalesOrderSummaryFF',   'phplist' => $reportPath.'reportEMKLSalesOrderSummary', 'target' => '_blank' ), array(2,5));
    
    // buat CIF
    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderSummaryReport'], 'securityObject' => 'reportEMKLJobOrderSummary', 'phplist' => $reportPath . 'reportEMKLJobOrderSummary', 'target' => '_blank'), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['liftingReport'], 'securityObject' => 'reportEMKLLifting', 'phplist' => $reportPath . 'reportEMKLLifting', 'target' => '_blank'), array(2,5));
    
    // service mobil
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderReport'],   'securityObject' => 'reportSalesOrder',   'phplist' => $reportPath.'reportSalesOrderCarService', 'target' => '_blank' ), array(2,3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesReturnReport'],   'securityObject' => 'reportSalesCarServiceReturn',   'phplist' => $reportPath.'reportSalesCarServiceReturn', 'target' => '_blank' ), array(2,3));

    pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderReport'],   'securityObject' => 'reportTruckingServiceOrder',   'phplist' => $reportPath.'reportTruckingServiceOrder', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['workOrderReport'],   'securityObject' => 'reportTruckingServiceWorkOrder',   'phplist' => $reportPath.'reportTruckingServiceWorkOrder', 'target' => '_blank' ), array(2));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['workOrderCostReport'],   'securityObject' => 'reportTruckingServiceWorkOrder',   'phplist' => 'reportTruckingServiceWorkOrderCost', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costReport'],   'securityObject' => 'reportTruckingCost',   'phplist' => $reportPath.'reportTruckingCost', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['monthlySalesReport'],   'securityObject' => 'reportTruckingServiceOrder',   'phplist' => $reportPath.'reportMonthlySales', 'target' => '_blank' ), array(2));
  	pushMenuItem($submenuitem , array ('label' => $class->lang['grossPLReport'],   'securityObject' => 'reportTruckingServiceOrder',   'phplist' => $reportPath.'reportGrossPNLTrucking', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['grossPLReport'],   'securityObject' => 'reportGrossPNLFF',   'phplist' => $reportPath.'reportGrossPNLFF', 'target' => '_blank' ), array(2,5));
    
    // sementara khusus CIF
    if(DOMAIN_NAME == 'cif.wintera.co.id')
    pushMenuItem($submenuitem , array ('label' => $class->lang['grossPLReport'] .' (Invoice)',   'securityObject' => 'reportGrossPNLFF',   'phplist' => $reportPath.'reportGrossPNLFFInvoice', 'target' => '_blank' ), array(5));

    pushMenuItem($submenuitem , array ('label' => $class->lang['sellingRateReport'],   'securityObject' => 'reportSellingRate',   'phplist' => $reportPath.'reportTruckingSellingRate', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costRateReport'],   'securityObject' => 'reportCostRate',   'phplist' => $reportPath.'reportTruckingCostRate', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ritaseSummaryReport'],   'securityObject' => 'reportRitaseSummary',   'phplist' => $reportPath.'reportRitaseSummary', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingServiceOrderByVehicleReport'],   'securityObject' => 'reportTruckingServiceOrderByVehicle',   'phplist' => $reportPath.'reportTruckingServiceOrderByVehicle', 'target' => '_blank' ), array(2)); 	
    
	pushMenuItem($submenuitem , array ('label' => $class->lang['emklReminderJobOrderReport'] ,   'securityObject' => 'EMKLReminderJobOrder',   'phplist' => $reportPath.'reportEMKLReminderJobOrder', 'target' => '_blank' ), array(2));
   

	pushMenuItem($submenuitem , array ('label' => $class->lang['newRequestReport'],   'securityObject' => 'ReportMedicalRequestClaim',   'phplist' => $reportPath.'reportMedicalRequestClaim', 'target' => '_blank' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['jobOrderReport'],   'securityObject' => 'ReportMedicalJobOrder',   'phplist' => $reportPath.'reportMedicalJobOrder', 'target' => '_blank' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['priceQuotationReport'],   'securityObject' => 'ReportMedicalSalesOrderQuotation',   'phplist' => $reportPath.'reportMedicalSalesOrderQuotation', 'target' => '_blank' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['guaranteeLetterReport'],   'securityObject' => 'ReportMedicalPurchaseOrder',   'phplist' => $reportPath.'reportMedicalPurchaseOrder', 'target' => '_blank' ));
	pushMenuItem($submenuitem , array ('label' => $class->lang['serviceOrderInvoiceReport'],   'securityObject' => 'ReportMedicalSalesInvoice',   'phplist' => $reportPath.'reportMedicalSalesInvoice', 'target' => '_blank' ));

	pushMenuItem($submenuitem , array ('label' => $class->lang['membershipSubscriptionnReport'],   'securityObject' => 'reportMembershipSubscription',   'phplist' => $reportPath.'reportMembershipSubscription', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['monthlySubscriptionnReport'],   'securityObject' => 'reportMembershipSubscription',   'phplist' => $reportPath.'reportMonthlySubscription', 'target' => '_blank' )); 
    
    pushMenuItem($submenuitem , array ('label' => 'Laporan Biaya per Sopir',   'securityObject' => 'reportCostByDriver',   'phplist' => $reportPath.'reportCostByDriver', 'target' => '_blank' ), array(2)); 	
	
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 



    // DEPOT REPORT 
    $arrSubMenu = array ('label' => $class->lang['depot']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 

    pushMenuItem($submenuitem , array ('label' => $class->lang['itemInReport'],   'securityObject' => 'reportItemInDepot',   'phplist' => $reportPath.'reportItemInDepot', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['itemOutReport'],   'securityObject' => 'reportItemOutDepot',   'phplist' => $reportPath.'reportItemOutDepot', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['stockCardDepotReport'],   'securityObject' => 'reportStockCardDepot',   'phplist' => $reportPath.'reportStockCardDepot', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['depotItemMovementReport'],   'securityObject' => 'reportStockCardDepot',   'phplist' => $reportPath.'reportItemMovementDepot', 'target' => '_blank' ), array(2));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 
 
 
 // ASSET REPORT 
    $arrSubMenu = array ('label' => $class->lang['asset']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 

    pushMenuItem($submenuitem , array ('label' => $class->lang['assetReport'],   'securityObject' => 'reportAsset',   'phplist' => $reportPath.'reportAsset', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['assetPurchaseReport'],   'securityObject' => 'reportAssetPurchase',   'phplist' => $reportPath.'reportAssetPurchase', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['assetDepreciationReport'],   'securityObject' => 'reportAssetDepreciation',   'phplist' => $reportPath.'reportAssetDepreciation', 'target' => '_blank' ), array(2));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 

    // FINANCE REPORT
    $arrSubMenu = array ('label' => $class->lang['salesInvoice']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['serviceOrderInvoiceReport'],   'securityObject' => 'reportTruckingServiceOrderInvoice',   'phplist' => $reportPath.'reportTruckingServiceOrderInvoice', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesInvoiceReport'],   'securityObject' => 'reportDisposalSalesInvoice',   'phplist' => $reportPath.'reportDisposalSalesInvoice', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['salesOrderInvoiceReceiptReport'],   'securityObject' => 'reportSalesOrderInvoiceReceipt',   'phplist' => $reportPath.'reportSalesOrderInvoiceReceipt', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrSubMenu = array ('label' => $class->lang['downpayment']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['customerDownpaymentReport'],   'securityObject' => 'reportCustomerDownpayment',   'phplist' => $reportPath.'reportCustomerDownpayment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['supplierDownpaymentReport'],   'securityObject' => 'reportSupplierDownpayment',   'phplist' => $reportPath.'reportSupplierDownpayment', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 

    $arrSubMenu = array ('label' => $class->lang['cashBank']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
    pushMenuItem($submenuitem , array ('label' => $class->lang['garageCashVoucherReport'],   'securityObject' => 'reportCashBankTrucking',   'phplist' => $reportPath.'reportCashBankVoucherTrucking', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['maintenanceCashVoucherReport'],   'securityObject' => 'reportCashBankMaintenance',   'phplist' => $reportPath.'reportCashBankVoucherMaintenance', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvanceReport'],   'securityObject' => 'reportCashAdvance',   'phplist' => $reportPath.'reportCashAdvance', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashAdvanceRealizationReport'],   'securityObject' => 'reportCashAdvanceRealization',   'phplist' => $reportPath.'reportCashAdvanceRealization', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankInReport'],   'securityObject' => 'reportCashBankIn',   'phplist' => $reportPath.'reportCashBankIn', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashAndBankVoucherReport'],   'securityObject' => 'reportCashBankVoucher',   'phplist' => $reportPath.'reportCashBankVoucher', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashInReport'],   'securityObject' => 'reportCashIn',   'phplist' => $reportPath.'reportCashIn', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashOutReport'],   'securityObject' => 'reportCashOut',   'phplist' => $reportPath.'reportCashOut', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankTransferReport'],   'securityObject' => 'reportCashBankTransfer',   'phplist' => $reportPath.'reportCashBankTransfer', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankRealizationReport'],   'securityObject' => 'reportCashBankRealization',   'phplist' => $reportPath.'reportCashBankRealization', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['bankReconsiliationReport'],   'securityObject' => 'reportBankReconsiliation',   'phplist' => $reportPath.'reportBankReconsiliation', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cashBankCardReport'],   'securityObject' => 'reportCashBankCard',   'phplist' => $reportPath.'reportCashBankCard', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['pettyCashReport'],   'securityObject' => 'reportPettyCash',   'phplist' => $reportPath.'reportPettyCash', 'target' => '_blank' ));
    
    pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCostCashOutReport'],   'securityObject' => 'reportTruckingCostCashOut',   'phplist' => $reportPath.'reportTruckingCostCashOut', 'target' => '_blank' ), array(2));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['truckingCashFlowReportReport'],   'securityObject' => 'reportTruckingCostCashOut',   'phplist' => 'reportTruckingCashFlow', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['dailyCashReport'],   'securityObject' => 'reportDailyCash',   'phplist' => $reportPath.'reportDailyCash', 'target' => '_blank' ));
  
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);


    $arrSubMenu = array ('label' => $class->lang['accountsPayable']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['APReport'],   'securityObject' => 'reportAP',   'phplist' => $reportPath.'reportAP', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APPaymentReport'],   'securityObject' => 'reportAPPayment',   'phplist' =>  $reportPath.'reportAPPayment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APAgingReport'],   'securityObject' => 'reportAP',   'phplist' =>  $reportPath.'reportAPAging' , 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APCardReport'],   'securityObject' => 'reportAP',   'phplist' => $reportPath.'reportAPCard', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23Report'],   'securityObject' => 'reportAPPayableTax23',   'phplist' => $reportPath.'reportAPPayableTax23', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23PaymentReport'],   'securityObject' => 'reportAPPayableTax23Payment',   'phplist' => $reportPath.'reportAPPayableTax23Payment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23PaymentReport'] . ' (Template)',   'securityObject' => 'reportAPPayableTax23Payment',   'phplist' => $reportPath.'reportAPPayableTax23PaymentTemplate', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['payableTax23AgingReport'],   'securityObject' => 'reportAPPayableTax23',   'phplist' =>  $reportPath.'reportAPPayableTax23Aging' , 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);


    $arrSubMenu = array ('label' => $class->lang['commissionAP']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['apCommissionReport'],   'securityObject' => 'reportAPCommission',   'phplist' => $reportPath.'reportAPCommission', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['apCommissionPaymentReport'],   'securityObject' => 'reportAPCommissionPayment',   'phplist' => $reportPath.'reportAPCommissionPayment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APCommissionCardReport'].' ('.$class->lang['supplier'].')',   'securityObject' => 'reportAPCommission',   'phplist' => $reportPath.'reportAPCardCommissionSupplier', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['apCustomerCommissionReport'],   'securityObject' => 'reportAPCustomerCommission',   'phplist' => $reportPath.'reportAPCustomerCommission', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['APCommissionReviewReport']  ,   'securityObject' => 'reportAPCommission',   'phplist' => $reportPath.'reportEmklJobOrderCommission', 'target' => '_blank' ), array(2,5));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);



    $arrSubMenu = array ('label' => $class->lang['accountsReceivable']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARReport'],   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportAR', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARPaymentReport'],   'securityObject' => 'reportARPayment',   'phplist' => $reportPath.'reportARPayment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARAgingReport'],   'securityObject' => 'reportAR',   'phplist' =>  $reportPath.'reportARAging' , 'target' => '_blank' ));
    // tset di logol dulu
//    if(DOMAIN_NAME == 'logol.wintera.co.id')
//        pushMenuItem($submenuitem , array ('label' => $class->lang['ARSOAReport'],   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportARSOA', 'target' => '_blank' ));
    
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARCardReport'].' ('.$class->lang['transaction'].')',   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportARCard', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARCardReport'].' ('.$class->lang['customer'].')',   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportARCardCustomer', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['statementOfAccountReport'],   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportStatementOfAccount', 'target' => '_blank' ), array(2,5));
	pushMenuItem($submenuitem , array ('label' => $class->lang['ARSOAReport'],   'securityObject' => 'reportAR',   'phplist' => $reportPath.'reportEMKLARSOA', 'target' => '_blank' ), array(2,5));
	
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23Report'],   'securityObject' => 'reportARPrepaidTax23',  'phplist' => $reportPath.'reportARPrepaidTax23', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23PaymentReport'],   'securityObject' => 'reportARPrepaidTax23Payment',   'phplist' => $reportPath.'reportARPrepaidTax23Payment', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23PaymentReport'] . ' (Template)',   'securityObject' => 'reportARPrepaidTax23Payment',   'phplist' => $reportPath.'reportARPrepaidTax23PaymentTemplate', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidTax23AgingReport'],   'securityObject' => 'reportARPrepaidTax23',  'phplist' => $reportPath.'reportARPrepaidTax23Aging', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['ARAPCashflowReport'],   'securityObject' => 'reportARAPCashflow',  'phplist' => $reportPath.'reportARAPCashflow', 'target' => '_blank' ));
    
 
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);
 

    $arrSubMenu = array ('label' => $class->lang['employeeAccountsReceivable']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAccountsReceivableReport'],   'securityObject' => 'reportAREmployee',   'phplist' => $reportPath.'reportAREmployee', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAccountsReceivablePaymentReport'],   'securityObject' => 'reportAREmployeePayment',   'phplist' => $reportPath.'reportAREmployeePayment', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrSubMenu = array ('label' => $class->lang['employeeCommission']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommissionReport'],   'securityObject' => 'reportAPEmployeeCommission',   'phplist' => $reportPath.'reportAPEmployeeCommission', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['employeeCommissionPaymentReport'],   'securityObject' => 'reportAPEmployeeCommissionPayment',   'phplist' => $reportPath.'reportAPEmployeeCommissionPayment', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu);
  

    // PREPAID COST REEPORT
    $arrSubMenu = array ('label' => $class->lang['prepaidCost']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['prepaidCostReport'],   'securityObject' => 'ReportPrepaidExpense',   'phplist' => $reportPath.'reportPrepaidExpense', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costReconsiliationReport'],   'securityObject' => 'ReportCostReconsile',   'phplist' => $reportPath.'reportCostReconsile', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 
 
    $arrSubMenu = array ('label' => $class->lang['accounting']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['generalJournalReport'],   'securityObject' => 'reportGeneralJournal',   'phplist' => $reportPath.'reportGeneralJournal', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['generalLedgerReport'],   'securityObject' => 'reportGeneralLedger',   'phplist' => $reportPath.'reportGeneralLedger', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['balanceSheetReport'],   'securityObject' => 'reportBalanceSheet',   'phplist' => $reportPath.'reportBalanceSheet', 'target' => '_blank' ));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['balanceSheetReport'] . ' (Beta)',   'securityObject' => 'reportBalanceSheet',   'phplist' => $reportPath.'reportBalanceSheet2', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['trialBalanceReport'],   'securityObject' => 'reportTrialBalance',   'phplist' => $reportPath.'reportTrialBalance', 'target' => '_blank' ));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['incomeStatementReport'],   'securityObject' => 'reportIncomeStatement',   'phplist' => $reportPath.'reportIncomeStatement', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['incomeStatementReport'],   'securityObject' => 'reportIncomeStatement',   'phplist' => $reportPath.'reportIncomeStatement', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['costReport'],   'securityObject' => 'reportCost',   'phplist' => $reportPath.'reportCost', 'target' => '_blank' ));
    //pushMenuItem($submenuitem , array ('label' => $class->lang['cashFlowReport'],   'securityObject' => 'reportCashFlow',   'phplist' => 'reportCashFlow', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['financialSummaryReport'],   'securityObject' => 'reportFinancialSummary',   'phplist' => $reportPath.'reportFinancialSummary', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 


  	// OTHERS REPORT 
    $arrSubMenu = array ('label' => $class->lang['HRD']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 

	pushMenuItem($submenuitem , array ('label' => $class->lang['employeeAttendanceReport'],   'securityObject' => 'reportEmployeeAttendance',   'phplist' => $reportPath.'reportEmployeeAttendance', 'target' => '_blank' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem);  
    pushMenuItem($menuitem , $arrSubMenu); 


     // OTHERS REPORT 
    $arrSubMenu = array ('label' => $class->lang['others']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 
    pushMenuItem($submenuitem , array ('label' => $class->lang['cityReport'],   'securityObject' => 'reportCity',   'phplist' => $reportPath.'reportCity', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['cityCategoryReport'],   'securityObject' => 'reportCityCategory',   'phplist' => $reportPath.'reportCityCategory', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carReport'],   'securityObject' => 'reportCar',   'phplist' => $reportPath.'reportCar', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carScheduleReport'],   'securityObject' => 'reportCarSchedule',   'phplist' => $reportPath.'reportCarSchedule', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['vehicleAvailabilityReport'],   'securityObject' => 'reportVehicleAvailability',   'phplist' => $reportPath.'reportVehicleAvailability', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carMaintenanceReport'],   'securityObject' => 'reportCarServiceMaintenance',   'phplist' => $reportPath.'reportCarServiceMaintenance', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['maintenanceSummaryReport'],   'securityObject' => 'reportCarServiceMaintenance',   'phplist' => $reportPath.'reportMaintenanceSummary', 'target' => '_blank' ), array(2));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carMaintenanceHistoryReport'],   'securityObject' => 'reportCarMaintenanceSalesHistory',   'phplist' => $reportPath.'reportCarMaintenanceSalesHistory', 'target' => '_blank' ), array(3));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carMaintenanceHistoryReport'],   'securityObject' => 'reportCarMaintenanceHistory',   'phplist' => $reportPath.'reportCarMaintenanceHistory', 'target' => '_blank' ), array(2,5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['carTurnoverReport'],   'securityObject' => 'reportCarTurnOver',   'phplist' => $reportPath.'reportCarTurnover', 'target' => '_blank' ), array(2));
    
    // sementar khusus MTI
    if(in_array(DOMAIN_NAME, array('mti.wintera.co.id', 'tcl.wintera.co.id')))
        pushMenuItem($submenuitem , array ('label' => $class->lang['carSummaryTurnoverReport'],   'securityObject' => 'reportCarTurnOver',   'phplist' => $reportPath.'reportCarSummaryTurnover', 'target' => '_blank' ), array(2));
    
    pushMenuItem($submenuitem , array ('label' => $class->lang['warrantyClaimProgressReport'],   'securityObject' => 'reportWarrantyClaimProgress',   'phplist' => $reportPath.'reportWarrantyClaimProgress', 'target' => '_blank' ), array(5));
    pushMenuItem($submenuitem , array ('label' => $class->lang['loginLogReport'],   'securityObject' => 'reportLoginLog',   'phplist' => $reportPath.'reportLoginLog', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['transactionLogReport'],   'securityObject' => 'reportTransactionLog',   'phplist' => $reportPath.'reportTransactionLog', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['securityPrivilegesReport'],   'securityObject' => 'reportSecurityPrivileges',   'phplist' => $reportPath.'reportSecurityPrivileges', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['marketplaceLogReport'],   'securityObject' => 'reportMarketplaceLog',   'phplist' => $reportPath.'reportMarketplaceLog', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['timespanReport'],   'securityObject' => 'reportTimespan',   'phplist' => $reportPath.'reportTimespan', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['voucherReport'],   'securityObject' => 'reportVoucher',  'phplist' => $reportPath.'reportVoucher', 'target' => '_blank' ));
    pushMenuItem($submenuitem , array ('label' => $class->lang['activityLog'],   'securityObject' => 'reportActivityTransactionLog',   'phplist' => $reportPath.'reportMedicalRequestClaimHistory', 'target' => '_blank' ), array(1,4));
    pushMenuItem($submenuitem , array ('label' => $class->lang['dailyReport'],   'securityObject' => 'reportDaily',   'phplist' => $reportPath.'reportDaily', 'target' => '_blank' ), array(2));
     

	if($arrActiveModule['emkljoborder'] && $showEximServices == 1)
		pushMenuItem($submenuitem , array ('label' => $class->lang['servicesCOALink'],   'securityObject' => 'ChartOfAccount',  'phplist' => $reportPath.'reportServicesCoaLink', 'target' => '_blank' ));
    
	if (PLAN_TYPE['usefrontend'] == 1){
        pushMenuItem($submenuitem , array ('label' => $class->lang['newsletterSubscriptionReport'],   'securityObject' => 'ReportNewsletterSubscription',   'phplist' => $reportPath.'reportNewsletterSubscription', 'target' => '_blank' ), array(1));
        pushMenuItem($submenuitem , array ('label' => $class->lang['customerIssueReport'],   'securityObject' => 'customerIssueReport',   'phplist' => $reportPath.'reportCustomerIssue', 'target' => '_blank' ), array(1));

    }

	pushMenuItem($submenuitem , array ('label' => $class->lang['apiLogReport'],  'securityObject' => 'reportAPILog',   'phplist' => $reportPath.'reportAPILog', 'target' => '_blank' ));
    

    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 
   
 

    $arrReport['menu'] = array();
    pushMenuItem ($arrReport['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrReport); 
  

    // OTHERS
    $arrOthers = array ('label' => $class->lang['others'], 'icon' => 'fas fa-ellipsis-h');  
    $menuitem = array();     
  
    //pushMenuItem($menuitem , array ('label' => $class->lang['snInformation'],   'securityObject' => $item->securityObject,   'phplist' => 'snInformation', 'target' => 'tab' ), array(5));
    //pushMenuItem($menuitem , array ('label' => $class->lang['changeItemSN'],   'securityObject' => $changeItemSN->securityObject,   'phplist' => 'changeItemSNList', 'target' => 'tab' ), array(5));
    pushMenuItem($menuitem , array ('label' => $class->lang['container'],   'securityObject' => 'container',   'phplist' => 'containerList', 'target' => 'tab' ), array(2,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['depotList'],   'securityObject' => 'Depot',   'phplist' => 'depotList', 'target' => 'tab' ), array(2,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['portList'],   'securityObject' => 'Port',   'phplist' => 'portList', 'target' => 'tab' ), array(2,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['terminalList'],   'securityObject' => 'Terminal',   'phplist' => 'terminalList', 'target' => 'tab' ), array(2));
    pushMenuItem($menuitem , array ('label' => $class->lang['vesselList'],   'securityObject' => 'vessel',   'phplist' => 'vesselList', 'target' => 'tab' ), array(2,5));
    //pushMenuItem($menuitem , array ('label' => $class->lang['warrantyPeriod'],   'securityObject' => $warrantyPeriod->securityObject,   'phplist' => 'warrantyPeriodList', 'target' => 'tab' ), array(5));
    pushMenuItem($menuitem , array ('label' => $class->lang['itemChecklist'],   'securityObject' => 'itemChecklist',   'phplist' => 'itemChecklistList', 'target' => 'tab' ), array(1,2,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['itemChecklistGroup'],   'securityObject' => 'ItemChecklistGroup',   'phplist' => 'itemChecklistGroupList', 'target' => 'tab' ), array(1,5));
    pushMenuItem($menuitem , array ('label' => $class->lang['issueCategory'],   'securityObject' => 'IssueCategory',   'phplist' => 'issueCategoryList', 'target' => 'tab' ), array(5));
    pushMenuItem($menuitem , array ('label' => $class->lang['vehicleChecklist'],   'securityObject' => 'CarChecklist',   'phplist' => 'carChecklistList', 'target' => 'tab' ), array(2));

    pushMenuItem($menuitem , array ('label' => $class->lang['commodityList'],   'securityObject' => 'Commodity',   'phplist' => 'commodityList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['commodityType'],   'securityObject' => 'CommodityType',   'phplist' => 'commodityTypeList', 'target' => 'tab' ));

    pushMenuItem($menuitem , array ('label' => $class->lang['shipment'],   'securityObject' => 'Shipment',   'phplist' => 'shipmentList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['continent'],   'securityObject' => 'Continent',   'phplist' => 'continentList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['country'],   'securityObject' => 'Country',   'phplist' => 'countryList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['city'],   'securityObject' => 'City',   'phplist' => 'cityList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['cityCategory'],   'securityObject' => 'CityCategory',   'phplist' => 'cityCategoryList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['location'],   'securityObject' => 'location',   'phplist' => 'locationList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['division'],   'securityObject' => 'division',   'phplist' => 'divisionList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['faq'],   'securityObject' => 'FAQ',   'phplist' => 'faqList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['cancelReason'],   'securityObject' => 'CancelReason',   'phplist' => 'cancelReasonList', 'target' => 'tab' ));
   
    pushMenuItem($menuitem , array ('label' => $class->lang['media'],   'securityObject' => 'Media',   'phplist' => 'mediaList', 'target' => 'tab' ), array(6));
    pushMenuItem($menuitem , array ('label' => $class->lang['jobDetails'],   'securityObject' => 'JobDetails',   'phplist' => 'jobDetailsList', 'target' => 'tab' ), array(6));
    pushMenuItem($menuitem , array ('label' => $class->lang['stagesProcess'],   'securityObject' => 'StagesProcess',   'phplist' => 'stagesProcessList', 'target' => 'tab' ), array(6));
    
	pushMenuItem($menuitem , array ('label' => $class->lang['diagnose'],   'securityObject' => 'diagnose',   'phplist' => 'diagnoseList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['reminder'],   'securityObject' => 'Reminder',   'phplist' => 'reminderList', 'target' => 'tab' ));
	pushMenuItem($menuitem , array ('label' => $class->lang['costGrouping'] .' ('.$class->lang['report'].')',   'securityObject' => 'CostGrouping',   'phplist' => 'costGroupingList', 'target' => 'tab' ));
    
	pushMenuItem($menuitem , array ('label' => $class->lang['termsAndConditions'],   'securityObject' => 'TermsAndConditions',   'phplist' => 'termsAndConditionsList', 'target' => 'tab' ), array(2,5));
	pushMenuItem($menuitem , array ('label' => $class->lang['notificationLetter'],   'securityObject' => 'NotificationLetter',   'phplist' => 'notificationLetterList', 'target' => 'tab' ));
    pushMenuItem($menuitem , array ('label' => $class->lang['age'],   'securityObject' => 'Age',   'phplist' => 'ageList', 'target' => 'tab' ));
     
    if (PLAN_TYPE['usefrontend'] == 1){
        pushMenuItem($menuitem , array ('label' => $class->lang['downloadList'],   'securityObject' => 'Download',   'phplist' => 'downloadList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['webpage'],   'securityObject' => 'Page',   'phplist' => 'pageList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['testimonial'],   'securityObject' => 'Testimonial',   'phplist' => 'testimonialList', 'target' => 'tab' ));
        pushMenuItem($menuitem , array ('label' => $class->lang['contactUs'],   'securityObject' => 'Contact',   'phplist' => 'contactUsList', 'target' => 'tab' )); 
        pushMenuItem($menuitem , array ('label' => $class->lang['contactUsCategory'],   'securityObject' => 'ContactCategory',   'phplist' => 'contactCategoryList', 'target' => 'tab' )); 
        pushMenuItem($menuitem , array ('label' => $class->lang['newsletterSubscription'],   'securityObject' => 'NewsletterSubscription',   'phplist' => 'subscribeList', 'target' => 'tab' ), array(1));
        pushMenuItem($menuitem , array ('label' => $class->lang['customerIssue'],   'securityObject' => 'CustomerIssue',   'phplist' => 'customerIssueList', 'target' => 'tab' )); 
    }

    //pushMenuItem($menuitem , array ('label' => $class->lang['bugReport'],   'securityObject' => '',   'phplist' => 'bugList', 'target' => 'tab' ));
 
    // SETTINGS
    $arrSubMenu = array ('label' => $class->lang['setting']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array(); 


    //pushMenuItem($submenuitem , array ('label' => $class->lang['variableSetting'],   'securityObject' => $setting->securityObject,   'phplist' => 'setting', 'target' => 'tab' ));
    $rsSettingCategory = $setting->getSettingCategory();
    for($i=0;$i<count($rsSettingCategory);$i++) 
        pushMenuItem($submenuitem , array ('label' => $rsSettingCategory[$i]['category'],   'securityObject' => 'Setting',   'phplist' => 'setting/'. $rsSettingCategory[$i]['pkey'], 'target' => 'tab' ));

    pushMenuItem($submenuitem , array ('label' => $class->lang['codeSetting'],   'securityObject' => 'AutoCode',   'phplist' => 'autoCodeForm', 'target' => 'tab' ));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 
    pushMenuItem($menuitem , $arrSubMenu); 

    // TOOLS
    $arrSubMenu = array ('label' => $class->lang['tools']);  
    $submenuitem = array();
    $arrSubMenu['menu'] = array();  
 
    pushMenuItem($submenuitem , array ('label' => $class->lang['import'].' SN',   'securityObject' => 'reportItem',   'phplist' => 'import/serialnumber', 'target' => '_blank' ),array(5));
    pushMenuItem ($arrSubMenu['menu'], $submenuitem); 

    pushMenuItem($menuitem , $arrSubMenu); 

    $arrOthers['menu'] = array();
    pushMenuItem ($arrOthers['menu'], $menuitem);   
    pushMenuItem ($arrMenu, $arrOthers); 
  

    $menu = buildMenu($arrMenu);  
 
    echo $menu; 
?>
