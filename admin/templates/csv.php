<?
/*  DEFINICIONES DEL BROWSER: */
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    } else if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
        $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
    } else if (!isset($HTTP_USER_AGENT)) {
        $HTTP_USER_AGENT = '';
    }

    // 1. Platform
    if (strstr($HTTP_USER_AGENT, 'Win')) {
        define('MT_USR_OS', 'Win');
    } else if (strstr($HTTP_USER_AGENT, 'Mac')) {
        define('MT_USR_OS', 'Mac');
    } else if (strstr($HTTP_USER_AGENT, 'Linux'))
      {
        if(!defined('MT_USR_OS'))
          define('MT_USR_OS', 'Linux');
    } else if (strstr($HTTP_USER_AGENT, 'Unix')) {
        define('MT_USR_OS', 'Unix');
    } else if (strstr($HTTP_USER_AGENT, 'OS/2')) {
        define('MT_USR_OS', 'OS/2');
    } else {
        define('MT_USR_OS', 'Other');
    }

    // 2. browser and version
    if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('MT_USR_BROWSER_VER', $log_version[2]);
        define('MT_USR_BROWSER_AGENT', 'OPERA');
    } else if (ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('MT_USR_BROWSER_VER', $log_version[1]);
        define('MT_USR_BROWSER_AGENT', 'IE');
    } else if (ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('MT_USR_BROWSER_VER', $log_version[1]);
        define('MT_USR_BROWSER_AGENT', 'OMNIWEB');
    } else if (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))
      {
        if(!defined('MT_USR_BROWSER_VER'))
          define('MT_USR_BROWSER_VER', $log_version[1]);
        if(!defined('MT_USR_BROWSER_AGENT'))
        define('MT_USR_BROWSER_AGENT', 'MOZILLA');
    } else if (ereg('Konqueror/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
        define('MT_USR_BROWSER_VER', $log_version[1]);
        define('MT_USR_BROWSER_AGENT', 'KONQUEROR');
    } else {
        define('MT_USR_BROWSER_VER', 0);
        define('MT_USR_BROWSER_AGENT', 'OTHER');
    }
//  ------------------------------  //
  $mime_type = 'text/x-csv';

  // Send headers
  header('Content-Type: ' . $mime_type);
  // lem9 & loic1: IE need specific headers
  if (MT_USR_BROWSER_AGENT == 'IE') {
      header('Content-Type: application/force-download');
//      header('Content-Disposition: inline; filename="' . $filename . '.' . $ext . '"');
      header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
  } else {
      header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
      header('Expires: 0');
      header('Pragma: no-cache');

  }

	echo $entidad->getTitleRowCSV();
	while($row=$entidad->getRowCSV()) {
		echo $row;
	}
?>
