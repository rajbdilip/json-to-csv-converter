# JSON to CSV converter
Parse nested JSON file and convert to CSV; Convert Yelp dataset to JSON

## Usage
We need to first populate all the possible fields (JSON keys and sub keys). Ideally, place your JSON file under `data` folder.

```
$ php extract-fields.php data/[file_name].json
```
This will generate `[file_name].json` under `fields` folder. This file will be used in the next step. If you want to change the order of the fields in the CSV file or remove some fields, you can modify this file. However you need to be careful not to break JSON syntax.

Next, use `convert-to-csv.php` script to finally convert your JSON file to CSV.

```
$ php convert-to-csv.php data/[file_name].json
```

This will generate `[file_name].csv` file under `output` folder.
