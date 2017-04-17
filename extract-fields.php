<?php
require('JSONParser.php');

if (!isset($argv[1])) {
	exit("Error: Please specify source file name.\n");
}

$filePath = $argv[1];
if (!file_exists($filePath)) {
	exit("Error: File doesn't exist.\n");
}

$parser = new JSONParser($filePath);
$fields = $parser->getAllFields();

$fieldsJSON = json_encode($fields);

// Write fields to the file
$outputFilePath =  'fields/' . basename(__DIR__ . '/' . $filePath);
$handle = fopen($outputFilePath, "w");
fwrite($handle, $fieldsJSON);
fclose($handle);

echo "Fields written to:\n\t $outputFilePath \n";