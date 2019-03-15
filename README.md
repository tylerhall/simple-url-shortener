# simple-url-shortener
Pretty much the simplest URL shortening service possible. It's simple, fast, opinionated, keeps track of click-thru stats, and does everything I need. Maybe you'll find it useful, too.

## Requirements

* Apache2
* PHP 5.3+ or 7+
* MySQL
* mod_rewrite

## Install

1. Clone this repo into the top-level directory of your website on a PHP enabled Apache2 server.
2. Import `database.sql` into a MySQL database.
3. Edit the database settings at the top of `index.php`. You may also edit additional settings such as the length of the short url generated, the allowed characters in the short URL, or set a password to prevent anyone from creating links or viewing statistics about links.

**Note:** This project relies on the `mod_rewrite` rules contained in the `.htaccess` file. Some web servers (on shared web hosts for example) may not always process `.htaccess` files by default. If you're getting `404` errors when trying to use the service, this is probably why. You'll need to contact your server administrator to enable `.htaccess` files. Here's [more information](http://ask.xmodulo.com/enable-htaccess-apache.html) about the topic if you're technically inclined.

## Create a New Short Link

To create a new short link, just go to `https://example.com/http://somewebsite.com`. If all goes well, a plain-text shortened URL will be displayed. Visiting that shortened URL will redirect you to the original URL.

Possibly of interest to app developers like myself: The shortening service also supports URLs of any scheme - not just HTTP and HTTPS. This means you can shorten URLs like `app://whatever`, where `app://` is the URL scheme belonging to your mobile/desktop software.

## Viewing Click-thru Statistics

All visits to your shortened links are tracked. No personally identifiable user information is logged, however. You can view a summary of your recent link activity by going to `/stats/` on the domain hosting your link shortener.

You can click the "View Stats" link to view more detailed statistics about a specific short link.

## Password Protecting Creating Links

If you don't want to leave your shortening service wide-open for anyone to create a new link, you can optionally set a password by assigning a value to the `$pw_create` variable at the top of `index.php`. You will then need to pass in that password as part of the URL when creating a new link like so:

**Create link with no password set:** `http://example.com/http://domain.com`

**Create link with password set:** `http://example.com/your-password/http://domain.com`

## Password Protecting Stats

Your stats pages can also be password protected. Just set the `$pw_stats` variable at the top of the `index.php` file.

**Viewing stats with no password set:** `http://example.com/stats`

**Viewing stats with password set:** `http://example.com/stats/your-password`

