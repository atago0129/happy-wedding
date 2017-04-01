<?php

$setting = require_once __DIR__ . '/../conf/db.php';

$baseSql = 'mysql -u ' . $setting['user'] . ' -h' . $setting['host'] . ' -p' . $setting['password'] . ' ' . $setting['name'];

system($baseSql . '< ../sql/user.sql');
system($baseSql . '< ../sql/user_gift.sql');
system($baseSql . '< ../sql/gift.sql');