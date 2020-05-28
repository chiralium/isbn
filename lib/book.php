<?php

/**
 * Class ISBN
 */
class ISBN {
    public $checksum = null;
    private $sum = null;
    public $is_valid = null;
    public $type = null;
    public $isbn_string = null;
    public $is_standard_delimiter = null;

    /**
     * ISBN constructor.
     * @param $string
     */
    function __construct($string) {
        $this->extract_isbn($string);
        $this->type = strlen($this->isbn_string);
        if ($this->type == 10 || $this->type == 13) {
            $this->calc_checksum();
            $this->validate();
        } else {
            $this->checksum = 0;
            $this->is_valid = false;
        }
    }

    private function validate() {
        $this->is_valid = (($this->sum + $this->checksum) % 11) == 0;
    }

    private function calc_checksum() {
        $checksum = 0;
        for ($i = 0; $i <= $this->type; $i++) {
            $checksum += (int)($this->isbn_string[$i]) * ($this->type - $i);
        }
        $this->checksum = 11 - $checksum % 11;
        $this->sum = $checksum;
    }

    private function get_delimiter($matches) {
        $string = implode('', $matches);
        for ($i = 0; $i < strlen($string); $i++) {
            if (!is_numeric($string[$i]) && $string[$i] != '-') {
                $this->is_standard_delimiter = false;
                break;
            } else $this->is_standard_delimiter = true;
        }
    }

    private function extract_isbn($string) {
        $regex = '~([0-9]+)((.+)|(?:.+))([0-9]+)(.+)~';
        $matches = array();
        if (preg_match_all($regex, $string, $matches)) {
            $isbn_string = ""; $this->get_delimiter($matches[0]);
            foreach ($matches[0] as $substring) {
                for ($counter = 0; $counter < strlen($substring); $counter++) is_numeric($substring[$counter]) ? $isbn_string .= $substring[$counter] : NULL;
            }
            $this->isbn_string = $isbn_string;
        }
    }
}

/**
 * Class Book
 */
class Book {
    public $id = null;
    public $title = null;
    public $description = null;

    public $isbn = null;
    public $isbn2 = null;
    public $isbn3 = null;
    public $isbn4 = null;

    public $wrong_isbn = null;

    public $ISBN = null;
    public $is_defined_flag = null;

    /**
     * Book constructor.
     * @param $result_set
     */
    function __construct($result_set) {
        $this->id = $result_set->id;
        $this->title = $result_set->title_ru;
        $this->description = $result_set->description_ru;
        $this->isbn = $result_set->isbn;
        $this->isbn2 = $result_set->isbn2;
        $this->isbn3 = $result_set->isbn3;
        $this->isbn4 = $result_set->isbn4;
        $this->wrong_isbn = $result_set->isbn_wrong;

        $this->ISBN = new ISBN($this->description);
        $this->is_defined($result_set);
    }


    /**
     * @param $result_set
     * @description The function returning true if passed $isbn already set in $result_set ($isbn2, $isbn3, $isbn4)
     * @return boolean
     */
    private function is_defined($result_set) {
        $ISBN2 = new ISBN($result_set->isbn2); $ISBN3 = new ISBN($result_set->isbn3); $ISBN4 = new ISBN($result_set->isbn4);
        $this->is_defined_flag = ($this->ISBN->isbn_string == $ISBN2->isbn_string ||
                             $this->ISBN->isbn_string == $ISBN3->isbn_string ||
                             $this->ISBN->isbn_string == $ISBN4->isbn_string);
    }
}