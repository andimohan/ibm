<?php
require_once $_SERVER ['DOCUMENT_ROOT'].'/assets/vendor/autoload.php';
use Google\Cloud\Translate\V3\TranslationServiceClient;

class GCloud extends BaseClass{
    
   function __construct(){
		
        $this->projectId = 'icommunity-362400';
       
		parent::__construct();
		 
	}
	 
    function translate($contents,$targetLanguage='en',$opt = array()){
        // contents in Array 
        // mimeTpye per payload, karena kao di grouping, urutan contentnya berubah
         
        if(empty($contents)) return array();
        
        $arrConfig = array();
        if(isset($opt['mimeType'])) 
            $arrConfig['mimeType'] = $opt['mimeType'];
        
        
        $arrResult = array();

        $translationServiceClient = new TranslationServiceClient();

        /** Uncomment and populate these variables in your code */ 
        $formattedParent = $translationServiceClient->locationName( $this->projectId, 'global'); 
        
        try {
            $response = $translationServiceClient->translateText(
                $contents,
                $targetLanguage,
                $formattedParent,
                $arrConfig
            );

            // Display the translation for each input text provided
            foreach ($response->getTranslations() as $translation) 
                array_push($arrResult,$translation->getTranslatedText());

        } finally {
            $translationServiceClient->close();
        }

        return $arrResult;
         
    }    
    
}
?>