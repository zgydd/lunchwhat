<?php

// Service sample SQL
// Initlize    20170124    Joe

define('select.getItems', <<<SQL
    SELECT * FROM select_items WHERE hide = 0 ORDER BY pre_selected_date, choose_times
SQL
);

define('update.resetItems', <<<SQL
    UPDATE select_items SET 
        weight = 1, 
        choose_times = 0, 
        hide = 0, 
        pre_selected_date = '2017-10-07'
SQL
);

define('update.refreshWeight', <<<SQL
    UPDATE select_items SET hide = 0;
    UPDATE select_items SET weight = :weight WHERE id = :id
SQL
);

define('update.setChoose', <<<SQL
    UPDATE select_items SET 
        pre_selected_date = sysdate(), 
        choose_times = choose_times + 1, 
        hide = 1 
    WHERE id = :id
SQL
);
