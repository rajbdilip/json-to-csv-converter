<?php

class JSONParser {

	private $filePath;
	private $totalLines = null;

	public function __construct($filePath) {
		$this->filePath = $filePath;

		$this->calculateNoOfLines();
	}

	public function convertToCSV($outputFilePath, $fields) {
		$fieldNames = array();
		$this->prepareFieldNames($fieldNames, $fields);
		
		$outputHandle = fopen($outputFilePath, 'w');
		fputcsv($outputHandle, $fieldNames);

		$inputHandle = fopen($this->filePath, "r") or die("Couldn't get handle");

		$count = 0;
		while (!feof($inputHandle)) {
		    $buffer = fgets($inputHandle);
		    $record = json_decode($buffer, true);

		    if ($record != null) {
		    	$csvRecord = array();
				$this->prepareCSVRecord($csvRecord, $record, $fields);

				fputcsv($outputHandle, $csvRecord);
			}

		    $count++;
		    if ($count % 1000 == 0) {
		    	$this->showProgress($count);
		    }
		}

		echo "Processed $count lines. 100%.\n\n";
		echo "CSV file written to:\n\t $outputFilePath \n";
	}
	
	public function getAllFields() {
		$fields = array();
		$handle = fopen($this->filePath, "r") or die("Couldn't get handle");
		if (!$handle) {
			die('Unexpected error');
		}

		$count = 0;
		while (!feof($handle)) {
		    $buffer = fgets($handle);
		    $record = json_decode($buffer, true);

		    if ($record != null) {
		    	$fields += $this->getKeys($record);
			}

		    $count++;
		    if ($count % 1000 == 0) {
		    	$this->showProgress($count);
		    }
		}

		echo "Processed $count lines. 100%.\n\n";

		return $fields;
	}

	private function prepareCSVRecord(&$csvRecord, $record, $fields) {
		foreach ($fields as $key => $value) {
			if ($value == null) {
				$csvValue = isset($record[$key]) ? $record[$key] : 'N/A';
				$csvRecord[] = is_array($csvValue) ? implode($csvValue, ',') : $csvValue;
			} else {
				if (!isset($record[$key])) {
					// Because we need to iterate through every keys
					$record[$key] = array();
				}
				$this->prepareCSVRecord($csvRecord, $record[$key], $fields[$key]);
			}
		}
	}

	private function prepareFieldNames(&$fieldNames, $fields, $prefix = '') {
		foreach ($fields as $key => $value) {
			if ($value == null) {
				$fieldNames[] = $prefix . $key;
			} else {
				$this->prepareFieldNames($fieldNames, $fields[$key], $prefix . $key . '.');
			}
		}
	}

	private function isAssoc(array $arr) {
	    if (array() === $arr) return false;
	    return array_keys($arr) !== range(0, count($arr) - 1);
	}

	private function getKeys($arr) {
		$fields = array();
		$keys = array_keys($arr);
		foreach ($keys as $key) {
			if (is_array($arr[$key]) && $this->isAssoc($arr[$key])) {
				$fields[$key] = $this->getKeys($arr[$key]);
			} else {
				$fields[$key] = null;
			}
		}

		return $fields;
	}

	/*
	|--------------------------------------------------------------------------
	| OUTPUT UTILITIES
	|--------------------------------------------------------------------------
	*/

	private function calculateNoOfLines() {
		// $output = exec('wc -l ' . $this->filePath);
		exec('wc -l ' . $this->filePath, $output, $return);
		if (!$return) {		// Returns 0 if successfully ran
			preg_match('/\d+/', $output[0], $matches);
			$this->totalLines = intval($matches[0]);
		}

	}

	private function showProgress($count) {
		if ($this->totalLines != null) {
			$percent = ( $count / $this->totalLines ) * 100;
			$percent = number_format((float) $percent, 2, '.', '');
			echo "Processed $count lines. $percent%.\n";
		} else {
			echo "Processed $count lines.\n";
		}

		flush();
	}

}