<?php

/**
 * Here is the list of helper functions for Contact Form 7 Cloud Database
 * Company: contactUs.com
 * Created : 20131010
 * */
class CF7_cloud_helper {

    function __construct() {
        
    }

    function CF7_cloud_helper() {
        
    }

    /**
     * this just simplifies multilevel arrays 
     * @param MULTIDIMENSIONAL ARRAY $data expects to be the whole data array structure
     * @param STRING is the index string for the level array to work on
     * @param STRING is the field name with which the array will be re-arranged.
     * */
    public static function simplify_array($data, $index, $field) {
        // simplify

        $data = @array_filter($data);
        if (!empty($data)) {

            if ($index == NULL) { // if no index field is defined.
                $index = 0;
                //print_r($data); exit;
                foreach ($data as $c => $a) {
                    $simple[] = $a->$field;
                    $index++;
                }
            } elseif (strlen($index) > 0 && $index != NULL) { // is a defined field
                //echo($index); exit;
                if (!$data[$index])
                    die('no index found in array');

                foreach ($data[$index] as $c => $a) {
                    $simple[] = $a->$field;
                }
            }
        }
        return $simple;

        // TODO
        // i would like to have something like this working , but need more attention, remember cero compares to false in ternary operator always. EBE
        /*
         *  foreach($data((strlen($index) > 0)?'[$index]':'') as $c => $a){
          $simple[] = $a->$field;
          }
         * 
         * 
         * 
         * 
         * */
    }

}

/* -------------------- END Cloud Database - cf7_cloud_helper.php ----------------- */
