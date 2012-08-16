<?php

$db = new PDO('mysql:host=localhost;dbname=information_schema', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

var_dump($db->query('SHOW COLLATIONS')->fetchAll());
