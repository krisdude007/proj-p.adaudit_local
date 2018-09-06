<?php
class Utility {
    /**
     * Gets the Excel-style column letter for the number.
     *
     * @param      integer  $number      The number
     * @param      boolean  $startAtOne  The start at one
     *
     * @return     string   The column letter.
     */
    public function getColumnLetter($number, $startAtOne=true)
    {
        if($startAtOne) $number-=1;

            for($r = ""; $number >= 0; $number = intval($number / 26) - 1)
                $r = chr($number%26 + 0x41) . $r;
            return $r;
    }

    /**
     * Write to a log file
     *
     * @param      array  $data   contains file name and message
     *
     * @return     bool  success or fail of operation
     */
    public function log($data)
    {
        $log_storage_path = '../logs/';
        $startOfEntry = "\r\n ". date('d F y H:i:s') .": ";
        $fileName = $log_storage_path. $data['name'];
        $file     = fopen($fileName, "a+");
        $result   = fwrite($file, $startOfEntry. $data['message'] . "\r\n\r\n");
        fclose($file);
        return $result;
    }

    /**
     * Save a data file to disk as a JSON string
     * @param  array          $array    $array['name'] is the file name, $array['data'] is the data to store.
     * @return boolean/int              The number of bytes written or false on error
     */
    public function saveToFile($array)
    {
        $fileName = $this->file_storage_path. $array['name'];
        $file     = fopen($fileName, "w+");
        $result   = fwrite($file, json_encode($array['data']));
        fclose($file);
        return $result;
    }

    /**
     * Read JSON data from a file on disk
     * @param  string            $fileName   The file name
     * @return boolean/string                The string read from the file or false on error
     */
    public function readFromFile($name)
    {
        $fileName = $this->file_storage_path. $name;
        $file     = fopen($fileName, "r");
        $result   = fread($file, filesize($fileName));
        fclose($file);
        return $result;
    }
}