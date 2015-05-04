<?php

require_once(KT_LIB_DIR . '/actions/folderaction.inc.php');
require_once('ChantierUtil.php');

class CreateChantierAction extends ChantierActionUtil {
	var $sDisplayName = 'Create';
	var $sName = 'ktcore.actions.chantier.create';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return 'Initialiser depuis le mod&egrave;le';
	}

	function getInfo() {
		global $default;
		
		$aChantier = ChantierUtil::getChantier($this->oFolder->getId());
		if ($aChantier == null) {
			$default->log->warn(sprintf('CreateChantierAction - return getinfo.parent=%s', $this->getDisplayName()));
// 			return parent::getInfo();
			$aInfo = array(
					'description' => $this->sDescription,
					'name' => $this->getDisplayName(),
					'ns' => $this->sName,
					'url' => $this->getURL(),
			);
			return $this->customiseInfo($aInfo);
		}
		return null;
	}
	
	function do_main() {
		$this->oPage->setBreadcrumbDetails($this->getDisplayName());
		$id = $this->oFolder->getId();
		$chantier = new Chantier($id);
		$res = ChantierUtil::initChantier($chantier);
		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
			controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $id));
		} else {
			$_SESSION['KTInfoMessage'][] = sprintf(_kt('Chantier "%s" initialisé.'),$this->oFolder->getName());
		}

		$idmdl = ChantierUtil::getModel();
		$cpt = 0;
		// Add first the type of lot
		foreach (ChantierUtil::$FOLDER_NAME as $fname) {
			$sfolder = ChantierActionUtil::addFolder($fname, $this->oFolder);
			// Add lots
			$lots = ChantierUtil::getLots($idmdl, $cpt);
			foreach ($lots as $lot) {
				$lotFolder = ChantierActionUtil::addFolder($lot->getCompleteName(), $sfolder);
				$idlot = $lotFolder->getId();
				$lot->setChantierId($id);
				$lot->setId($idlot);
				$res = ChantierUtil::addLot($lot);
				if (PEAR::isError($res)) {
					$_SESSION['KTErrorMessage'][] = $res->getMessage();
					controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $id));
				} else {
					// Add pieces
					foreach($lot->getPieces() as $mdl) {
// 						ChantierUtil::addPiece($idlot, $mdl->getReference(), $mdl->getType());
						ChantierUtil::addPiece($idlot, $mdl->getType());
					}
				}
			}
			$cpt++;
		}
		controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		exit(0);
	}
}

