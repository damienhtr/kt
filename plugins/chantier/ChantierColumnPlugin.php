<?php

// Tutorial code from wiki.ktdms.com

require_once(KT_LIB_DIR . "/plugins/plugin.inc.php");
require_once(KT_LIB_DIR . "/plugins/pluginregistry.inc.php");

class ChantierColumnPlugin extends KTPlugin {
     var $sNamespace = 'techon.columnchantier.plugin';
     function ChantierColumnPlugin($sFilename = null) {
          $res = parent::KTPlugin($sFilename);
          $this->sFriendlyName = _kt("Column Chantier Plugin");
     }

     function setup() {
          $this->registerColumn(_kt("Chantier Column"), 'techon.columnchantier.plugin', 'ChantierColumn','ChantierColumn.php');
     } 
}

$oRegistry =& KTPluginRegistry::getSingleton();
$oRegistry->registerPlugin('ChantierColumnPlugin','techon.columnchantier.plugin', __FILE__);

?>
