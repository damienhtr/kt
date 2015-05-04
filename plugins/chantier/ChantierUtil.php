<?php
/**
 * $Id$
 * DHA
 * Util function
 */

require_once(KT_LIB_DIR . '/database/dbutil.inc');
require_once(KT_LIB_DIR . '/documentmanagement/Document.inc');

class ChantierUtil {

	static public $ADMIN_ID = 0;
	static public $CONCEPT_ID = 1;
	static public $REAL_ID = 2;
	static public $FINC_ID = 3;
	static public $MAX = 3;
	static public $FOLDER_NAME = array('00 Administratif', '01 Conception', '02 Realisation', '03 Fin');
	static public $SHT_FOLDER_NAME = array('A', 'C', 'R', 'F');
	static public $SUB_FOLDER_NEED = array(
			"00 Administratif" => 'true',
			"01 Conception" => 'true',
			"02 Realisation" => 'true',
			"03 Fin" => 'false');

	static function initChantier($chantier) {
		$cId = $chantier->getId();
		if (ChantierUtil::exists($cId)) {
			$query = sprintf("UPDATE `chantier` SET `address`='%s',
					`address2`='%s', `moa`='%s', `pc`='%s', `montant`='%s',
					`doc_date`='%s', `finchantier_date`='%s', `garantie`='%s',
					`code`='%s', `ville`='%s', `mail`='%s'
					WHERE folderid=%s",
					$chantier->getAddress(),
					$chantier->getAddressCplt(),
					$chantier->getMoa(),
					$chantier->getPc(),
					$chantier->getMontant(),
					$chantier->getDateDoc(),
					$chantier->getDateFin(),
					$chantier->getGarantie(),
					$chantier->getCp(),
					$chantier->getVille(),
					$chantier->getMail(),
					$cId);
		} else {
			$query = sprintf("INSERT INTO `chantier` (`folderid`, `address`,
					`address2`, `moa`, `pc`, `montant`,
					`doc_date`, `finchantier_date`, `garantie`,
					`code`, `ville`, `mail`)
					VALUES (%s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$cId,
					$chantier->getAddress(),
					$chantier->getAddressCplt(),
					$chantier->getMoa(),
					$chantier->getPc(),
					$chantier->getMontant(),
					$chantier->getDateDoc(),
					$chantier->getDateFin(),
					$chantier->getGarantie(),
					$chantier->getCp(),
					$chantier->getVille(),
					$chantier->getMail());
		}
		return DBUtil::runQuery($query);
	}

	static function deleteChantier($id) {
		$querySel = sprintf("select id from `chantier_lot` WHERE `folderid`='%s'", $id);
		$rows = DBUtil::getResultArray($querySel);
		if (PEAR::isError($rows)) {
			return null;
		} else {
			foreach($rows as $res) {
				DBUtil::runQuery(sprintf("delete from `chantier_piece` WHERE `idlot`='%s'", $res['id']));
			}
			DBUtil::runQuery(sprintf("delete from `chantier_lot` WHERE `folderid`='%s'", $id));
			DBUtil::runQuery(sprintf("delete from `chantier` WHERE `folderid`='%s'", $id));
		}
		return;
	}

	static function addLot($lot) {
		if (ChantierUtil::existsLot($lot->getId())) {
			return ChantierUtil::updateLot($lot);
		} else {
			$query = sprintf("INSERT INTO `chantier_lot` (`id`, `folderid`, `nom`, `reference`, `etp`, `nature`, `montant`) VALUES ( %s, %s, '%s', '%s', '%s', %s, '%s')",
					$lot->getId(),
					$lot->getChantierId(),
					$lot->getName(),
					$lot->getReference(),
					$lot->getEtp(),
					$lot->getNature(),
					$lot->getMontant());
			$res = DBUtil::runQuery($query);
			return $res;
		}
	}

	static function updateLot($lot) {
		$query = sprintf("UPDATE `chantier_lot` SET `folderid`='%s', `nom`='%s', `reference`='%s', `etp`='%s', `montant`='%s' WHERE `id`=%s",
				$lot->getChantierId(),
				$lot->getName(),
				$lot->getReference(),
				$lot->getEtp(),
				$lot->getMontant(),
				$lot->getId());
		return DBUtil::runQuery($query);
	}

