# |||\_|||\_|||\_DEFEND THE CASTLE\_|||\_|||\_|||

## Description

**Defend the castle** is a game of attack and defense in which the principle is to defend the castle as its name suggests.
There is a unique castle with 3 types of troops to defend. The level of defensive troops is randomly assigned to each new game unlike random attacks. The strongest troop wins the round and the castle score increases or decreases. The game only ends if someone resets the castle, so the score can be positive or negative.
Good game to you!

## Installation steps

1. Clone the repo from Github.
2. Run `composer install`.
3. Create *config/db.php* from *config/db.php.dist* file and add your DB parameters. Don't delete the *.dist* file, it must be kept.
```php
define('APP_DB_HOST', 'your_db_host');
define('APP_DB_NAME', 'your_db_name');
define('APP_DB_USER', 'your_db_user_wich_is_not_root');
define('APP_DB_PWD', 'your_db_password');
```
4. Import `defend_the_castle.sql` in your SQL server,
5. Run the internal PHP webserver with `php -S localhost:8000 -t public/`. The option `-t` with `public` as parameter means your localhost will target the `/public` folder.
6. Go to `localhost:8000` with your favorite browser.
7. From this starter kit, create your own web application.

## URLs availables

* Home page at [localhost:8000/](localhost:8000/game/play)