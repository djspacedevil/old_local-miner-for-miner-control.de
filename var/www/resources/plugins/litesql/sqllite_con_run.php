<?php
/********************************************************************
*			   phpLite - SQLite Connection Script					*
*				   Author: Sven Gössling							*
*				  Site: Sven-Goessling.de							*
*																	*
*						Version: 1.0.0								*
*																	*
*	                     - Donations - 								*
*			BTC: 1LvETe6uTP64hK3UR3oSAdzT5ZjLnttqBm					*
*			DEM: NWtFftChrx28mvYgqfopmDejxoHiZmAK7u					*
********************************************************************/
// DB connect
function db_con($DBfile) {
    if (!$db = new PDO("sqlite:$DBfile")) {
        $e="font-size:23px; text-align:left; color:firebrick; font-weight:bold;";
        echo "<b style='".$e."'>Fehler beim öffnen der Datenbank $DBfile:</b><br/>";
        echo "<b style='".$e."'>".$db->errorInfo()."</b><br/>";
        die;
    }
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

// DB Query
function db_query($sql) {
    global $db;
    $result = $db->query($sql) OR db_error($sql,$db->errorInfo());
    return $result;
}

// DB errors
function db_error($sql,$error) {
    die('<small><font color="#ff0000"><b>[DB ERROR]</b></font></small><br/><br/><font color="#800000"><b>'.$error.'</b><br/><br/>'.$sql.'</font>');
}

// Add HTML character incoding to strings
function db_output($string) {
    return htmlspecialchars($string);
}
// Add slashes to incoming data
function db_input($string) {
    if (function_exists('mysql_real_escape_string')) {
        return mysql_real_escape_string($string);
    }
    return addslashes($string);
}
?>