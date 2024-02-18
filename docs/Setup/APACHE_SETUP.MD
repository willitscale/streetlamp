# Apache Setup

To use streetlamp with Apache you will need to route all PHP requests related to your application to a singular file.
There's multiple ways to approach your configuration, but there's no "right" way to do it, as long as it works then for the most part it's fine.

## vHost Rewrite

The approach that's used within the Streetlamp's init is a simple rewrite of all requests into `index.php`.

```text
<VirtualHost *:80>

  DocumentRoot /app

  ErrorLog /dev/stderr
  CustomLog /dev/stdout combined

  <Directory "/app">
    Options Indexes FollowSymlinks MultiViews
    AllowOverride None
    Require all granted
    RewriteEngine on
    RewriteRule ^(.*)$ index.php/$1 [L,QSA]
  </Directory>

  ProxyPassMatch ^/(.*\.php(/.*)?)$ fcgi://php:9000/app/index.php

</VirtualHost>

```

If you want to exclude static files such as `robots.txt` or `css/js` files then you add a `RewriteCond` such as:

```text
RewriteEngine on
RewriteCond $1 !^(favicon\.ico|favicon\.png|media|robots\.txt|crossdomain\.xml|css|js)
RewriteRule ^(.*)$ index.php/$1 [L,QSA]
```

This should allow files to resolve as usual.