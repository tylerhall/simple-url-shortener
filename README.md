# simple-url-shortener
Pretty much the simplest URL shortening service possible.

## Requirements

* Apache2
* PHP 5.3+
* MySQL
* mod_rewrite

## Install

1. Clone this repo into the top-level directory of your website on a PHP enabled Apache2 server.
2. Import `database.sql` into a MySQL database.
3. Edit the database settings at the top of `index.php`

## Create a New Short Link

To create a new short link, just go to `https://your-domain.com/http://somewebsite.com`. If all goes well, a plain-text shortened URL will be displayed. Visiting that link will redirect you to the original URL.

Possibly of interest to app developers like myself: The shortening service also supports URLs of any scheme - not just HTTP and HTTPS. This means you can shorten things like `app://whatever`, where `app://` is the URL scheme belonging to your mobile/desktop software.
