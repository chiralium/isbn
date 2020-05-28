<?php
    require_once("book.php");
    require_once("db.php");

    function read_table() {
        $db = new DB("127.0.0.1", "root", "");
        $books = $db->execute_query("SELECT * FROM isnb.books");

        $BOOKS = array();
        foreach ($books as $book) {
            $BOOK = new Book($book);
            // getting only 10 and 13 type
            if ($BOOK->ISBN->type == 10 || $BOOK->ISBN->type == 13) $BOOKS[] = $BOOK;
        }
        return $BOOKS;
    }

    function get_data() {
        // create the output json-object
        $BOOKS = read_table();
        $json_object = array(); $id = 1;

        foreach ($BOOKS as $BOOK) {
            $json_object[$id]['id'] = $id;
            $json_object[$id]['title'] = $BOOK->title;
            $json_object[$id]['description'] = $BOOK->description;
            $json_object[$id]['isbn'] = $BOOK->isbn;
            $json_object[$id]['isbn2'] = $BOOK->isb2;
            $json_object[$id]['isbn3'] = $BOOK->isb3;
            $json_object[$id]['isbn4'] = $BOOK->isb4;
            if ($BOOK->is_defined_flag) $json_object[$id]['action'] = 'ISBN был определен в одном из столбцов';
            elseif ($BOOK->ISBN->is_valid && $BOOK->ISBN->is_standard_delimiter) $json_object[$id]['action'] = 'ISBN перемещен в другой столбец';
            else $json_object[$id]['action'] = 'ISBN отмечен как неверный';

            $id++;
        }
        return json_encode($json_object, JSON_UNESCAPED_UNICODE);
    }