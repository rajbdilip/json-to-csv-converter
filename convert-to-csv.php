<?php
require('JSONParser.php');

if (!isset($argv[1])) {
	exit("Error: Please enter data file name.\n");
}

$filePath = $argv[1];
if (!file_exists($filePath)) {
	exit("Error: Data file doesn't exist.\n");
}

$fileName = basename(__DIR__ . '/' . $filePath);

// Retrive fields
$fieldFilePath = "fields/$fileName";
if (!file_exists($fieldFilePath)) {
	exit("Fields file doesn't exist. Please generate fields using extract-fields.\n");
}

$fieldsJSON = file_get_contents($fieldFilePath);
$fields = json_decode($fieldsJSON, true);
$outputFilePath =  "output/" . pathinfo($fileName, PATHINFO_FILENAME) . '.csv';

$parser = new JSONParser($filePath);
$parser->convertToCSV($outputFilePath, $fields);
