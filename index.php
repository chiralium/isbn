<?php
    require_once("./lib/book.php");
    require_once("./lib/db.php");

    $db = new DB("127.0.0.1", "root", "");
    $books = $db->execute_query("SELECT * FROM isnb.books limit 5");

    $BOOKS = array();
    foreach ($books as $book) {
        $regex = "~(.+)([0-9]+)(.+)~";
        // getting the books only with description that containing numbers
        if (preg_match($regex, $book->description_ru)) $BOOKS[] = new Book($book);
    }
    var_dump($BOOKS[1]->ISBN);
    var_dump(count($BOOKS));
