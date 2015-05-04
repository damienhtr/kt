<?php

// Tutorial code from wiki.ktdms.com

require_once(KT_LIB_DIR . "/plugins/plugin.inc.php");
require_once(KT_LIB_DIR . "/plugins/pluginregistry.inc.php");

class TutorialColumnPlugin extends KTPlugin {
     var $sNamespace = 'wikitutorial.columntutorial.plugin';
     function TutorialColumnPlugin($sFilename = null) {
          $res = parent::KTPlugin($sFilename);
          $this->sFriendlyName = _kt("Column Tutorial Plugin");
     }

     function setup() {
          $this->registerColumn(_kt("Tutorial Column"), 'wikitutorial.columntutorial.column', 'TutorialColumn','TutorialColumn.php');
     } 
}

$oRegistry =& KTPluginRegistry::getSingleton();
$oRegistry->registerPlugin('TutorialColumnPlugin','wikitutorial.columntutorial.plugin', __FILE__);

?>