class ParamChantierAction extends ChantierActionUtil {
	var $sDisplayName = 'Param';
	var $sName = 'ktcore.actions.chantier.param';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return _kt('Parametrer le chantier');
	}

	function getInfo() {
		$aChantier = ChantierUtil::getChantier($this->oFolder->getId());
		if ($aChantier == null) {
			return null;
		}
		return parent::getInfo();
	}

	// Source : KTFolderActions.php
	function form_main() {
		$oForm = new KTForm;

		$oForm->setOptions(array(
				'context' => &$this,
				'identifier' => 'ktcore.chantier.add',
				'action' => 'createChantier',
				'fail_action' => 'main',
				'cancel_url' => $this->getUrlForChantier(),
				'label' => $this->getDisplayName(),
				'submit_label' => $this->getDisplayName(),
				'extraargs' => $this->meldPersistQuery("","", true),
		));

		$aChantier = ChantierUtil::getChantier($this->oFolder->getId());
		if ($aChantier == null) {
			$aChantier = new Chantier($this->oFolder->getId());
		}
		
		// widgets
		$oForm->setWidgets(array(
				array('ktcore.widgets.string', array(
						'label' => _kt('MOA'),
						'description' => _kt('Nom de la maitrise d\'Ouvrage'),
						'required' => false,
						'name' => 'moa',
						'value' => $aChantier->getMoa())),
				array('ktcore.widgets.string', array(
						'label' => _kt('Courriel'),
						'description' => _kt('Addresse email de la MOA'),
						'required' => false,
						'name' => 'courriel',
						'value' => $aChantier->getMail())),
				array('ktcore.widgets.string', array(
						'label' => _kt('Addresse'),
						'description' => _kt('Addresse du chantier'),
						'required' => false,
						'name' => 'address',
						'value' => $aChantier->getAddress())),
				array('ktcore.widgets.string', array(
						'label' => _kt('Adresse 2'),
						'description' => _kt('Complement de l\'adresse'),
						'required' => false,
						'name' => 'address2',
						'value' => $aChantier->getAddressCplt())),
				array('ktcore.widgets.string', array(
						'label' => _kt('Code Postal'),
						'description' => _kt('Code Postal de la commune'),
						'required' => false,
						'name' => 'code',
						'value' => $aChantier->getCp())),
				array('ktcore.widgets.string', array(
						'label' => _kt('Ville'),
						'description' => _kt('Nom de la commune'),
						'required' => false,
						'name' => 'ville',
						'value' => $aChantier->getVille())),
				array('ktcore.widgets.string', array(
						'label' => _kt('PC'),
						'description' => _kt('Numéro de PC'),
						'required' => false,
						'name' => 'numpc',
						'value' => $aChantier->getPc())),
				array('ktcore.widgets.date', array(
						'label' => _kt('Date ROC'),
						'description' => _kt('Date de la DOC ou DROC'),
						'required' => false,
						'name' => 'droc',
						'value' => $aChantier->getDateDoc())),
				array('ktcore.widgets.date', array(
						'label' => _kt('Date Fin Chantier'),
						'description' => _kt('Date prévisionnelle de fin de chantier'),
						'required' => false,
						'name' => 'dfin',
						'value' => $aChantier->getDateFin())),
				array('ktcore.widgets.string', array(
						'label' => _kt('Montant'),
						'description' => _kt('Montant total du march&eacute;.'),
						'required' => false,
						'name' => 'montant',
						'value' => $aChantier->getMontant())),
				array('ktcore.widgets.string', array(
						'label' => _kt('Type'),
						'description' => _kt('Garantie souhait&eacute;e'),
						'required' => true,
						'name' => 'garantie',
						'value' => $aChantier->getGarantie())),
		));

		$oForm->setValidators(array(
				array('ktcore.validators.string', array(
						'test' => 'montant',
						'output' => 'montant',
				)),
				array('ktcore.validators.string', array(
						'test' => 'moa',
						'output' => 'moa',
				)),
				array('ktcore.validators.string', array(
						'test' => 'courriel',
						'output' => 'courriel',
				)),
				array('ktcore.validators.string', array(
						'test' => 'numpc',
						'output' => 'numpc',
				)),
				array('ktcore.validators.string', array(
						'test' => 'address',
						'output' => 'address',
				)),
				array('ktcore.validators.string', array(
						'test' => 'address2',
						'output' => 'address2',
				)),
				array('ktcore.validators.string', array(
						'test' => 'code',
						'output' => 'code',
				)),
				array('ktcore.validators.string', array(
						'test' => 'ville',
						'output' => 'ville',
				)),
				array('ktcore.validators.string', array(
						'test' => 'garantie',
						'output' => 'garantie',
				)),
		));

		return $oForm;

	}

	function do_main() {
		$this->oPage->setBreadcrumbDetails($this->getDisplayName());
		$oTemplate =& $this->oValidator->validateTemplate('ktcore/chantier/create');

		$oForm = $this->form_main();

		$oTemplate->setData(array(
				'context' => &$this,
				'form' => $oForm,
		));
		return $oTemplate->render();
	}

	function do_createChantier() {

		$oForm = $this->form_main();
		$res = $oForm->validate();
		if (!empty($res['errors'])) {
			$oForm->handleError();
			exit(0);
		}
		$res = $res['results'];
		$chantier = new Chantier($this->oFolder->getId());
		$chantier->setAddress($res['address']);
		$chantier->setAddressCplt($res['address2']);
		$chantier->setCp($res['code']);
		$chantier->setVille($res['ville']);
		$chantier->setMoa($res['moa']);
		$chantier->setMail($res['courriel']);
		$chantier->setMontant($res['montant']);
		$chantier->setPc($res['numpc']);
		$chantier->setDateDoc($res['droc']);
		$chantier->setDateFin($res['dfin']);
		$chantier->setGarantie($res['garantie']);
		$res = ChantierUtil::initChantier($chantier);
		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
			controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		} else {
			$_SESSION['KTInfoMessage'][] = sprintf(_kt('Chantier "%s" initialisé.'),$this->oFolder->getName());
		}

		controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		exit(0);
	}
}

