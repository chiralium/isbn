<?php
    require_once("./lib/book.php");
    require_once("./lib/db.php");

/**
 * @param $isbn
 * @param $result_set
 * @description The function returning true if passed $isbn already set in $result_set ($isbn2, $isbn3, $isbn4)
 * @return boolean
 */
    function is_defined($isbn, $result_set) {
        $ISBN2 = new ISBN($result_set->isbn2); $ISBN3 = new ISBN($result_set->isbn3); $ISBN4 = new ISBN($result_set->isbn4);
        return ($isbn->isbn_string == $ISBN2->isbn_string ||
                $isbn->isbn_string == $ISBN3->isbn_string ||
                $isbn->isbn_string == $ISBN4->isbn_string);
    }

    $db = new DB("127.0.0.1", "root", "");
    $books = $db->execute_query("SELECT * FROM isnb.books");

    $BOOKS = array();
    foreach ($books as $book) {
        $regex = "~(.+)([0-9]+)(.+)~";
        // getting the books only with description that containing numbers
        if (preg_match($regex, $book->description_ru)) {
            $BOOK = new Book($book);
            // getting only 10 and 13 type
            if ($BOOK->ISBN->type == 10 || $BOOK->ISBN->type == 13) {
                echo "SRC: $book->description_ru<br><br><br>";
                echo "ISBN: "; var_dump($BOOK->ISBN); echo "<br><br><br>";
                echo "ALREADY SET: "; var_dump(is_defined($BOOK->ISBN, $book));
                $BOOKS[] = $BOOK;
            }
        }
    }