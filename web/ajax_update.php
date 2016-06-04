<?php
die('');
// require('inc/ojsettings.php');
// $nowtime = date("Y/m/d H:i:s");
// if(!isset($_POST['type']))
	// die('Invalid argument.');

// else if($_POST['type'] == 'check'){
	// $updxml = $updloc.'info.xml';
	// $curl = curl_init($updxml);
	// $timeout = 10;
	// curl_setopt ($curl, CURLOPT_NOBODY, true);
	// curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout); 
	// $result = curl_exec($curl);
	// $found = false;
	// if ($result !== false) {
		// $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		// if ($statusCode == 200) $found = true;
	// }	
	// curl_close($curl);
	// if ($found == false) die('error');

	// $xml = new DOMDocument(); 
	// $xml->load($updxml); 
	// foreach($xml->getElementsByTagName('latest') as $list)
	// $latest = $list->nodeValue; 
	// if($latest>$web_ver) echo $latest;
	// else echo 'false';
// }

// else if($_POST['type'] == 'getfile'){
	// set_time_limit (60 * 60);  
	
	// if(!isset($_POST['newver']))
		// die('Invalid argument.');
	// $updfile = $updloc.'full_'.$_POST['newver'].'.zip';
	// $curl = curl_init($updfile);
	// $timeout = 10;
	// curl_setopt ($curl, CURLOPT_NOBODY, true);
	// curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout); 
	// $result = curl_exec($curl);
	// $found = false;
	// if ($result !== false) {
		// $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		// if ($statusCode == 200) $found = true;
	// }	
	// curl_close($curl);
	// if ($found == false) die('error');
	
	// $destination_folder = './temp/';   //Location 
	// $newfname = $destination_folder . basename($updfile);         
	// $file = fopen ($updfile, "rb");         
	// if ($file) {         
		// $newf = fopen ($newfname, "wb");         
	// if ($newf)         
		// while(!feof($file)) {         
			// fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );         
			// }         
		// }         
	// if ($file)     
		// fclose($file);            
	// if ($newf)       
		// fclose($newf);         
	// echo 'success';
// }

// else if($_POST['type'] == 'install'){
// if(!isset($_SESSION['administrator']))
	// die('Not administrator');
// if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa'])
	// die('No TFA');
	// echo 'success';
// }
?>