	static function deleteLot($id) {
		DBUtil::runQuery(sprintf("delete from `chantier_piece` WHERE `idlot`='%s'", $id));
		return DBUtil::runQuery(sprintf("delete from `chantier_lot` WHERE `id`='%s'", $id));
	}
	
	static function addPiece($lot, $type) {
		$query = sprintf("INSERT INTO `chantier_piece` (`idlot`, `type`) VALUES (%s, '%s')",
				$lot,
				$type);
		return DBUtil::runQuery($query);
	}

	static function deletePiece($linkId) {
		$query = sprintf("DELETE FROM `chantier_piece` WHERE `id`='%s';",
				$linkId);
		return DBUtil::runQuery($query);
	}

	static function linkPiece($linkId, $fileId) {
		$query = sprintf("UPDATE `chantier_piece` SET `file`='%s' WHERE `id`='%s';",
				$fileId,
				$linkId);
		return DBUtil::runQuery($query);
	}

	static function exists($id) {
		$sql = sprintf("select count(*) as no FROM `chantier` WHERE folderid='%s'", $id);
		$res = DBUtil::getOneResultKey($sql,'no');
		return $res > 0;
	}

	static function existsLot($id) {
		$sql = sprintf("select count(*) as no FROM `chantier_lot` WHERE id='%s'", $id);
		$res = DBUtil::getOneResultKey($sql,'no');
		return $res > 0;
	}

	static function getChantier($id) {
		$query = sprintf("SELECT * FROM `chantier` WHERE folderid=%s;", $id);
		$res = DBUtil::getResultArray($query);
		if (PEAR::isError($res)) {
			return null;
		} else {
			if(count($res)>0) {
				$chantier = new Chantier($id);
				$chantier->setAddress($res[0]['address']);
				$chantier->setAddressCplt($res[0]['address2']);
				$chantier->setCp($res[0]['code']);
				$chantier->setVille($res[0]['ville']);
				$chantier->setMail($res[0]['mail']);
				$chantier->setMoa($res[0]['moa']);
				$chantier->setMontant($res[0]['montant']);
				$chantier->setPc($res[0]['pc']);
				$chantier->setDateDoc($res[0]['doc_date']);
				$chantier->setDateFin($res[0]['finchantier_date']);
				$chantier->setGarantie($res[0]['garantie']);
				return $chantier;
			} else {
				return null;
			}
		}
	}

	static function getModel() {
		$query = sprintf("SELECT folderid FROM `chantier` WHERE model=1;"); // TODO Filtrer sur le nom de repertoire
		$res = DBUtil::getResultArray($query);
		if (PEAR::isError($res)) {
			return null;
		} else {
			if(count($res)>0) {
				$id = $res[0]['folderid'];
				return $id;
			} else {
				return null;
			}
		}
	}

	static function getPiece($id) {
		$query = sprintf("SELECT * FROM `chantier_piece` WHERE id=%s;", $id);
		$rows = DBUtil::getResultArray($query);
		$aReturn = array();
		if (PEAR::isError($rows)) {
			return $aReturn;
		} else {
			if(count($rows)>0) {
				$row = $rows[0];
				$aPiece = new PieceChantier($row['id']);
				$aPiece->setType($row['type']);
// 				$aPiece->setReference($row['reference']);
				$aPiece->setFileId($row['file']);
				$aPiece->setLotId($row['idlot']);
				return $aPiece;
			} else {
				return null;
			}
		}
		return $aReturn;
	}

	static function getLot($id) {
		$query = sprintf("SELECT * FROM `chantier_lot` WHERE id=%s;", $id);
		$rows = DBUtil::getResultArray($query);
		$aReturn = array();
		if (PEAR::isError($rows)) {
			return $aReturn;
		} else {
			if(count($rows)>0) {
				$row = $rows[0];
				$aLot = new LotChantier($row['id']);
				$aLot->setChantierId($row['folderid']);
				$aLot->setName($row['nom']);
// 				$aLot->set/($row['reference']);
				$aLot->setEtp($row['etp']);
				$aLot->setNature($row['nature']);
				$aLot->setMontant($row['montant']);

				getPiecesFromLot($aLot);

				return $aLot;
			} else {
				return null;
			}
		}
		return $aReturn;
	}

