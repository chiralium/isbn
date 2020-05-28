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

    function set_new_isbn($BOOK, $isbn_code) {
        if (!is_null($BOOK->isbn2)) {
            $BOOK->isbn2 = $isbn_code;
            return "ISBN_2";
        } elseif (!is_null($BOOK->isbn3)) {
            $BOOK->isbn3 = $isbn_code;
            return "ISBN_3";
        } else {
            $BOOK->isbn4 .= ", $isbn_code";
            return "ISBN_4";
        }
    }

    function set_wrong_isbn($BOOK, $isbn_code) {
        $BOOK->wrong_isbn .= ", $isbn_code";
    }

    function get_data() {
        // create the output json-object
        $BOOKS = read_table();
        $json_object = array(); $id = 1;

        foreach ($BOOKS as $BOOK) {
            if ($BOOK->is_defined_flag) {
                $collname = $BOOK->defined_by;
                $json_object[$id]['action'] = "<span style='color: blue'>ISBN был определен в одном из столбцов <b>($collname)</b></span>";
            } elseif ($BOOK->ISBN->is_valid && $BOOK->ISBN->is_standard_delimiter) {
                $collname = set_new_isbn($BOOK, $BOOK->ISBN->isbn_string);
                $json_object[$id]['action'] = "<span style='color: green'>ISBN перемещен в другой столбец <b>($collname)</b></span>";
            } else {
                set_wrong_isbn($BOOK, $BOOK->ISBN->isbn_string);
                $json_object[$id]['action'] = '<span style="color: red">ISBN отмечен как неверный</span>';
            }

            $json_object[$id]['id'] = $id;
            $json_object[$id]['title'] = $BOOK->title;
            $json_object[$id]['description'] = $BOOK->description;
            $json_object[$id]['isbn'] = $BOOK->isbn;
            $json_object[$id]['isbn2'] = $BOOK->isbn2;
            $json_object[$id]['isbn3'] = $BOOK->isbn3;
            $json_object[$id]['isbn4'] = $BOOK->isbn4;
            $json_object[$id]['isbn_wrong'] = $BOOK->wrong_isbn;
            $json_object[$id]['potential'] = $BOOK->ISBN->isbn_string;
            $id++;
        }
        return json_encode($json_object, JSON_UNESCAPED_UNICODE);
    }