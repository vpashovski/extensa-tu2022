<?php
namespace Speedy;

class Csv
{
    static function toArray($string, $key = '')
    {
        if (!empty($string)) {
            $rows = array_map('str_getcsv', explode(PHP_EOL, $string));
            $header = array_shift($rows);
            $headerCount = count($header);
            $csv = array();

            if ($key && array_search($key, $header) !== false) {
                $unique = array_search($key, $header);

                foreach ($rows as $row) {
                    if ($headerCount == count($row)) {
                        $csv[$row[$unique]] = array_combine($header, $row);
                    }
                }
            } else {
                foreach ($rows as $row) {
                    if ($headerCount == count($row)) {
                        $csv[] = array_combine($header, $row);
                    }
                }
            }

            return $csv;
        } else {
            return array();
        }
    }
}