class AddFolderChantierAction extends ChantierActionUtil {

	var $_sShowPermission = "ktcore.permissions.write";


	function form_main() {
		$oForm = ChantierActionUtil::buildFileForm();
		return $oForm;
	}

	function do_main() {
		
		/*
		$this->aBreadcrumbs[] = array('name' => _kt('Document Links'));
		$this->oPage->setBreadcrumbDetails(_kt("view"));
		
		$aLinkTypes =& LinkType::getList('id > 0');
		
		$addLinkForm = array();
		// KTBaseWidget($sLabel, $sDescription, $sName, $value, $oPage, $bRequired = false, $sId = null, $aErrors = null, $aOptions = null)
		$addLinkForm[] = new KTStringWidget(_kt('Name'), _kt('A short, human-readable name for the link type.'), 'fName', null, $this->oPage, true);
		$addLinkForm[] = new KTStringWidget(_kt('Description'), _kt('A short brief description of the relationship implied by this link type.'), 'fDescription', null, $this->oPage, true);
		
		
		$oTemplating =& KTTemplating::getSingleton();
		$oTemplate = $oTemplating->loadTemplate('ktcore/chantier/linktypes');
		$oTemplate->setData(array(
				"context" => $this,
				"add_form" => $addLinkForm,
				"links" => $aLinkTypes,
		));
		return $oTemplate;
		*/
		$this->oPage->setBreadcrumbDetails($this->getDisplayName());
		$oTemplate =& $this->oValidator->validateTemplate('ktcore/chantier/addfile');

		$oForm = $this->form_main();

		
		$oForm->setOptions(array(
				'context' => &$this,
				'identifier' => 'ktcore.chantier.addAdmin',
				'action' => 'addFile',
				'fail_action' => 'main',
				'cancel_url' => $this->getUrlForChantier(),
				'label' => "Decrire le lot",
				'submit_label' => $this->getDisplayName(),
				'extraargs' => $this->meldPersistQuery("","", true),
		));

		$oTemplate->setData(array(
				'context' => &$this,
				'form' => $oForm,
		));
		return $oTemplate->render();
	}

	function can_user_access_object_requiring_permission() {
		return true;
	}
}

class AddAdminChantierAction extends AddFolderChantierAction {
	var $sDisplayName = 'AddAdminFile';
	var $sName = 'ktcore.actions.chantier.addadminfile';

	function getDisplayName() {
		return _kt('Ajouter un lot Administratif');
	}

	function do_addFile() {
		$oForm = $this->form_main();
		ChantierActionUtil::addLotToChantier($oForm, $this->oFolder, ChantierUtil::$ADMIN_ID);
	}
}

class AddConceptChantierAction extends AddFolderChantierAction {
	var $sDisplayName = 'AddConceptFile';
	var $sName = 'ktcore.actions.chantier.addconceptfile';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return _kt('Ajouter un lot Conception');
	}

	function do_addFile() {
		$oForm = $this->form_main();
		ChantierActionUtil::addLotToChantier($oForm, $this->oFolder, ChantierUtil::$CONCEPT_ID);
	}
}

class AddRealChantierAction extends AddFolderChantierAction {
	var $sDisplayName = 'AddRealFile';
	var $sName = 'ktcore.actions.chantier.addrealfile';

	function getDisplayName() {
		return _kt('Ajouter un lot Realisation');
	}

	function do_addFile() {
		$oForm = $this->form_main();
		ChantierActionUtil::addLotToChantier($oForm, $this->oFolder, ChantierUtil::$REAL_ID);
	}
}

class AddCloseChantierAction extends AddFolderChantierAction {
	var $sDisplayName = 'AddCloseFile';
	var $sName = 'ktcore.actions.chantier.addclosefile';

	function getDisplayName() {
		return _kt('Ajouter un lot Fin de Chantier');
	}

