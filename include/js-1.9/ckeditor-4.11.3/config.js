CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode' ] },
		{ name: 'clipboard', groups: [ 'undo', 'clipboard' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ] },
        { name: 'basicstyles'},
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'styles'},
//		{ name: 'styles', groups: [ 'Styles', Format' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
//        { name: 'editing', groups: ['basicstyles'] } 
	];

	config.removeButtons = 'Save,Print,Flash,Flash,SpecialChar,Iframe,ShowBlocks,CreateDiv,Save,NewPage,DocProps,Undo,Redo,Copy,Cut,Styles';
    config.allowedContent = true; 
    config.resize_enabled = false; 
   
//    config.format_tags = 'p;h1;h2;h3;h4;h5;h6;div';
    
    config.enterMode = CKEDITOR.ENTER_BR;
    config.protectedSource.push(/<i[^>]*><\/i>/g);
};  
 