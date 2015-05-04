<?php

require_once(KT_LIB_DIR . "/browse/advancedcolumns.inc.php");

class TutorialColumn extends AdvancedColumn {

    var $name = 'tutorial';
    var $namespace = 'wikitutorial.columntutorial.column';   // this is a unique name for this column, so kt doesn't get confused
    var $sortable = false; // we don't cover sorting
    
    // a constructor - its important that this takes no arguments.
	function TutorialColumn() {
        $this->label = _kt("Status");
    }

    function setOptions($aOptions) {
    	$this->document_text = KTUtil::arrayGet($aOptions, 'document_text', 'a document');
    	$this->folder_text = KTUtil::arrayGet($aOptions, 'folder_text', 'a folder');
    	return parent::setOptions($aOptions);
    }
    
	function renderData($aDataRow) {
         if ($aDataRow['type'] == 'folder') {
         	$folder = $aDataRow['folder'];
         	$url = KTUtil::addQueryString($_SERVER['PHP_SELF'], sprintf('fFolderId=%d', $folder->getId()));
         	$url = sprintf('<a href="%s&action=detail">Voir le detail</a>', $url);
         	return $url;
         	/*$listing = $folder->get_full_listing();
			$nbChildDoc = 0;
         	foreach($listing as $val) {
         		$nbChildDoc = $nbChildDoc + 1;
         	}
			$nbExpecDoc = 12;
			return sprintf("%02.1f %%", $nbChildDoc / $nbExpecDoc);*/
         	
         } else if ($aDataRow['type'] == 'document') {
              $doc = $aDataRow['document'];
              if ($doc->getIsCheckedOut()) {
                   return "<strong>Checked Out</strong>";
              }
         } 
         return ' '; // show nothing if not checked out or not a document.
    } 
    
    
}
    
?>
    