	function do_addFile() {
		$oForm = $this->form_main();
		ChantierActionUtil::addLotToChantier($oForm, $this->oFolder, ChantierUtil::$FINC_ID);
	}
}

// FIXME Bug de la page blanche !
class EditLotChantierAction extends AddFolderChantierAction {
	var $sDisplayName = 'EditFolder';
	var $sName = 'ktcore.actions.chantier.editfolder';

	function getDisplayName() {
		return _kt('Editer un lot de Chantier');
	}

	function getInfo() {
		return null;
	}

	function do_addFile() {
		$oForm = $this->form_main();
		ChantierActionUtil::addLotToChantier($oForm, $this->oFolder);
	}
}

class AutoLink {
	var $pieceId;
	var $fileId;
	var $message;
	
	function AutoLink($pId, $fId) {
		$this->pieceId = $pId;
		$this->fileId = $fId;
		
	}
	
 	function read($name) {
 		$sentences = explode ("-", $name);
 		$this->pieceId = $sentences[0];
 		$this->fileId = $sentences[1];
 	}
	
	function getPieceId() {
		return $this->pieceId;
	}
	
	function getFileId() {
		return $this->fileId;
	}
	
	function getUserFriendlyName() {
		return sprintf("%s-%s", $this->pieceId, $this->fileId);
	}
	
	function getMessage() {
		return $this->message;
	}
	
	function setMessage($msg) {
		$this->message = $msg;
	}
	
}

class AutoLinkChantierAction extends ChantierActionUtil {
	var $sDisplayName = 'AutoLink';
	var $sName = 'ktcore.actions.chantier.autolink';

	function getDisplayName() {
		return _kt('Lier automatiquement');
	}

	function do_main() {
		// List of pieces
		$fFolderId = $this->oFolder->getId();
		$fName = $this->oFolder->getName();
		$lots = ChantierUtil::getAllLots($fFolderId);
		
		// Search for files
		// bug to fix in bulk link
		$ktapi = new KTAPI();
		$links = array();
		$aEnabledLinks = array();
		foreach($lots as $lot) {
			$pieces = $lot->getPieces();
			foreach($pieces as $piece) {
				$fId= 0;
				if ($piece->getFileId()==0){
					$query = sprintf('(FullPath contains "%s") AND (FullPath contains "%s") AND (FullPath contains "%s") AND (FullPath contains "%s")', $fName, $lot->getReference(), $piece->getType(), $lot->getName());
					$response = $ktapi->search($query, null);
					$results = array();
					if($response['status_code'] == 0) {
						$results = $response['results'];
						$fId = $results[0];
					}
				}
				if($fId>0) {
					$link = new AutoLink($piece->getId(), $fId['document_id']);
					$link->setMessage(sprintf('Lien pour %s.%s.%s: %s/%s >%s',
							ChantierUtil::$SHT_FOLDER_NAME[$lot->getNature()], 
							$lot->getReference(),
							$piece->getType(),
							$fId['fullpath'],
							$fId['filename'],
							$link->getUserFriendlyName()));
					$links[] = $link; 
					// FIXME Preselection dees fichiers
					$aEnabledLinks[] = true;
				}
			}
		}
		
		$this->persistParams(array('links', 'fReturnData'));
		$this->oPage->setBreadcrumbDetails($this->getDisplayName());
		
		session_start();
		$oTemplate =& $this->oValidator->validateTemplate('ktcore/chantier/linktypes');
		$oTemplate->setData(array(
				'context' => $this,
				'links' => $links,
            	'enabled_links' => $aEnabledLinks,
            'extraargs' => array('fFolderId' => $fFolderId,
                                 'fReturnAction' => KTUtil::arrayGet($_REQUEST, 'fReturnAction'),
                                 'fReturnData' => KTUtil::arrayGet($_REQUEST, 'fReturnData'),
                                 ),
		));
		return $oTemplate;
		
	}
	
