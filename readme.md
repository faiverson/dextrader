## Boilerplate for the Real Creators
This is a point to start from scratch new projects but with a solid baseline.

## Installation
1. Create a Database in your local env.
2. Install composer if you don't have it.
3. Install node.js
4. Setup the file .env with your local configuration
5. Download the plugin related to your Editor [IDE](http://editorconfig.org/#download)
5. Run commands:
```sh
sudo chmod -R 777 .
cd laravel
composer install
php artisan vendor:publish
php artisan migrate:refresh --seed
```
## Laravel PHP Framework (http://laravel.com/docs/5.1)
[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

### Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).


### License
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Add a cron job

117
down vote
accepted
Just follow these steps:

In Terminal: crontab -e.
Press i to go into vim's insert mode.
Type your cron job, for example:

* * * * * php /Users/faiverson/Sites/dextrader/laravel/artisan/ schedule:run >> /dev/null 2>&1

Press Esc to exit vim's insert mode.

Type ZZ (must be capital letters).
Verify by using crontab -l

### Notifications
You need to install redis and socket.io in your system
Once they are installed you need to run redis
redis-server /usr/local/etc/redis.conf
and also run node /front/src/server/server.js

