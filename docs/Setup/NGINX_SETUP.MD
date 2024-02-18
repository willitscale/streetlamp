# NGINX Setup

To use streetlamp with Nginx you will need to route all PHP requests related to your application to a singular file.
There's a couple of different approaches to this and a more bespoke configuration may be required for multi tenant applications.
This setup is taking the assumption that this is being setup for a singular vhost with a very relaxed level of mapping (e.g. like a docker config).
This is also taking the assumption that this is being setup with a PHP FPM configuration.

## vHost try_files directive

Typically, I'd advise against this approach with nginx, but there may be a need for it, so I'll cover it anyway.
I'd advise against it due to it opening up your PHP files directly to the world instead of using the `index.php` as a gateway.
This could be bothersome if you have say command line scripts or even worse a proxy to `system` or `exec`.
If you do have files that you may need to access directly then this would probably be the approach for you.

```text
server {
    listen 80;

    access_log /dev/stdout main;
    error_log /dev/stderr info;

    location / {
        try_files $uri $uri/ /index.php;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME /app$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## vHost rewrite directive

The rewrite approach is my personal preference and maintains the constraint of the `index.php` being the sole gateway for the application.
Now, unlike the `try_files` approach of nginx it does mean that even assets have to use the `index.php` as a proxy.

```text
server {
    listen 80;

    access_log /dev/stdout main;
    error_log /dev/stderr info;

    location / {
      rewrite ^ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME /app$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

If you wanted to allow assets to be accessed outside of PHP you could do so by adding something like this before the location rewrite:

```text
    location ~* \.(js|ico|gif|jpg|png|svg|css|jpeg|eot|woff|ttf)$ {
    }

    location / {
      rewrite ^ /index.php$is_args$args;
    }
```

This will allow the files to resolve as they usually would.

___Note: This is the configuration used when running `./vendor/bin/streetlamp init docker`___