	// FIXME Page blanche, quelle erreur ?
	function do_linkFiles() {
        $aIds = (array) KTUtil::arrayGet($_REQUEST, 'linkids');
//         $default->log->info($aIds);
        // Update disabled plugins
        
        $url = "<";
//         foreach ($aIds as $aId) {
        	$sId = implode(',', $aIds);
        	$link = new AutoLink();
        	$link->read($sId);
        	$url = sprintf("%s %s", $url, $link->getUserFriendlyName());
        	$res = ChantierUtil::linkPiece($link->getPieceId(), $link->getFileId());
//         }
        $url = sprintf("%s>", $url);
        if (PEAR::isError($res)) {
        	$_SESSION['KTErrorMessage'][] = $res->getMessage();
        } else {
        	$_SESSION['KTInfoMessage'][] = sprintf($url);
        }
        controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
	}
}

/**
 * Edit will add a file to a lot
 * @author harter
 *
 */
class EditFolderChantierAction extends ChantierActionUtil {
	var $sDisplayName = 'EditFile';
	var $sName = 'ktcore.actions.chantier.editfile';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return _kt('Ajouter un fichier');
	}

	function can_user_access_object_requiring_permission() {
		return true;
	}

	function getInfo() {
		return null;
	}

	function form_main() {

		$lotid = $_REQUEST['lotid'];
		$oForm = new KTForm;

		// widgets
		$oForm->setWidgets(array(
				array('ktcore.widgets.string', array(
						'label' => _kt('Type'),
						'description' => _kt('Type du fichier attendu. Utiliser le ; pour déclarer plusieurs fichiers'),
						'required' => true,
						'name' => 'type')),
				array('ktcore.widgets.hidden', array(
						'name' => 'id',
						'value' => $lotid)),
		));

		$oForm->setValidators(array(
				array('ktcore.validators.string', array(
						'test' => 'type',
						'output' => 'type',
				)),
				array('ktcore.validators.string', array(
						'test' => 'id',
						'output' => 'id',
				)),
		));

		return $oForm;
	}

	function do_main() {
		$this->oPage->setBreadcrumbDetails($this->getDisplayName());
		$oTemplate =& $this->oValidator->validateTemplate('ktcore/chantier/addfile');

		$oForm = $this->form_main();

		$oForm->setOptions(array(
				'context' => &$this,
				'identifier' => 'ktcore.chantier.addPiece',
				'action' => 'addFile',
				'fail_action' => 'main',
				'cancel_url' => $this->getUrlForChantier(),
				'label' => $this->getDisplayName(),
				'submit_label' => $this->getDisplayName(),
				'extraargs' => $this->meldPersistQuery("","", true),
		));

		$oTemplate->setData(array(
				'context' => &$this,
				'form' => $oForm,
		));
		return $oTemplate->render();
	}

	function do_addFile() {
		$oForm = $this->form_main();
		$res = $oForm->validate();
		if (!empty($res['errors'])) {
			$oForm->handleError();
			exit(0);
		}
		$res = $res['results'];
		$lotid = $res['id'];
		$ptype = $res['type'];
		$atype = explode(";", $ptype);
		$i=0;
		foreach($atype as $type) {
			ChantierUtil::addPiece($lotid, $type);
			$i++;
		}
		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
		} else {
			$_SESSION['KTInfoMessage'][] = sprintf(_kt('Pièce(s) ajoutée(s).'));
		}
		controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));

		exit(0);
	}
}

