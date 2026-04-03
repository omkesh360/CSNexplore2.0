<?php
require 'php/database.php';
$db = Database::getInstance();
$tables = ['blogs', 'stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
$fields = ['description', 'meta_description', 'content', 'name', 'title'];
foreach ($tables as $t) {
    foreach ($fields as $f) {
        try {
            $db->query("UPDATE `$t` SET `$f` = REPLACE(`$f`, 'ÔÇö', '—')");
            $db->query("UPDATE `$t` SET `$f` = REPLACE(`$f`, 'â€”', '—')");
            // Also replacing any strange occurrences of hardcoded 'localhost:8000' while we are at it.
            $db->query("UPDATE `$t` SET `$f` = REPLACE(`$f`, 'http://localhost:8000/', '/CSNexplore2.0/')");
        } catch (Exception $e) {}
    }
}
echo "DB encoding fixed.\n";