	static function getLots($id, $type) {
		$query = sprintf("SELECT * FROM `chantier_lot` WHERE folderid=%s AND nature=%s;", $id, $type);
// 		$query = sprintf("SELECT * FROM `chantier_lot` WHERE folderid=%s AND nature=%s ORDER BY reference ASC;", $id, $type);
		$rows = DBUtil::getResultArray($query);
		$aReturn = array();
		if (PEAR::isError($rows)) {
			return $aReturn;
		} else {
			foreach($rows as $row) {
				$aLot = new LotChantier($row['id']);
				$aLot->setChantierId($row['folderid']);
				$aLot->setName($row['nom']);
				$aLot->setReference($row['reference']);
				$aLot->setEtp($row['etp']);
				$aLot->setNature($row['nature']);
				$aLot->setMontant($row['montant']);
				$aReturn[] = $aLot;
				
				ChantierUtil::getPiecesFromLot($aLot);
			}
		}
		return $aReturn;
	}

	static function getAllLots($id) {
		$query = sprintf("SELECT * FROM `chantier_lot` WHERE folderid=%s;", $id);
		$rows = DBUtil::getResultArray($query);
		$aReturn = array();
		if (PEAR::isError($rows)) {
			return $aReturn;
		} else {
			foreach($rows as $row) {
				$aLot = new LotChantier($row['id']);
				$aLot->setChantierId($row['folderid']);
				$aLot->setName($row['nom']);
				$aLot->setReference($row['reference']);
				$aLot->setEtp($row['etp']);
				$aLot->setNature($row['nature']);
				$aLot->setMontant($row['montant']);
				$aReturn[] = $aLot;
				
				ChantierUtil::getPiecesFromLot($aLot);
			}
		}
		return $aReturn;
	}
	
	static function getPiecesFromLot($aLot) {
		$query = sprintf("SELECT * FROM `chantier_piece` WHERE idlot=%s ORDER BY id ASC;", $aLot->getId());
		$rows2 = DBUtil::getResultArray($query);
		
		if (!PEAR::isError($rows2)) {
			$pieces = array();
			foreach($rows2 as $piece) {
				$aPiece = new PieceChantier($piece['id']);
				$aPiece->setType($piece['type']);
// 				$aPiece->setReference($piece['reference']);
				$aPiece->setFileId($piece['file']);
				$aPiece->setLotId($aLot->getId());
				$pieces[$aPiece->getId()] = $aPiece;
			}
			$aLot->setPieces($pieces);
		}
		return;
	}

	static function getUncompleteChantier() {
		$query = sprintf("SELECT p.folderid, f.name, COUNT(p.id) as nbpieces
				FROM `chantier_piece` as p, folders as f
				WHERE `file`='0' AND p.folderid=f.id
				GROUP BY p.folderid
				ORDER BY nbpieces DESC
				LIMIT 0, 5;");
		$rows = DBUtil::getResultArray($query);
		$aReturn = array();
		if (PEAR::isError($rows)) {
			return $aReturn;
		} else {
			foreach($rows as $row) {
				$aPiece = new ChantierInfo($row['folderid']);
				$aPiece->setName($row['name']);
				$aPiece->setUnlink($row['nbpieces']);
				$aReturn[] = $aPiece;
			}
		}
		return $aReturn;

	}

	static function getFieldTypeVocab() {
		$types = array(
				'convention' => 'Convention',
				'facture' => 'Facture',
				'devis' => 'Devis',
				'rict' => 'RICT',
				'rapport' => 'Rapport',
				'na' => 'Autre',
		);
		return $types;
	}

	static function getDefaultType() {
		return 'na';
	}
}

class ChantierKey {
	var $folderid;

	function ChantierKey($id) {
		$this->folderid = $id;
	}

	function getId() {
		return $this->folderid;
	}
}

class ChantierInfo extends ChantierKey {

	var $name;
	var $unlink;

	function getName() {
		return $this->name;
	}

	function setName($aname) {
		$this->name = $aname;
	}

	function getUnlink() {
		return $this->unlink;
	}