class LinkPieceChantierAction extends ChantierActionUtil {
	var $sDisplayName = 'Link';
	var $sName = 'ktcore.actions.chantier.link';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return _kt('Lier un fichier');
	}

	function getInfo() {
		// Hide this action
		return null;
	}

	function can_user_access_object_requiring_permission() {
		return true;
	}

	function form_main() {
		$nature = $_REQUEST['nature'];
		$linkid = $_REQUEST['id'];
		$ptype = $_REQUEST['type'];
		$natName = ChantierUtil::$FOLDER_NAME[$nature];
		$subFolder = &KTAPI::get_folder_by_name("/".$natName, $this->oFolder->getId());
		if(PEAR::isError($subFolder)){
			return _kt('Dossier introuvable : ').$subFolder->getMessage();
		}
		$oSubFolder  = $subFolder->get_folder();
		$iFolderId = $oSubFolder->getID();

		// Setup the collection for move display.
		$collection = new AdvancedCollection();
		$oCR =& KTColumnRegistry::getSingleton();
		$col = $oCR->getColumn('ktcore.columns.selection');
		$aColOptions = array();
		$aColOptions['qs_params'] = kt_array_merge($aBaseParams, array('fFolderId'=>$iFolderId));
		$aColOptions['show_folders'] = false;
		$aColOptions['show_documents'] = true;
		$aColOptions['rangename'] = '_d[]';
		$col->setOptions($aColOptions);
		$collection->addColumn($col);

		$col = $oCR->getColumn('ktcore.columns.title');
		//$col->setOptions(array('qs_params'=>kt_array_merge($aBaseParams, array('action' => 'new', 'fFolderId'=>$oFolder->getId()))));
		$col->setOptions(array('link_documents' => false));
		$collection->addColumn($col);

		$qObj = new BrowseQuery($iFolderId);
		$collection->setQueryObject($qObj);

		$collection->empty_message = _kt('No folders available in this location.');
		$aOptions = $collection->getEnvironOptions();
		$collection->setOptions($aOptions);

		$oForm = new KTForm;

		// widgets
		$oForm->setWidgets(array(
				array('ktcore.widgets.string', array(
						'label' => _kt('Id'),
						'description' => _kt('Identifiant de la pièce'),
						'required' => true,
						'value' => $linkid,
						'name' => 'id')),
				array('ktcore.widgets.collection', array(
						'label' => _kt('Target Folder'),
						'description' => _kt(sprintf('<p>Sélection le fichier %s: </p>', $ptype)),
						'required' => true,
						'name' => 'browse',
						'folder_id' => $iFolderId,
						'collection' => $collection))
		));


		$oForm->setValidators(array(
				array('ktcore.validators.string', array(
						'test' => 'id',
						'output' => 'id',
				))
		));
		return $oForm;
	}

	function do_main() {
		$this->oPage->setBreadcrumbDetails($this->getDisplayName());
		$oTemplate =& $this->oValidator->validateTemplate('ktcore/chantier/addfile');

		$oForm = $this->form_main();

		$oForm->setOptions(array(
				'context' => &$this,
				'identifier' => 'ktcore.chantier.link',
				'action' => 'linkFile',
				'fail_action' => 'main',
				'cancel_url' => $this->getUrlForChantier(),
				'label' => $this->getDisplayName(),
				'submit_label' => $this->getDisplayName(),
				'extraargs' => $this->meldPersistQuery("","", true),
		));

		$oTemplate->setData(array(
				'context' => &$this,
				'form' => $oForm,
		));
		return $oTemplate->render();
	}

	function do_linkFile() {
		$oForm = $this->form_main();
		$res = $oForm->validate();
		if (!empty($res['errors'])) {
			$oForm->handleError();
			exit(0);
		}
		$res = $res['results'];
		$oLinkId = $res['id'];
		$selected_docs = KTUtil::arrayGet($_REQUEST, '_d', array());
		$aDocuments = array();
		foreach ($selected_docs as $doc_id) {
			$oDoc =& Document::get($doc_id);
			if (PEAR::isError($oDoc) || ($oDoc === false)) {
				$this->errorRedirectToMain(_kt('Invalid document id specified. Aborting restore.'));
			}
			$aDocuments[] = $oDoc;
			$res = ChantierUtil::linkPiece($oLinkId, $oDoc->getId());
		}

		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
			controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		} else {
			$_SESSION['KTInfoMessage'][] = sprintf(_kt('Pièce %s ajoutée.'), $natureName);
		}


		controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		exit(0);
	}
}

class ChantierActionUtil extends KTFolderAction {
	
	function getInfo() {
		$aChantier = ChantierUtil::getChantier($this->oFolder->getId());
		if ($aChantier==null)
			return null;
		return parent::getInfo();
	}
	
	function getUrlForChantier() {
		$iFolderId = KTUtil::getId($this->oFolder);
		$sExt = '.php';
		if (KTUtil::arrayGet($_SERVER, 'kt_no_extensions')) {
			$sExt = '';
		}
		return sprintf('%s/chantier%s?fFolderId=%d&action=detail', $GLOBALS['KTRootUrl'], $sExt, $iFolderId);
	}
	
