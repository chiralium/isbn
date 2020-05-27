<?php
class ISBN {
    public $checksum = null;
    public $is_valid = null;
    public $type = null;
    public $isbn_string = null;

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
        $this->is_valid = (0 === ($this->checksum % 11)) ? true : false;
    }

    private function calc_checksum() {
        $checksum = 0;
        for ($i = 0; $i <= $this->type; $i++) {
            $checksum += (int)($this->isbn_string[$i]) * ($this->type - $i);
        }
        $this->checksum = $checksum;
    }

    private function extract_isbn($string) {
        $regex = '~([0-9]+)((.)|(?:.))([0-9]+)~';
        $matches = array();
        if (preg_match_all($regex, $string, $matches)) {
            $isbn_string = "";
            foreach ($matches[0] as $substring) {
                for ($counter = 0; $counter < strlen($substring); $counter++) is_numeric($substring[$counter]) ? $isbn_string .= $substring[$counter] : NULL;
            }
            $this->isbn_string = $isbn_string;
        }
    }
}

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
    }
}