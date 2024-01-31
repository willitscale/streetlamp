# Apache Setup

To use streetlamp with Apache you will need to route all PHP requests related to your application to a singular file.

## vHost Rewrite

```text
<VirtualHost *:80>
  ServerName localhost
  DocumentRoot /app
  
  ErrorLog /dev/stderr
  CustomLog /dev/stdout combined
    
  <IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L,QSA]
  </IfModule>

</VirtualHost>
```
