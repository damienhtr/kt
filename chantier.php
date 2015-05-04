<?php
/**
 * $Id$
 * DHA
 * Main chantier page -- This page is presented to the user after login.
 * It contains a high level overview of the users subscriptions, checked out
 * document, pending approval routing documents, etc.
 *
 * KnowledgeTree Community Edition
 * Document Management Made Simple
 * Copyright (C) 2008, 2009 KnowledgeTree Inc.
 *  
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact KnowledgeTree Inc., PO Box 7775 #87847, San Francisco,
 * California 94120-7775, or email info@knowledgetree.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * KnowledgeTree" logo and retain the original copyright notice. If the display of the
 * logo is not reasonably feasible for technical reasons, the Appropriate Legal Notices
 * must display the words "Powered by KnowledgeTree" and retain the original
 * copyright notice.
 * Contributor( s): ______________________________________
 */

// main library routines and defaults
require_once('config/dmsDefaults.php');
require_once(KT_LIB_DIR . '/templating/templating.inc.php');
require_once(KT_LIB_DIR . '/templating/kt3template.inc.php');
require_once(KT_LIB_DIR . '/dispatcher.inc.php');
require_once(KT_LIB_DIR . '/util/ktutil.inc');
require_once(KT_LIB_DIR . '/unitmanagement/Unit.inc');
require_once(KT_LIB_DIR . '/browse/DocumentCollection.inc.php');
require_once(KT_LIB_DIR . '/browse/BrowseColumns.inc.php');
require_once(KT_LIB_DIR . '/browse/PartialQuery.inc.php');
require_once(KT_LIB_DIR . '/browse/browseutil.inc.php');

require_once(KT_LIB_DIR . '/widgets/portlet.inc.php');
require_once(KT_LIB_DIR . '/actions/folderaction.inc.php');
require_once(KT_DIR . '/plugins/ktcore/KTFolderActions.php');

require_once(KT_LIB_DIR . '/permissions/permissionutil.inc.php');
require_once(KT_LIB_DIR . '/permissions/permission.inc.php');

require_once(KT_LIB_DIR . '/users/userhistory.inc.php');

require_once(KT_LIB_DIR . '/browse/columnregistry.inc.php');
require_once(KT_LIB_DIR . '/actions/entitylist.php');
require_once(KT_LIB_DIR . '/actions/bulkaction.php');

require_once(KT_LIB_DIR . '/foldermanagement/Folder.inc');
require_once(KT_DIR . '/plugins/chantier/ChantierUtil.php');

$sectionName = 'chantier';

class ChantierDispatcher extends KTStandardDispatcher {
	
	var $bAdminRequired = true;
	var $sName = 'ktcore.actions.folder.view';

	var $oFolder = null;
	var $oChantier = null;
	var $oLots = null;
	var $sSection = 'chantier';
	var $query = null;
	var $resultURL;
	var $sHelpPage = 'ktcore/browse.html';
	var $editable;

    function ChantierDispatcher() {
        $this->aBreadcrumbs = array(
            array('action' => 'chantier', 'name' => _kt('Chantier')),
        );
        return parent::KTStandardDispatcher();
    }
    
