<?php

require_once("../../../config/dmsDefaults.php");
require_once(KT_LIB_DIR . '/import/zipimportstorage.inc.php');

$f = new KTZipImportStorage(KT_DIR .  "/tests/import/dataset2/dataset2.zip");
$f->init();

$rootFiles = array("c");

if ($f->listDocuments("/") !== $rootFiles) {
    print "Root file listing failure\n";
    print "Should be:\n";
    var_dump($rootFiles);
    print "Got:\n";
    var_dump($f->listDocuments("/"));
    $f->cleanup();
    exit(0);
}

$f->cleanup();

print "SUCCESS\n";
