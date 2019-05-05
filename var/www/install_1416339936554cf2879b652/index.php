<?php
/**
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Management
 * @author     Mr. Willy Fritzsche <willy@willy-tech.de>
 * @copyright  1997-2014 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */
 
(include_once realpath(dirname(__FILE__)).'/resources/main_config.php')	or die('Fehler beim Laden der Seite. Konnte Konfigurationen nicht laden. Fehlercode: 0x0000');

(include_once LIBRARY_PATH.'/main/error_codes.php')				or die($error_code['0x0001']);
(include_once LIBRARY_PATH.'/main/rain.tpl.nocache.class.php')	or die($error_code['0x0002']);
(include_once LIBRARY_PATH.'/main/functions.php')				or die($error_code['0x0003']);
(include_once LIBRARY_PATH.'/main/sites.php')					or die($error_code['0x0007']);

// Lade Header
(include_once CONTENT_PATH.'/html_header.php') or die($error_code['0x0009']);

try
{
	// Lade Content
	if (isset($_GET['s']) && isset($site[$_GET['s']]) && file_exists(CONTENT_PATH.'/'.$site[$_GET['s']]))
	{
		include_once CONTENT_PATH.'/'.$site[$_GET['s']];
	}
	else
	{
		if (isset($_GET['s']) && (!isset($site[$_GET['s']]) || file_exists(CONTENT_PATH.'/'.$site[$_GET['s']]) === false))
		{
			$tpl = new RainTPL;
			$tpl->error('Fehler', 'Leider existiert die angeforderte Seite nicht.');
		}
		else
			include_once CONTENT_PATH.'/install.php';
	}
}
catch(Exception $e)
{
	$errorHandler[] = 'Fehler [tpl]: '.$e;
	
	$tpl = new RainTPL;
	$tpl->error('Fehler', 'Leider ist beim Aufbau der Seite ein Fehler aufgetreten!');
}

// Lade Footer
(include_once CONTENT_PATH.'/html_footer.php') or die($error_code['0x0010']);
?>