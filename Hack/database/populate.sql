delete from chantier_piece;
delete from chantier_lot;
delete from chantier where folderid=1;
delete from chantier where folderid=x;

INSERT INTO `chantier` (`folderid`, `moa`, `address`, `address2`, `pc`, `montant`, `doc_date`, `finchantier_date`, `garantie`, `mail`, `code`, `ville`, `model`) VALUES
(1, 'Model Gros Chantier', '', '', 0, 0, '', '', 'DO', 'admin@assuconcept.fr', '', '', 1);

INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('1', '1', '01', 'CdC', NULL, '0', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('2', '1', '02', 'PC', NULL, '0', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('3', '1', '03', 'Offre', NULL, '0', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('4', '1', '04', 'KBIS', NULL, '0', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('5', '1', '05', 'Divers', NULL, '0', NULL);

INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('101', '1', '01', 'Architecte', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('102', '1', '02', 'MOE', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('103', '1', '03', 'Contractant', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('104', '1', '04', 'CT', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('105', '1', '05', 'Coordonateur', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('106', '1', '06', 'Economiste', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('107', '1', '07', 'Metreur', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('108', '1', '08', 'Bureau Etude', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('109', '1', '09', 'AMO', NULL, '1', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('110', '1', '10', 'Geotechnicien', NULL, '1', NULL);

INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('201', '1', '01', 'Demolition', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('202', '1', '02', 'Terrassement', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('203', '1', '03', 'VRD', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('204', '1', '04', 'Fondations', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('205', '1', '05', 'Gros Oeuvre', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('2050', '1', '05b', 'Ossaturebois', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('206', '1', '06', 'Charpente', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('207', '1', '07', 'Couverture', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('208', '1', '08', 'Etancheite', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('209', '1', '09', 'MenuiserieExt', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('210', '1', '10', 'MenuiserieInt', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('211', '1', '11', 'Plomberie', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('212', '1', '12', 'Chauffage', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('213', '1', '13', 'Carrelage', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('214', '1', '14', 'RevetementExt', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('215', '1', '15', 'RevetementInt', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('216', '1', '16', 'Cloison', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('217', '1', '17', 'Electricite', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('218', '1', '18', 'Serrurerie', NULL, '2', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('220', '1', '20', 'Materiau', NULL, '2', NULL);

INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('30', '1', '01', 'PV', NULL, '3', NULL);
INSERT INTO `chantier_lot` (`id`, `folderid`, `reference`, `nom`, `etp`, `nature`, `montant`) VALUES ('31', '1', '02', 'Rapport Final', NULL, '3', NULL);

INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('1', 'CCAP', '0', '1');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('2', 'CCTP', '0', '1');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('1', 'Attestation', '0', '2');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('2', 'Plan', '0', '2');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('3', 'Photo1', '0', '2');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('1', 'Precotation', '0', '3');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('2', 'Devis Assureur', '0', '3');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('3', 'Devis MBC', '0', '3');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('1', 'Extrait', '0', '4');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('1', 'Constat', '0', '5');
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('2', 'Refere', '0', '5');

INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('1', 'Devis', '0', '220'); 
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('2', 'RC', '0', '220'); 
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('3', 'RCD', '0', '220'); 
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('1', 'Devis', '0', '2050'); 
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('2', 'RC', '0', '2050'); 
INSERT INTO `chantier_piece` (`reference`, `type`, `file`, `idlot`) VALUES ('3', 'RCD', '0', '2050'); 

// INSERT AUTO

UPDATE `chantier_lot` SET `folderid`=5 WHERE `folderid`=1;
delete from chantier where folderid=1;