	function addFolder($name, $parent) {
		if(KTFolderUtil::exists($parent, $name)) {
			return KTFolderUtil::idFromParent($parent, $name);
		}
		$this->startTransaction();
		$res = KTFolderUtil::add($parent, $name, $this->oUser);
		$aErrorOptions['defaultmessage'] = _kt("Could not create folder in the document management system");
		$this->oValidator->notError($res, $aErrorOptions);
		$this->commitTransaction();
		return $res;
	}

	function addLotToChantier($oForm, $chantier, $nature = null) {
		// $natureName = ChantierUtil::$FOLDER_NAME[$nature];
		$res = $oForm->validate();
		if (!empty($res['errors'])) {
			$oForm->handleError();
			exit(0);
		}
		$res = $res['results'];

		$lot = null;
		$id = $res['id'];
		$creation = true;
		if ($id>0) {
			$lot = new LotChantier($id);
			$creation = false;
		} else {
			$lot = new LotChantier();
			$lot->setNature($nature);
		}
		$chantierId = $chantier->getId();
		$lot->setChantierId($chantierId);
		$lot->setMontant($res['montant']);
		$lot->setReference($res['reference']);
		$lot->setName($res['name']);
		$lot->setEtp($res['etp']);
		if ($creation) {
			// TODO capitaliser avec CreateChantierAction
			$sfolder = ChantierActionUtil::addFolder(ChantierUtil::$FOLDER_NAME[$nature], $chantier);
			$lotFolder = ChantierActionUtil::addFolder($lot->getCompleteName(), $sfolder);
			$idlot = $lotFolder->getId();
			$lot->setId($idlot);
			$res = ChantierUtil::addLot($lot);
		} else {
			$res = ChantierUtil::updateLot($lot);
		}

		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
			controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $chantierId));
		} else {
			$_SESSION['KTInfoMessage'][] = sprintf(_kt('Pièce ajoutée.'));
		}

		controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $chantierId));
		exit(0);
	}

	static function buildFileForm() {
		$linkid = $_REQUEST['lotid'];

		$oForm = new KTForm;

		$lot = null;
		if($linkid!=null)
			$lot = ChantierUtil::getLot($linkid);

		// widgets
		$oForm->setWidgets(array(
				array('ktcore.widgets.string', array(
						'label' => _kt('Name'),
						'description' => _kt('Nom du lot'),
						'required' => true,
						'value' => (($lot==null) ? '' : $lot->getName()),
						'name' => 'name')),
				array('ktcore.widgets.string', array(
						'label' => _kt('Reference'),
						'description' => _kt('Reference du lot (nombre sur 2 chiffres). Ne pas mettre à jour.'),
						'required' => true,
						'value' => (($lot==null) ? '' : $lot->getReference()),
						'name' => 'reference')),
				array('ktcore.widgets.string', array(
						'label' => _kt('Entreprise'),
						'description' => _kt('Entreprise'),
						'required' => false,
						'value' => (($lot==null) ? '' : $lot->getEtp()),
						'name' => 'etp')),
				array('ktcore.widgets.string', array(
						'label' => _kt('Montant'),
						'description' => _kt('Montant'),
						'required' => false,
						'value' => (($lot==null) ? '0' : $lot->getMontant()),
						'name' => 'montant')),
				array('ktcore.widgets.hidden', array(
						'name' => 'id',
						'value' => (($lot==null) ? '0' : $lot->getId())
				)),
		));

		$oForm->setValidators(array(
				array('ktcore.validators.string', array(
						'test' => 'id',
						'output' => 'id',
				)),
				array('ktcore.validators.string', array(
						'test' => 'name',
						'output' => 'name',
				)),
				array('ktcore.validators.string', array(
						'test' => 'reference',
						'output' => 'reference',
				)),
				array('ktcore.validators.string', array(
						'test' => 'etp',
						'output' => 'etp',
				)),
				array('ktcore.validators.string', array(
						'test' => 'montant',
						'output' => 'montant',
				)),
				array('ktcore.validators.string', array(
						'test' => 'type',
						'output' => 'type',
				)),
		));

		return $oForm;
	}
}