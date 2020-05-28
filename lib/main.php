<?php
    require_once("book.php");
    require_once("db.php");

/**
 * @return array
 * @description read the data from table and compose BOOK object for each rows; store each object in array
 */
    function read_table() {
        $db = new DB("127.0.0.1", "root", "");
        $books = $db->execute_query("SELECT * FROM isnb.books WHERE description_ru RLIKE '[0-9]+'"); // getting a rows which contained numbers

        $BOOKS = array();
        foreach ($books as $book) {
            $BOOK = new Book($book);
            // getting only 10 and 13 type
            if ($BOOK->ISBN->type == 10 || $BOOK->ISBN->type == 13) $BOOKS[] = $BOOK;
        }
        return $BOOKS;
    }

/**
 * @param $BOOK
 * @param $isbn_code
 * @return string
 * @description check the isbn field and set passed isbn_code to the empty field; return field name
 */
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

/**
 * @param $BOOK
 * @param $isbn_code
 * @description set the wrong isbn_code to the corresponding field
 */
    function set_wrong_isbn($BOOK, $isbn_code) {
        $comma = (!empty($BOOK->wrong_isbn)) ? ', ' : '';
        $BOOK->wrong_isbn .= $comma . $isbn_code;
    }

/**
 * @return false|string
 * @description getting data from BOOK-array and encode it to the JSON; change the each book-object by orders
 */
    function get_data() {
        // create the output json-object
        global $BOOKS;
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

            $json_object[$id]['inner_id'] = $id;
            $json_object[$id]['id'] = $BOOK->id;
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
        commit_changes($BOOKS);
        return json_encode($json_object, JSON_UNESCAPED_UNICODE);
    }

/**
 * @param $BOOKS
 * @description compose the UPDATE statement and execute it to update copied table
 */
    function commit_changes($BOOKS) {
        // create the copy of original table
        $db = new DB("127.0.0.1", "root", "");
        $db->execute_query("DROP TABLE IF EXISTS isnb.copied_books");
        $db->execute_query("CREATE TABLE isnb.copied_books AS (SELECT * FROM isnb.books)");

        // commit the changes into copied table
        $update_template = "UPDATE isnb.copied_books SET %s WHERE id = %s";
        foreach ($BOOKS as $BOOK) {
            $set_template = "isbn2 = '$BOOK->isbn2', isbn3 = '$BOOK->isbn3', isbn4 = '$BOOK->isbn4', isbn_wrong = '$BOOK->wrong_isbn'";
            $db->execute_query(sprintf($update_template, $set_template, $BOOK->id));
        }
    }