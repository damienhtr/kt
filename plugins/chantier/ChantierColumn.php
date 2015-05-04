<?php

require_once(KT_LIB_DIR . "/browse/advancedcolumns.inc.php");

class ChantierColumn extends AdvancedColumn {

    var $name = 'chantier';
    var $namespace = 'techon.columnchantier.column';   // this is a unique name for this column, so kt doesn't get confused
    var $sortable = false; // we don't cover sorting
    
    // a constructor - its important that this takes no arguments.
	function ChantierColumn() {
        $this->label = _kt("Chantier");
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
         	$url = sprintf('<a href="%s&action=detail">Acc&eacuteder au chantier</a>', $url);
         	return $url;
         	
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
    