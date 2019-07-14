<?php
session_start();
spl_autoload_register();
//ini_set('display_errors',1); 
    //error_reporting(E_ALL);
$sqlClient = new mysqlclient(__METHOD__);

if (isset($_GET["elementID"])) $elementID = $sqlClient->escapeString($_GET["elementID"]);
if (isset($_GET["newValue"])) { $newValue = $sqlClient->escapeString($_GET["newValue"]); } else $newValue = null;

// $binaryElementID = pack("H*", $elementID);

// $sqlUpdateCode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $_SESSION['key'],, MCRYPT_MODE_CBC, $_SESSION['iv']);
$sqlUpdateCode = (openssl_decrypt( hex2bin($elementID),'AES-128-CBC', $_SESSION['key'], OPENSSL_RAW_DATA, $_SESSION['iv']));

$splitResult = explode(",", $sqlUpdateCode);

$pageID = trim($splitResult[0]);

switch ($pageID) {   
    case(globalconstants::PAGE_UPDATE_TASKS):
        managertaskscomponents::processUpdate($elementID, $newValue, $splitResult);
        break;
}
?>