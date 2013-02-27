<?php
	namespace vendor\mpdf;

	//
	define('mPDF_VERSION','5.6');

	//Scale factor
	define('_MPDFK', (72/25.4));

	/*-- HTML-CSS --*/
	define('AUTOFONT_CJK',1);
	define('AUTOFONT_THAIVIET',2);
	define('AUTOFONT_RTL',4);
	define('AUTOFONT_INDIC',8);
	define('AUTOFONT_ALL',15);
	define('_BORDER_ALL',15);
	define('_BORDER_TOP',8);
	define('_BORDER_RIGHT',4);
	define('_BORDER_BOTTOM',2);
	define('_BORDER_LEFT',1);
	/*-- END HTML-CSS --*/

	if (!defined('_MPDF_PATH')) define('_MPDF_PATH', dirname(preg_replace('/\\\\/','/',__FILE__)) . '/');
	if (!defined('_MPDF_URI')) define('_MPDF_URI',_MPDF_PATH);

	require_once(_MPDF_PATH.'includes/functions.php');
	require_once(_MPDF_PATH.'config_cp.php');

	if (!defined('_JPGRAPH_PATH')) define("_JPGRAPH_PATH", _MPDF_PATH.'jpgraph/'); 
	if (!defined('_MPDF_TEMP_PATH')) define("_MPDF_TEMP_PATH", _MPDF_PATH.'tmp/');
	if (!defined('_MPDF_TTFONTPATH')) { define('_MPDF_TTFONTPATH',_MPDF_PATH.'ttfonts/'); }
	if (!defined('_MPDF_TTFONTDATAPATH')) { define('_MPDF_TTFONTDATAPATH',_MPDF_PATH.'ttfontdata/'); }

	$errorlevel=error_reporting();
	$errorlevel=error_reporting($errorlevel & ~E_NOTICE);

	if (!function_exists("mb_strlen")) { 
		die("Error - mPDF requires mb_string functions. Ensure that PHP is compiled with php_mbstring.dll enabled."); 
	}

	if (!defined('PHP_VERSION_ID')) {
	    $version = explode('.', PHP_VERSION);
	    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	}

	// Machine dependent number of bytes used to pack "double" into binary (used in cacheTables)
	$test = pack("d", 134455.474557333333666);
	define("_DSIZE", strlen($test));

	// Connector
	require_once(_MPDF_PATH.'mPDF.php');
	class PDF extends \mPDF{}