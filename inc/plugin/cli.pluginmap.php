<?php
/**
 * CLI-Script to generate pluginmap
 */
/**
 * Output php file containing pluginmap as <?php $PLUGINMAP = array(...); ?>
 * @var string
 */
$OUTPUT_FILE = dirname(__FILE__).'/pluginmap.php';

/**
 * Root directory
 * @var string
 */
$ROOT_DIR = dirname(__FILE__).'/../../plugin/';

/**
 * Generate pluginmap
 */
$counter  = 0;
$pluginmap = "<?php\n\$PLUGINMAP = array(\n"; scanDirectory($ROOT_DIR, $pluginmap, $counter); $pluginmap .= ");";
writeFile($OUTPUT_FILE, $pluginmap);

echo $counter.' classes found and written to '.$OUTPUT_FILE."\n";

/**
 * Write a file
 * @param string $fileName relative to FRONTEND_PATH
 * @param string $fileContent 
 */
function writeFile($fileName, $fileContent) {
	$file = fopen($fileName, "w");

	if ($file !== false) {
		fwrite($file, $fileContent);
		fclose($file);
	}
}

/**
 * Scan directory
 * @param string $dir
 * @param string $pluginmap
 * @param int $counter
 */
function scanDirectory($dir, &$pluginmap, &$counter) {
	global $ROOT_DIR;

	$handle = opendir($dir);
	while ($file = readdir($handle)) {
		if ($file != '.' && $file != '..') {
			if (is_dir($dir.$file)) {
				scanDirectory($dir.$file.'/', $pluginmap, $counter);
			} else {
				if (substr($file, 0, 6) == 'class.' && substr($file, -4) == '.php') {
					$counter++;
					$classname = substr( substr($file, 6), 0, -4 );
					$filename  = "../plugin/".substr( $dir.$file, strlen($ROOT_DIR) );
					$pluginmap .= "'$classname' => '$filename',\n";
				}
			}
		}
	}

	closedir($handle);
}