    function check() {
    	$action = KTUtil::arrayGet($_REQUEST, $this->event_var, 'main');
    	$this->editable = false;
    
    
    	// catch the alternative actions.
    	if ($action != 'main' && $action != 'detail' && $action != 'addFile') {
    		return true;
    	}
    
    	// if we're going to main ...
    
    	// read folder
   		$in_folder_id = KTUtil::arrayGet($_REQUEST, 'fFolderId');
   		if (empty($in_folder_id)) {
   			$oConfig = KTConfig::getSingleton();
   			if ($oConfig->get('chantier/rootFolderId')) {
   				$in_folder_id = $oConfig->get('chantier/rootFolderId');
   			}
   		}
    
   		$folder_id = (int) $in_folder_id; // conveniently, will be 0 if not possible.
   		if ($folder_id == 0) {
   			$folder_id = 1;
   		}
    
   		// here we need the folder object to do the breadcrumbs.
   		$oFolder =& Folder::get($folder_id);
   		if (PEAR::isError($oFolder)) {
   			return false; // just fail.
   		}
    
   		// check whether the user can edit this folder
   		$oPerm = KTPermission::getByName('ktcore.permissions.write');
   		if (KTPermissionUtil::userHasPermissionOnItem($this->oUser, $oPerm, $oFolder)) {
   			$this->editable = true;
   		} else {
   			$this->editable = false;
   		}
    
   		// set the title and breadcrumbs...
   		$this->oPage->setTitle(_kt('Chantier'));
    
   		if (KTPermissionUtil::userHasPermissionOnItem($this->oUser, 'ktcore.permissions.folder_details', $oFolder)) {
   			$this->oPage->setSecondaryTitle($oFolder->getName());
   		} else {
   			if (KTBrowseUtil::inAdminMode($this->oUser, $oFolder)) {
   				$this->oPage->setSecondaryTitle(sprintf('(%s)', $oFolder->getName()));
   			} else {
   				$this->oPage->setSecondaryTitle('...');
   			}
   		}
    
   		//Figure out if we came here by navigating trough a shortcut.
   		//If we came here from a shortcut, the breadcrumbspath should be relative
   		//to the shortcut folder.
   		$iSymLinkFolderId = KTUtil::arrayGet($_REQUEST, 'fShortcutFolder', null);
   		if(is_numeric($iSymLinkFolderId)){
   			$oBreadcrumbsFolder = Folder::get($iSymLinkFolderId);
   			$this->aBreadcrumbs = kt_array_merge($this->aBreadcrumbs, KTBrowseUtil::breadcrumbsForFolder($oBreadcrumbsFolder,array('final' => false)));
   			$this->aBreadcrumbs[] = array('name'=>$oFolder->getName());
   		}else{
   			$this->aBreadcrumbs = kt_array_merge($this->aBreadcrumbs, KTBrowseUtil::breadcrumbsForFolder($oFolder));
   		}
   		$this->oFolder =& $oFolder;
   		
   		// read the chantier
   		$oFolderId = $this->oFolder->getId();
   		$this->oChantier = ChantierUtil::getChantier($oFolderId);
   		$this->oLots = array();
   		$idx = 0;
   		foreach (ChantierUtil::$FOLDER_NAME as $aName) {
   			$anArray = ChantierUtil::getLots($oFolderId, $idx);
   			$this->oLots[] = $anArray;
   			$idx++;
   		}
  
   		// we now have a folder, and need to create the query.
   		$aOptions = array(
   				'ignorepermissions' => KTBrowseUtil::inAdminMode($this->oUser, $oFolder),
   		);
   		$this->oQuery =  new BrowseQuery($oFolder->getId(), $this->oUser, $aOptions);
   
   		$this->resultURL = KTUtil::addQueryString($_SERVER['PHP_SELF'], sprintf('fFolderId=%d', $oFolderId));
   		$this->resultURL = sprintf("%s&action=%s", $this->resultURL, $action);
    
   		// and the portlets
   		$util = new KTFolderActionUtil();
   		if ($action == 'main') {
   			$portlet = new KTActionPortlet(sprintf(_kt('About this folder')));
   			$aActions = $util->getFolderInfoActionsForFolder($this->oFolder, $this->oUser);
   			$portlet->setActions($aActions, $this->sName);
   			$this->oPage->addPortlet($portlet);
   		} else if ($action == 'detail') {
   			$portlet = new KTActionPortlet(sprintf(_kt('Actions sur ce chantier')));
   			$aActions = KTChantierActionUtil::getChantierActionsForFolder($oFolder, $this->oUser);
   			$portlet->setActions($aActions, null, false);
   			$this->oPage->addPortlet($portlet);
   		}
   		
   		$portlet = new KTActionPortlet(sprintf(_kt('Actions on this folder')));
   		$aActions = $util->getFolderActionsForFolder($oFolder, $this->oUser);
   		$portlet->setActions($aActions,null);
   		$this->oPage->addPortlet($portlet);    
    
    	return true;
    }
    
    function do_main() {
    	$action = KTUtil::arrayGet($_REQUEST, 'action', 'main');
		
    	$oColumnRegistry =& KTColumnRegistry::getSingleton();
    	
    	$collection = new AdvancedCollection;
    	$collection->addColumns($oColumnRegistry->getColumnsForView('ktplugin.views.chantier'));
    	
    	$aOptions = $collection->getEnvironOptions(); // extract data from the environment
    	$aOptions['result_url'] = $this->resultURL;
    	$aOptions['is_browse'] = true;
    	
    	$collection->setOptions($aOptions);
    	$collection->setQueryObject($this->oQuery);
    	$collection->setColumnOptions('ktcore.columns.selection', array(
    			'rangename' => 'selection',
    			'show_folders' => true,
    			'show_documents' => false,
    	));
    	
    	$oTemplating =& KTTemplating::getSingleton();
    	if ($action == 'detail') {
			$oTemplate = $oTemplating->loadTemplate('kt3/chantier_detail');
			$aTemplateData = array
			(
					'action' => $action,
					'folder' => $this->oFolder->getName(),
					'status' => !is_null($this->oChantier),
	    		    'chantier' => $this->oChantier,
	    		    'lots' => $this->oLots,
					'natures' => ChantierUtil::$FOLDER_NAME,
					'shortcut' => ChantierUtil::$SHT_FOLDER_NAME,
					'context' => $this,
					'bulkactions' => $aBulkActions,
					'browseutil' => new KTBrowseUtil(),
					'returnaction' => 'chantier',
			);
    	} else {
			$oTemplate = $oTemplating->loadTemplate('kt3/browse');
	    	$aTemplateData = array
	    	(
	    		'action' => $action,
	    		'folder' => $this->oFolder->getName(),
	            'context' => $this,
	            'collection' => $collection,
	            'browse_mode' => $this->browse_mode,
	            'isEditable' => $this->editable,
	            'bulkactions' => $aBulkActions,
	            'browseutil' => new KTBrowseUtil(),
	            'returnaction' => 'browse',
	    	);
    	}
    	return $oTemplate->render($aTemplateData);
    }
}

class KTChantierActionUtil {
	static function getChantierActions() {
		$oRegistry =& KTActionRegistry::getSingleton();
		return $oRegistry->getActions('chantieraction');
	}
	static function &getChantierActionsForFolder($oFolder, $oUser) {
		$aObjects = array();
		foreach (KTChantierActionUtil::getChantierActions() as $aAction) {
			list($sClassName, $sPath, $sPlugin) = $aAction;
			$oRegistry =& KTPluginRegistry::getSingleton();
			$oPlugin =& $oRegistry->getPlugin($sPlugin);
			if (!empty($sPath)) {
				require_once($sPath);
			}
			$aObjects[] =new $sClassName($oFolder, $oUser, $oPlugin);
		}
		return $aObjects;
	}
}
$oDispatcher = new ChantierDispatcher();
$oDispatcher->dispatch();

?>

