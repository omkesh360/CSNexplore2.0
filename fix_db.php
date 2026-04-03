<?php
require 'php/database.php';
$db = Database::getInstance();
$db->query("UPDATE blogs SET image = REPLACE(image, 'http://localhost:8000/', '/CSNexplore2.0/'), content = REPLACE(content, 'http://localhost:8000/', '/CSNexplore2.0/')");
$db->query("UPDATE stays SET image = REPLACE(image, 'http://localhost:8000/', '/CSNexplore2.0/'), gallery = REPLACE(gallery, 'http://localhost:8000/', '/CSNexplore2.0/')");
$db->query("UPDATE cars SET image = REPLACE(image, 'http://localhost:8000/', '/CSNexplore2.0/'), gallery = REPLACE(gallery, 'http://localhost:8000/', '/CSNexplore2.0/')");
$db->query("UPDATE bikes SET image = REPLACE(image, 'http://localhost:8000/', '/CSNexplore2.0/'), gallery = REPLACE(gallery, 'http://localhost:8000/', '/CSNexplore2.0/')");
$db->query("UPDATE restaurants SET image = REPLACE(image, 'http://localhost:8000/', '/CSNexplore2.0/'), gallery = REPLACE(gallery, 'http://localhost:8000/', '/CSNexplore2.0/')");
$db->query("UPDATE attractions SET image = REPLACE(image, 'http://localhost:8000/', '/CSNexplore2.0/'), gallery = REPLACE(gallery, 'http://localhost:8000/', '/CSNexplore2.0/')");
$db->query("UPDATE buses SET image = REPLACE(image, 'http://localhost:8000/', '/CSNexplore2.0/'), gallery = REPLACE(gallery, 'http://localhost:8000/', '/CSNexplore2.0/')");
echo 'Done';
