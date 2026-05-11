function TemplateActivity(tabID, data, varConstant){   
    var thisObj = this;
    var tabObj = $("#" + tabID);      
 
    this.tabID = tabID;     

    var id = tabObj.find("[name=hidId]").val();
 
    this.onChangeDataType = function onChangeDataType() 
    {
        var selDataType = tabObj.find("[name=selDataType]").val();

        if (selDataType == varConstant.INPUT_TYPE.select || selDataType == varConstant.INPUT_TYPE.checkbox) { 
            tabObj.find(".data-type-detail").show();
        } else {
            tabObj.find(".data-type-detail").hide();
        }
    }
    
    this.rebindEl = function rebindEl(){   

    }

    this.loadOnReady = function loadOnReady(){  

        if (!data['dataTypeDetail'] || data['dataTypeDetail'].length == 0) {
            addNewTemplateRow("data-type-row-template", null, null, thisObj.rebindEl);
        }
        tabObj.find("[name=selDataType]").change(function() { thisObj.onChangeDataType(); }); 
        thisObj.onChangeDataType();
        thisObj.rebindEl();
    }
}