<?php
$loctest = array(
	"css", "common/css", "styles", "common/styles"
);

$location = (isset($_REQUEST['location']) && $_REQUEST['location'] != "") ? $_REQUEST['location'] : substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/"));
$ignore = (isset($_REQUEST['ignore']) && $_REQUEST['ignore'] != "") ? explode(",", $_REQUEST['ignore']) : array();
$destination = (isset($_REQUEST['destination']) && $_REQUEST['destination'] != "") ? $_REQUEST['destination'] : $location."/build/";
$order = (isset($_REQUEST['order']) && $_REQUEST['order'] != "") ? $_REQUEST['order'] : '';

$cssfiles = array();
$cssdirs = array();

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
					if(!in_array($t, $cssdirs)){
						array_push($cssdirs, $t);
					}
					array_push($cssfiles, $location."/".$t."/".$file);
				}
			}
			closedir($handle);
		}
	}
}

$setorder = ($order != "") ? explode(",",$order) : array();
$setorder = array_reverse($setorder);

$cssfiles = orderfiles($cssfiles);

// Check if build directory exists. Clean it if ti does
if(is_dir($destination)){
	//removedir($destination);
}else{
	createdir($destination);
}

foreach($cssfiles as $file){
	copyfile($file, $destination.str_replace($location."/", "", $file));
}

echo compress(concat());

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
		
		if((is_dir($file)) && ($file != ".") && ($file != "..")){
			echo $file."<br />";
			removedir($file);
		}else{
		//echo $file."<br />";
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
         		if (filetype($dir."/".$object) == "dir") removedir($dir."/".$object); else unlink($dir."/".$object); 
       		} 
     	} 
     	reset($objects); 
     	rmdir($dir); 
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

/*
Copy files
*/
function copyfile($file, $destination){
	if(copy($file, $destination)){
		return true;
	}else{
		return false;
	}
}

/*
Set the file order
*/
function orderfiles($cssfiles) {
	global $setorder, $loctest, $location;
	
	foreach($setorder as $file){
		foreach($loctest as $t){
			$f = $location."/".$t."/".$file;
			$me = array_search($f, $cssfiles);
			if($me){
				$newfile = array_splice($cssfiles, $me, 1);
				array_unshift($cssfiles, $newfile[0]);
				break;
			}
		}
	}
	
	return $cssfiles;
}

/*
Concat files
*/
function concat(){
	global $cssfiles;
	
	$contents = '';
	reset($cssfiles);
	while (list(,$element) = each($cssfiles)) {
		//$path = realpath($base . '/' . $element);
		$contents .= "\n\n" . file_get_contents($element);
	}

	return $contents;
}

/*
Compress output
*/
function compress($buffer) {
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
}


?>