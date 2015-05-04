<?php

require_once(KT_LIB_DIR . '/actions/folderaction.inc.php');
require_once('ChantierUtil.php');

class DeleteChantierAction extends KTFolderAction {
	var $sDisplayName = 'Delete';
	var $sName = 'ktcore.actions.chantier.delete';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return 'Supprimer le chantier';
	}

	function getInfo() {
		$aChantier = ChantierUtil::getChantier($this->oFolder->getId());
		if ($aChantier == null) {
			return null;
		}
		return parent::getInfo();
	}
	function do_main() {
		$this->oPage->setBreadcrumbDetails($this->getDisplayName());

		$res = ChantierUtil::deleteChantier($this->oFolder->getId());
		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
			controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		} else {
			$_SESSION['KTInfoMessage'][] = sprintf('Chantier "%s" efface.',$this->oFolder->getName());
			controllerRedirect('chantier');
		}
		exit(0);
	}
}

class DropFileChantierAction extends KTFolderAction {
	var $sDisplayName = 'DropFile';
	var $sName = 'ktcore.actions.chantier.dropfile';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return _kt('Supprimer une piece');
	}

	function getInfo() {
		// Hide this action
		return null;
	}

	function can_user_access_object_requiring_permission() {
		return true;
	}

	function do_main() {
		$linkid = $_REQUEST['id'];
		ChantierUtil::deletePiece($linkid);

		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
			controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		} else {
			$_SESSION['KTInfoMessage'][] = sprintf(_kt('Pièce supprimée.'));
		}


		controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		exit(0);
	}
}

class DropFolderChantierAction extends KTFolderAction {
	var $sDisplayName = 'DropFolder';
	var $sName = 'ktcore.actions.chantier.droplot';
	var $_sShowPermission = "ktcore.permissions.write";

	function getDisplayName() {
		return _kt('Supprimer un lot');
	}

	function getInfo() {
		// Hide this action
		return null;
	}

	function can_user_access_object_requiring_permission() {
		return true;
	}

	function do_main() {
		$linkid = $_REQUEST['id'];
		$res = ChantierUtil::deleteLot($linkid);

		if (PEAR::isError($res)) {
			$_SESSION['KTErrorMessage'][] = $res->getMessage();
			controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		} else {
			KTFolderUtil::delete(FOlder::get($linkid), $this->oUser, 'Lot suppr du chantier');
			$_SESSION['KTInfoMessage'][] = sprintf(_kt('Lot supprimé.'));
		}

		controllerRedirect('chantier', sprintf('action=detail&fFolderId=%d', $this->oFolder->getId()));
		exit(0);
	}
}
