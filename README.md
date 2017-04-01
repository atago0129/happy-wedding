# Happy-Wedding
結婚式の出欠管理と、引き出物の選択を行えるシステムです。

## 動作環境
* php (\>= 5.6) 
* MySQL (\>= 5.1.73)
* Composer
* node.js
* npm

## 導入
##### コンポーネントインストール
```
$ composer install
$ npm install
```

##### 各種設定準備
```
$ cd /path/to/conf
$ cp common.php.temp common.php
$ cp db.php.temp db.php
$ cp slim.php.temp slim.php
$ php /path/to/init/init_tables.php
```
* コピーした設定ファイルは適切に編集してください

##### js コンパイル
```
$ ./node_modules/.bin/gulp
```

##### DocumentRoot および RewriteRule 設定
```
$ vim /path/to/conf.d/happy-wedding.conf
# 設定例
<VirtualHost *:80>
  ServerName example.com
  DocumentRoot /path/to/public
  <Directory "/path/to/public">
    AllowOverride All
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_URI} /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
  </Directory>
</VirtualHost>
```