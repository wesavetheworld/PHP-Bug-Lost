> This project has been discontinued long time ago.

# PHP Bug Lost

PHP Bug Lost is a one-file script for debug and monitoring web sites. It's an inline console running in the browser.

## Quicklinks

- [Demo and official site](http://phpbuglost.com)
- [Downloads](https://github.com/jordifreek/PHP-Bug-Lost/downloads)
- [Wiki Documentation](https://github.com/jordifreek/PHP-Bug-Lost/wiki)

## Features

- A web console
- Two versions, standard and lite with minimal info (both open source)
- Log messages (errors, warnings, info and user)
- View SQL query (mysql, sqlite3, PDO) with errors info and execution time
- View user and internal vars, functions and classes
- Load times and time marks
- Total memory usage, included files and individual vars memory
- Monitoring options, Send emails to the admins when SQL errors occur, also on excessive load times and memory usage
- Ajax panel (_standard version_)
- Profile (meassure execution time of functions and methods) (_standard version_)
- Eval panel, run php code whitin the console (_standard version_)
- Vars watcher, see how a var is getting different values during the execution of a script (_standard version_)
- File viewer for php files (_standard version_)

## Requeriments

PHPBugLost is a one-file library without dependencies, works with PHP5 and requires a modern browser.

- PHP5
- SQLite works with SQLite 3 extension
- Run in a modern browser: IE8+, Firefox, Ch....
- Min. resolution 1024x768
- Tested on WAMP / LAMP environments (Apache/Nginx)

## Installation

Basically, include PHP Bug Lost at top of your code and call _bl_debug()_ before ```</body>``` tag. See documentation for more examples.

```php
<?php
// include php bug lost
include 'phpbuglost.php';
// other libraries in your code
include 'config.php';
include 'functions.php';
?>
<html>
<head>
    <title>This is a PHPBugLost example</title>
</head>
<body>

<!-- Rest of your code -->

<?php
// set true to show console or false to hide
echo bl_debug(true);
?>
</body>
</html>
```

## Why two versions?

The standard version includes many options that do not need to use continuously. In the lite version have been removed to reduce memory usage and load times in production environments. Use the standard version if you want to use all the features of PHP Bug Lost in development environments. Use the lite version if you prefer the speed and simplicity.

## License

PHP Bug Lost was created by Jordi Enguídanos and released under the MIT License.
