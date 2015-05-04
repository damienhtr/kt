INSERT INTO plugin_helper (
`id` ,
`namespace` ,
`plugin` ,
`classname` ,
`pathname` ,
`object` ,
`classtype` ,
`viewtype`
)
VALUES (
NULL , 'ktplugin.views.chantier', 'ktcore.plugin', '', '', 'Chantier|ktplugin.views.chantier', 'view', 'general'
);
INSERT INTO plugin_helper (
`id` ,
`namespace` ,
`plugin` ,
`classname` ,
`pathname` ,
`object` ,
`classtype` ,
`viewtype`
)
VALUES (
NULL , 'ktplugin.views.chantier.detail', 'ktcore.plugin', '', '', 'ChantierDetail|ktplugin.views.chantier.detail', 'view', 'general'
);

CREATE TABLE chantier (
folderid integer PRIMARY KEY,
moa
 varchar(100),
address
 varchar(100),
address2
 varchar(100),
pc integer,
montant double,
doc_date varchar(32),
finchantier_date varchar(32),
garantie varchar(32)
);

alter table
   chantier
add
   (
mail varchar(64),
code varchar(5),
ville varchar(32)
   );

CREATE TABLE chantier_piece (
id integer PRIMARY KEY AUTO_INCREMENT,
folderid integer,
nom varchar(32),
etp varchar(32),
nature integer,
montant double,
type varchar(64),

file integer DEFAULT 0
);

// UPGRADE 130124, create chantier from model
alter table chantier
add
   (
model boolean DEFAULT false
   );

// UPGRADE 130316, store RC & ATT
alter table chantier_piece
add
   (
fileRC integer DEFAULT 0,
fileAttestation integer DEFAULT 0
   );

/ UPGRADE 130328, Chantier>Lot>Piece
alter table chantier_piece drop COLUMN fileRC;

alter table chantier_piece drop COLUMN fileAttestation;

alter table chantier_piece drop COLUMN nom;

alter table chantier_piece drop COLUMN etp;

alter table chantier_piece drop COLUMN nature;

alter table chantier_piece drop COLUMN montant;

alter table chantier_piece drop COLUMN folderid;

CREATE TABLE chantier_lot (
id integer PRIMARY KEY,
folderid integer,
nom varchar(32),
etp varchar(32),
nature integer,
montant double,
FOREIGN KEY (folderid) REFERENCES chantier(folderid)
);

alter table chantier_piece
add
   (
idlot integer,
FOREIGN KEY (idlot) REFERENCES chantier_lot(id)
   );


/ UPGRADE 131012, Ajout cle unique par piece et lot


alter table chantier_lot
add
   (
reference varchar(2)
   );


alter table chantier_piece
add
   (
reference varchar(2)
   );

