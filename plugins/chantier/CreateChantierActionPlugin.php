<?php

// Tutorial code from wiki.ktdms.com

require_once(KT_LIB_DIR . "/plugins/plugin.inc.php");
require_once(KT_LIB_DIR . "/plugins/pluginregistry.inc.php");

class CreateChantierActionPlugin extends KTPlugin {
     var $sNamespace = 'techon.actionchantier.plugin';
     function CreateChantierActionPlugin($sFilename = null) {
          $res = parent::KTPlugin($sFilename);
          $this->sFriendlyName = _kt("Create Chantier Action");
     }

     function setup() {
          $this->registerAction('chantieraction', 'CreateChantierAction', 'ktcore.actions.chantier.create', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'ParamChantierAction', 'ktcore.actions.chantier.param', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'AddAdminChantierAction', 'ktcore.actions.chantier.addadminfile', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'AddConceptChantierAction', 'ktcore.actions.chantier.addconceptfile', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'AddRealChantierAction', 'ktcore.actions.chantier.addrealfile', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'EditLotChantierAction', 'ktcore.actions.chantier.editfolder', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'AutoLinkChantierAction', 'ktcore.actions.chantier.autolink', 'CreateChantierAction.php');
          
          $this->registerAction('chantieraction', 'AddCloseChantierAction', 'ktcore.actions.chantier.addclosefile', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'EditFolderChantierAction', 'ktcore.actions.chantier.editfile', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'LinkPieceChantierAction', 'ktcore.actions.chantier.link', 'CreateChantierAction.php');
          $this->registerAction('chantieraction', 'DropFileChantierAction', 'ktcore.actions.chantier.dropfile', 'DeleteChantierAction.php');
          $this->registerAction('chantieraction', 'DropFolderChantierAction', 'ktcore.actions.chantier.droplot', 'DeleteChantierAction.php');
          $this->registerAction('chantieraction', 'DeleteChantierAction', 'ktcore.actions.chantier.delete', 'DeleteChantierAction.php');
          
          $this->registerDashlet('ChantierDashlet', 'ktcore.dashlets.chantier.dashboard', 'ChantierDashlet.php');
     } 
}

$oRegistry =& KTPluginRegistry::getSingleton();
$oRegistry->registerPlugin('CreateChantierActionPlugin','techon.actionchantier.plugin', __FILE__);

?>
