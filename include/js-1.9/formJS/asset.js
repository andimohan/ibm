function Asset(tabID ){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;

    var id = tabObj.find("[name=hidId]").val();  

	this.updateCategoryInfo = function updateCategoryInfo(obj){  
		var aging = $('option:selected', obj).attr('rel-aging');
		var typekey = $('option:selected', obj).attr('rel-type');
		$("[name=aging]").val(aging).blur();
		$("[name=selType]").val(typekey).blur();
	}
	
    this.rebindEl = function rebindEl(){ }

    this.loadOnReady = function loadOnReady(){   
        thisObj.rebindEl(); 
        tabObj.find("[name=selCategory]").on('change',function() { thisObj.updateCategoryInfo(this) }); 
		
		tabObj.find("[name=selCategory]").change();
    }
}