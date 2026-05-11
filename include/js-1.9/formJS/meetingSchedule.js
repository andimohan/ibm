function MeetingSchedule(tabID, uploadFolder, rsImage) {
	var thisObj = this;
	var tabObj = $("#" + tabID);

	this.tabID = tabID;
	var objAndValue = new Array;
	objAndValue.push({ object: 'hidCustomerKey[]', value: 'pkey' });
	objAndValue.push({ object: 'customerName[]', value: 'name' });
	objAndValue.push({ object: 'businessName[]', value: 'mainbusinessname' });
	var objAndValueForDetailAutoComplete = objAndValue;

	this.rebindEl = function rebindEl() {
		bindAutoCompleteForTransactionDetail('customerName[]', objAndValueForDetailAutoComplete, 'ajax-customer.php?action=searchData');
	}
	this.getMeetingPoint = function getMeetingPoint() {
		var val = tabObj.find('[name=selOnlineOffline]').val();
		switch (val) {
			case '1':
				$(".offline").hide();
				$(".online").show();
				break;
			default: 
				$(".offline").show();
				$(".online").hide();
				break;
		}

	}
	this.loadOnReady = function loadOnReady() {
		tabObj.find('[name=selOnlineOffline]').on('change', function () {
			thisObj.getMeetingPoint(); 
		})
		thisObj.getMeetingPoint(); 
		thisObj.rebindEl();
	}
}
