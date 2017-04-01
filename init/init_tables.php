<?php

$setting = require_once __DIR__ . '/../conf/db.php';

$baseSql = 'mysql -u ' . $setting['user'] . ' -h' . $setting['host'] . ' -p' . $setting['password'] . ' ' . $setting['name'];

$sqlDir = __DIR__ . '/../sql/';

system($baseSql . '< ' . $sqlDir . 'user.sql');
system($baseSql . '< ' . $sqlDir . 'user_gift.sql');
system($baseSql . '< ' . $sqlDir . 'gift.sql');