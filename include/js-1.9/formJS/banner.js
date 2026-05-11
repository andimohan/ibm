function Banner(tabID, opt) {
    var thisObj = this;
    var tabObj = $("#" + tabID);

    this.tabID = tabID;
 
    var fileFolder = opt.fileFolder;
    
    var fileUploaderTarget = "item-file-uploader";
    var arrFile = (opt.arrFile) ? opt.arrFile : Array();
 
    var id = tabObj.find("[name=hidId]").val();  
    
    this.rebindEl = function rebindEl() {

    }

    this.loadOnReady = function loadOnReady() {
 
            if (id) {
                createFileUploader(fileUploaderTarget, fileFolder, id, arrFile, false);
            } else {
                createFileUploader(fileUploaderTarget, fileFolder, "", "", false);
            }

            tabObj.find(".file-list").sortable({
                placeholder: "sortable-placeholder",
                stop: function (event, ui) {
                    updateItemFileArray(opt.fileUploaderTarget);
                }
            });
            tabObj.find(".file-list").disableSelection();
 
        multiLang(tabObj); 
        thisObj.rebindEl();
    }
}
