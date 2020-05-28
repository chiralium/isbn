<?php
    require_once("./lib/main.php");

    if ($_GET['g'] == 'row') {
        header("Content-Type: application/json");
        $json = get_data();
        echo $json;
    } elseif ($_GET['g'] == 'col') {
        $coll_structure = '
                            [
                                {"name": "id", "title": "ID", "breakpoints": "xs sm", "type": "number", "style": {"width": 80, "maxWidth": 80}},
                                {"name": "title", "title": "Заголовок"},
                                {"name": "description", "title": "Описание"},
                                {"name": "isbn", "title": "ISBN"},
                                {"name": "isbn2", "title": "ISBN_2"},
                                {"name": "isbn3", "title": "ISBN_3"},
                                {"name": "isbn4", "title": "ISBN_4"},
                                {"name": "isbn_wrong", "title": "ISBN_WRONG"},
                                {"name": "action", "title": "Действие"}
                            ]
                      ';
        header("Content-Type: application/json");
        echo $coll_structure;
    } else {
        echo 404;
    }