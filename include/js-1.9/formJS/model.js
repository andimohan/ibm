function Model(tabID, uploadFolder) {   
    
        var thisObj = this;
        var tabObj = $("#" + tabID);      
    
        this.tabID = tabID;    

        var id = tabObj.find("[name=hidId]").val(); 
    
        this.rebindEl = function rebindEl() {
            
        }
        
    this.loadOnReady = function loadOnReady() {
            
        for ($ctr = 0; $ctr < uploadFolder.length; $ctr++) {

            var folder = uploadFolder[$ctr]['folder'];
            var imageUploaderTarget = uploadFolder[$ctr]['imageUploaderTarget'];
            var rsImage = uploadFolder[$ctr]['rsImage'];
            var arrImage = Array();
            var arrPHPThumbHash = Array();

            if (id) {
                for ($i = 0; $i < rsImage.length; $i++) {
                    arrImage.push(rsImage[$i].file);
                    arrPHPThumbHash.push(rsImage[$i].phpthumbhash);
                }
                createImageUploader({ "tabID": tabID, "name": imageUploaderTarget }, { "folder": folder, "token": id, "arrImage": arrImage, "phpThumbHash": arrPHPThumbHash }, false);

            } else {
                createImageUploader({ "tabID": tabID, "name": imageUploaderTarget }, { "folder": folder }, false);
            }
        }

    }
            
    thisObj.rebindEl();
}