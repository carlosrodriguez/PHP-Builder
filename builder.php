<?php
$cssfiles = array();
$location = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/"));
$loctest = array(
	"css", "common/css", "styles", "common/styles"
);

$ignore = (isset($_REQUEST['ignore']) && $_REQUEST['ignore'] != "") ? explode(",", $_REQUEST['ignore']) : array();

foreach($loctest as $t){
	if(is_dir($location."/".$t)){
		if ($handle = opendir($location."/".$t)) {
			while (false !== ($file = readdir($handle))) {
				if($file == '.' || $file == '..') continue;
				if((findexts($file) == "css") && (!in_array($file, $ignore))){
					array_push($cssfiles, $location."/".$file);
				}
			}
			closedir($handle);
		}
	}
}

print_r($cssfiles);

function findexts ($filename) { 
 	$filename = strtolower($filename) ; 
 	$exts = explode(".", $filename); 
	
	return (isset($exts[1])) ? $exts[1] : false; 
} 

?>