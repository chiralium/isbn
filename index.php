<?php
    require_once("./lib/book.php");
    require_once("./lib/db.php");

    $db = new DB("127.0.0.1", "root", "");
    $books = $db->execute_query("SELECT * FROM isnb.books");

    $BOOKS = array(); $report = new stdClass();
    foreach ($books as $book) {
        $BOOK = new Book($book);
        // getting only 10 and 13 type
        if ($BOOK->ISBN->type == 10 || $BOOK->ISBN->type == 13) {
            echo "<div style='padding: 5px; margin: 5px; width: 1080px; height: auto; border: 2px solid red'>";
            echo "SRC: $book->description_ru<br><br><br>";
            echo "ISBN: "; var_dump($BOOK->ISBN); echo "<br><br><br>";
            echo "ALREADY SET: "; var_dump($BOOK->is_defined_flag);  echo "<br><br><br>";
            echo "</div>";
            $BOOKS[] = $BOOK;
        }
    }