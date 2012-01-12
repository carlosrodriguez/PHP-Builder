<?php
$cssfiles = array();
$loctest = array(
	"css", "common/css", "styles", "common/styles"
);

$location = (isset($_REQUEST['location']) && $_REQUEST['location'] != "") ? $_REQUEST['location'] : substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/"));
$ignore = (isset($_REQUEST['ignore']) && $_REQUEST['ignore'] != "") ? explode(",", $_REQUEST['ignore']) : array();
$destination = (isset($_REQUEST['destination']) && $_REQUEST['destination'] != "") ? $_REQUEST['destination'] : $location."/build/";

// Get CSS files
foreach($loctest as $t){
	if(is_dir($location."/".$t)){
		if ($handle = opendir($location."/".$t)) {
			while (false !== ($file = readdir($handle))) {
				if($file == '.' || $file == '..') continue;
				if((findexts($file) == "css") && (!in_array($file, $ignore))){
					if(!is_dir($destination."/".$t)){
						createdir($destination."/".$t);
					}
					array_push($cssfiles, $location."/".$t."/".$file);
				}
			}
			closedir($handle);
		}
	}
}

print_r($cssfiles);

// Check if build directory exists. Clean it if ti does
if(is_dir($destination)){
	emptydir($destination);
}else{
	createdir($destination);
}

foreach($cssfiles as $file){
	copyfile($file, $destination.str_replace($location."/", "", $file));
}


// Functions

/*
Checks if the files has the specified extenstion
*/
function findexts ($filename) { 
 	$filename = strtolower($filename) ; 
 	$exts = explode(".", $filename); 
	
	return (isset($exts[1])) ? $exts[1] : false; 
} 

/*
Delete all files from a directory
*/
function emptydir($dir) {
	$handle = opendir($dir);

	while (($file = readdir($handle))!==false) {
		if(is_dir($file)){
			removedir($file);
		}else{
			@unlink($dir.'/'.$file);
		}
	}

	closedir($handle);
}

function removedir($dir) { 
	if (is_dir($dir)) { 
		$objects = scandir($dir); 
     	foreach ($objects as $object) { 
       		if ($object != "." && $object != "..") { 
         		//if (filetype($dir."/".$object) == "dir") removedir($dir."/".$object); else unlink($dir."/".$object); 
       		} 
     	} 
     	reset($objects); 
     	//rmdir($dir); 
	} 
} 

/*
Creates a directory
*/
function createdir($dir){
	if(mkdir($dir, 0777,true )){
		return $dir;
	}else{
		return false;
	}
}

function copyfile($file, $destination){
	/*if(copy($file, $destination)){
		return true;
	}else{
		return false;
	}*/
}


?>