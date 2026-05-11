<?php
class BuildingUnit extends BaseClass
{

	function __construct()
	{

		parent::__construct();

		$this->tableName = 'building_unit';
		$this->tableDetailOwner = 'building_detail_owner';
		$this->tableCustomer = 'customer';
		$this->tableDetailTenant = 'building_detail_tenant';
		$this->tableStatus = 'master_status';

		$this->securityObject = 'BuildingUnit';

		// Mapping Form Detail
		$this->arrDetailOwner = array();
		$this->arrDetailOwner['pkey'] = array('hidDetailOwnerKey');
		$this->arrDetailOwner['refkey'] = array('pkey', 'ref');
		$this->arrDetailOwner['customerkey'] = array('hidOwnerCustomerKey', array('mandatory' => true));
		$this->arrDetailOwner['trdate'] = array('itemDateHomeOwner', 'date');

		// Mapping Form Detail
		$this->arrDetailTenant = array();
		$this->arrDetailTenant['pkey'] = array('hidDetailTenantKey');
		$this->arrDetailTenant['refkey'] = array('pkey', 'ref');
		$this->arrDetailTenant['customerkey'] = array('hidTenantCustomerKey', array('mandatory' => true));
		$this->arrDetailTenant['trdate'] = array('itemDateHouseTenant', 'date');
		$this->arrDetailTenant['totalresidents'] = array('totalResidents', 'decimal');

		$this->importUrl = 'import/buildingUnit';
		
		// Insert Form Detail
		$arrDetails = array();
		array_push($arrDetails, array('dataset' => $this->arrDetailOwner, 'tableName' => $this->tableDetailOwner));
		array_push($arrDetails, array('dataset' => $this->arrDetailTenant, 'tableName' => $this->tableDetailTenant));

		$this->arrData = array();
		$this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
		$this->arrData['code'] = array('code');
		$this->arrData['block'] = array('block');
		$this->arrData['unit'] = array('unit');
		$this->arrData['unitsize'] = array('unitSize', 'decimal');
		$this->arrData['trdesc'] = array('trDesc');
		$this->arrData['statuskey'] = array('selStatus');
		$this->arrData['vanumber'] = array('vaNumber');
		$this->arrData['categorykey'] = array('hidCategoryKey');
		$this->arrData['pricepersquare'] = array('pricePerSquare', 'number');
		

		$this->arrDataListAvailableColumn = array();
		array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
		array_push($this->arrDataListAvailableColumn, array('code' => 'block', 'title' => 'block', 'dbfield' => 'block', 'default' => true, 'width' => 60,'align' => 'center'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'unit', 'title' => 'unit', 'dbfield' => 'unit', 'default' => true, 'width' => 60,'align' => 'center'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'owner', 'title' => 'owner', 'dbfield' => 'ownername', 'default' => true, 'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'tenant', 'title' => 'tenant', 'dbfield' => 'tenantname', 'default' => true, 'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'size', 'title' => 'size', 'dbfield' => 'unitsize', 'default' => true, 'width' => 100,'align' => 'right','format' => 'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'totalResident', 'title' => 'totalResidents', 'dbfield' => 'totalresidents', 'default' => true,'align' => 'right', 'width' => 120));
		array_push($this->arrDataListAvailableColumn, array('code' => 'aroutstanding', 'title' => 'outstanding', 'dbfield' => 'aroutstanding', 'default' => true, 'width' => 100,'align' => 'right','format' => 'number'));		
       	array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

		// Function for Search
		$this->arrSearchColumn = array();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Blok', $this->tableName . '.block'));
		array_push($this->arrSearchColumn, array('Unit', $this->tableName . '.unit'));
//		array_push($this->arrSearchColumn, array('Tenant', $this->tableDetailTenant . '.name'));
//		array_push($this->arrSearchColumn, array('Owner', $this->tableDetailOwner . '.name'));
		$this->newLoad = true;

		$this->includeClassDependencies(
			array(
				'Category.class.php',
				'CityCategory.class.php',
				'BuildingUnitCategory.class.php',
				'Customer.class.php'
			)
		);

		$this->overwriteConfig();
	}



	// Untuk fetching data
	function getQuery()
	{

			return '
				select
					' . $this->tableName . '.*,
					' . $this->tableStatus . '.status as statusname,
					owner.name as ownername,
					tenant.name as tenantname
				from 
					' . $this->tableName . '
				left join 
				 ' . $this->tableCustomer . ' owner  ON ' . $this->tableName . '.ownerkey =  owner.pkey
				left join 
				 ' . $this->tableCustomer . ' tenant  ON ' . $this->tableName . '.tenantkey = tenant.pkey
				 ,' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
 		' . $this->criteria;

	}
 

	function validateForm($arr, $pkey = '')
	{

		$arrayToJs = parent::validateForm($arr, $pkey);

		// tambahin validasi block DAN unit gk boleh sama
		 
		return $arrayToJs;
	}
	
	
	
	function afterUpdateData($arrParam, $action)
	{
		// search ulang detail ut kupdate header
		
		$pkey = $arrParam['pkey']; 
		
		$rsOwner = $this->getOwnerDetail($pkey,'order by '.$this->tableDetailOwner.'.trdate desc, '.$this->tableDetailOwner.'.pkey desc limit 1');
		$rsTenant = $this->getTenantDetail($pkey,'order by '.$this->tableDetailTenant.'.trdate desc, '.$this->tableDetailTenant.'.pkey desc limit 1');
		
		$ownerkey = (!empty($rsOwner)) ? $rsOwner[0]['customerkey'] : 0;
		$tenantkey = (!empty($rsTenant)) ? $rsTenant[0]['customerkey'] : 0;
		$totalResident = (!empty($rsTenant)) ? $rsTenant[0]['totalresidents'] : 0;
		
		 
		
		$sql = '
			update  ' . $this->tableName . '
			set 
				ownerkey = '.$this->oDbCon->paramString($ownerkey).', 
				tenantkey = '.$this->oDbCon->paramString($tenantkey).', 
				totalresidents = '.$this->oDbCon->paramString($totalResident).'
			where 
				pkey = ' . $this->oDbCon->paramString($pkey);

		$this->oDbCon->execute($sql);
		
        
        //add and update customer
        $this->updateCustomer($arrParam,$action);

	}
	 
 
   function afterStatusChanged($rsHeader){ 
               
        $customer = new Customer();
       
        $pkey = $rsHeader[0]['pkey'];
        $rsHeader = $this->getDataRowById($pkey);
       
        $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.statuskey'),' and '.$customer->tableName.'.refbuildingunitkey = '.$this->oDbCon->paramString($pkey));
        $customerKey = $rsCustomer[0]['pkey'];
		
       
       //kalau change status harus juga keubah (?)
        if ($rsHeader[0]['statuskey'] == 2 ){ 
            $customer->changeStatus($customerKey,3);
		}else{
            $customer->changeStatus($customerKey,2);
        }
    }	 
    
    function updateAROutstanding($customerkey){
		
		// update kalo ad perubahan AR dari customer
		
        $customer = new Customer();

        $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.refbuildingunitkey',$customer->tableName.'.aroutstanding'),' and '.$customer->tableName.'.pkey = '.$this->oDbCon->paramString($customerkey));
        $outstanding = $rsCustomer[0]['aroutstanding'];
        $buildingUnitKey = $rsCustomer[0]['refbuildingunitkey'];
        
        $sql = 'update ' . $this->tableName .' set aroutstanding = ' .  $this->oDbCon->paramString($outstanding) .' where pkey = ' .  $this->oDbCon->paramString($buildingUnitKey);
        
        $this->oDbCon->execute($sql); 
        
    }
     
  
	function normalizeParameter($arrParam, $trim = false)
	{
		$arrParam = parent::normalizeParameter($arrParam, true);
		// tambahin default owner dan tenant key
		
		return $arrParam;
	}

  
    function addCustomer($arr){
        
        $customer = new Customer();
        
        $arrParam = array();
        
        $customerCode = $arr['block'].'/'.$arr['unit'];
        
        $arrParam['code'] = $customerCode;
        $arrParam['name'] = $customerCode;
        $arrParam['refbuildingunitkey'] = $arr['pkey'];
        $arrParam['virtualAccount'] = $arr['vaNumber'];
        $arrParam['islinked'] = 1; 
        $arrParam['selStatus'] = 2; //status harus sudah ke aktif
        
        $arrayToJs = $customer->addData($arrParam); 

        if (!$arrayToJs[0]['valid']){
//			 $this->setLog($arrayToJs[0]['message'],true);
			 $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
		}
            
        
    }

    
    function updateCustomer($arr,$action){
        
        $customer = new Customer();
        
        if($action == INSERT_DATA){
            $this->addCustomer($arr);

        }else if($action == UPDATE_DATA){
                
            $pkey = $arr['pkey'];
            $statuskey = $arr['statuskey'];
            $vaNumber = $arr['vaNumber'];
			$customerCode = $arr['code'];
            $customerName = $arr['block'].'/'.$arr['unit'];

			 $sql = '
				update  ' . $customer->tableName . '
				set 
					code = '.$this->oDbCon->paramString($customerCode).', 
					name = '.$this->oDbCon->paramString($customerName).',
					virtualaccount = '.$this->oDbCon->paramString($vaNumber).'
				where 
					refbuildingunitkey = ' . $this->oDbCon->paramString($pkey);
             
		   $this->oDbCon->execute($sql);
            
        }

        
    }
	function getOwnerDetail($pkey,$orderBy='')
	{

		
		// wajib pake paramstring
		
		$sql = '
				select
					' . $this->tableDetailOwner . '.*,
					' . $this->tableCustomer . '.name as customername
				from 
					' . $this->tableDetailOwner . '
				left join 
				 ' . $this->tableCustomer . '  ON ' . $this->tableDetailOwner . '.customerkey = ' . $this->tableCustomer . '.pkey
				where 
					' . $this->tableDetailOwner . '.refkey = ' . $this->oDbCon->paramString($pkey);

		if (!empty($orderBy)) $sql .= ' '. $orderBy;
			
		return $this->oDbCon->doQuery($sql);
	}

	function getTenantDetail($pkey,$orderBy='')
	{

		$sql = '
				select
					' . $this->tableDetailTenant . '.*,
					' . $this->tableCustomer . '.name as customername
				from 
					' . $this->tableDetailTenant . '
				left join 
					' . $this->tableCustomer . '  ON ' . $this->tableDetailTenant . '.customerkey = ' . $this->tableCustomer . '.pkey
				where ' . $this->tableDetailTenant . '.refkey = ' . $this->oDbCon->paramString($pkey) ;

		if (!empty($orderBy)) $sql .= ' '. $orderBy;
		return $this->oDbCon->doQuery($sql);
	}

}

?>