	function setUnlink($alink) {
		$this->unlink = $alink;
	}
}

class Chantier extends ChantierKey {
	var $address;
	var $address2;
	var $cp;
	var $ville;
	var $numpc;
	var $montant;
	var $dateDoc;
	var $dateFin;
	var $garantie;
	var $mail;
	var $moa;

	function getMoa() {
		return $this->moa;
	}

	function setMoa($newmoa) {
		$this->moa = $newmoa;
	}

	function getAddress() {
		return $this->address;
	}

	function setAddress($newad) {
		$this->address = $newad;
	}

	function getMail() {
		return $this->mail;
	}

	function setMail($newmail) {
		$this->mail = $newmail;
	}

	function getCp() {
		return $this->cp;
	}

	function setCp($newcp) {
		$this->cp = $newcp;
	}

	function getVille() {
		return $this->ville;
	}

	function setVille($newville) {
		$this->ville = $newville;
	}

	function getAddressCplt() {
		return $this->address2;
	}

	function setAddressCplt($newad) {
		$this->address2 = $newad;
	}

	function getPc() {
		return $this->numpc;
	}

	function setPc($newpc) {
		$this->numpc = $newpc;
	}

	function getMontant() {
		return $this->montant;
	}

	function setMontant($newmontant) {
		$this->montant = $newmontant;
	}

	function getDateDoc() {
		return $this->dateDoc;
	}

	function setDateDoc($newdate) {
		$this->dateDoc = $newdate;
	}

	function getDateFin() {
		return $this->dateFin;
	}

	function setDateFin($newdate) {
		$this->dateFin = $newdate;
	}

	function getGarantie() {
		return $this->garantie;
	}

	function setGarantie($newgar) {
		$this->garantie = $newgar;
	}
}


class LotChantier {
	var $lotId;
	var $pieces = array();
	var $nature;
	var $reference;
	var $chantierId;
	var $name;
	var $etp;
	var $montant;

	function LotChantier($id) {
		$this->lotId = $id;
	}

	function getId() {
		return $this->lotId;
	}

	function setId($nId) {
		$this->lotId = $nId;
	}

	function getReference() {
		return $this->reference;
	}

	function setReference($nReference) {
		$this->reference = $nReference;
	}

	function getPieces() {
		return $this->pieces;
	}

	function setPieces($newarray) {
		$this->pieces = $newarray;
	}

	function getChantierId() {
		return $this->chantierId;
	}

	function setChantierId($newval) {
		$this->chantierId = $newval;
	}

	function getMontant() {
		return $this->montant;
	}

	function setMontant($newmontant) {
		$this->montant = $newmontant;
	}

	function getNature() {
		return $this->nature;
	}

	function setNature($newnat) {
		$this->nature = $newnat;
	}

	function getName() {
		return $this->name;
	}

	function setName($aname) {
		$this->name = $aname;
	}
	
	function getCompleteName() {
		return sprintf("%s.%s",$this->reference,$this->name);
	}

	function getEtp() {
		return $this->etp;
	}

	function setEtp($aetp) {
		$this->etp = $aetp;
	}
}

class PieceChantier {
	var $pieceId;
	var $lotId;
	var $fileId;
	var $path;
	var $type;
// 	var $reference;

	function PieceChantier($id) {
		$this->pieceId = $id;
		$this->reloadPath();
	}

	function getId() {
		return $this->pieceId;
	}

	function getLotId() {
		return $this->lotId;
	}

	function setLotId($newval) {
		$this->lotId = $newval;
	}

	function getFileId() {
		return $this->fileId;
	}

	function setFileId($newval) {
		$this->fileId = $newval;
		$this->reloadPath();
	}

	private function reloadPath() {
		$res = Document::get($this->fileId);
		if (PEAR::isError($res)) {
			$this->path = sprintf("Erreur %s", $res->getMessage());
		} else {
			$this->path = $res->getName();
		}
	}

	function getFilePath() {
		return $this->path;
	}

	function getType() {
		return $this->type;
	}

	function setType($atype) {
		$this->type = $atype;
	}

// 	function getReference() {
// 		return $this->reference;
// 	}

// 	function setReference($nReference) {
// 		$this->reference = $nReference;
// 	}
	
// 	function getCompleteName() {
// 		return sprintf("%s.%s",$this->reference, $this->type);
// 	}
}

?>
