# simple-url-shortener
Pretty much the simplest URL shortening service possible. It's simple, fast, opinionated, keeps track of click-thru stats, and does everything I need. It's all self-contained in a single PHP script (and .htaccess file). No dependencies, no frameworks to install, etc. Just upload the file to your web server and you're done. Maybe you'll find it useful, too.

**Motivation:** I run [a small software company](https://tyler.gd/67wn3
) which sells macOS and iOS software. Part of my day-to-day in running the business is replying to customer support questions - over email and, sometimes, SMS/chat. I often need to reply to my customers with long URLs to support documents or supply them with custom-URL-scheme links which they can click on to deep-link them into a specific area of an app.

Long and non-standard URLs can often break once sent to a customer or subsequently forwarded around. I've used traditional link shortening services before (like [bit.ly](https://bit.ly), etc), but always worried about my URLs expiring or breaking if the 3rd party shortening service goes out of business or makes a system change. Even if I upgraded to a paid plan which supports using a custom domain name that I own, I'm still not fully in control of my data.

So, I looked around for open-source URL shortening projects which I could install on my own web server and bend to my will. I found quite a few, but most were either outdated or overly-complex with tons of dependencies on various web frameworks, libraries, etc. I wanted something that would play nicely with a standard LAMP stack so I could drop it onto one of my web servers without having to boot up an entirely new VPS just to avoid port 80/443 conflicts with Apache. Out of the question was anything requiring a dumb, container-based (I see you, Docker) solution just to get started. Nice-to-haves would be offering basic click-thru statistics and an easy way to script the service into my existing business tools and workflows.

Admittedly, I only spent about an hour looking around, but I didn't find anything that met my needs. So, I spent an afternoon hacking together this project to do exactly what I wanted, in the simplest way possible, and without any significant dependencies. The result is a branded URL shortening service I can use with my customers that's simple to use and also integrates with my company's existing support tools (because of its URL-based API and (optional) JSON responses - see below).

## Requirements

* Apache2 with mod_rewrite enabled
* PHP 5.4+ or 7+
* A recent version of MySQL

## Install

1. Clone this repo into the top-level directory of your website on a PHP enabled Apache2 server.
2. Import `database.sql` into a MySQL database.
3. Edit the database settings at the top of `index.php`. You may also edit additional settings such as the length of the short url generated, the allowed characters in the short URL, or set a password to prevent anyone from creating links or viewing statistics about links.

**Note:** This project relies on the `mod_rewrite` rules contained in the `.htaccess` file. Some web servers (on shared web hosts for example) may not always process `.htaccess` files by default. If you're getting `404` errors when trying to use the service, this is probably why. You'll need to contact your server administrator to enable `.htaccess` files. Here's [more information](http://ask.xmodulo.com/enable-htaccess-apache.html) about the topic if you're technically inclined.

## Creating a New Short Link

To create a new short link, just append the full URL you want to shorten to the end of the domain name you installed this project onto. For example, if your shortening service was hosted at `https://example.com` and you want to shorten the URL `https://some-website.com`, go to `https://example.com/http://somewebsite.com`. If all goes well, a plain-text shortened URL will be displayed. Visiting that shortened URL will redirect you to the original URL.

Possibly of interest to app developers like myself: The shortening service also supports URLs of any scheme - not just HTTP and HTTPS. This means you can shorten URLs like `app://whatever`, where `app://` is the URL scheme belonging to your mobile/desktop software. This is useful for deep-linking customers directly into your app.

**iOS Users:** If you have Apple's [Shortcuts.app](https://itunes.apple.com/app/shortcuts/id915249334) installed on your device, you can [click this link](https://www.icloud.com/shortcuts/d52505e26935491397d9c14b54beea41) to import a ready-made shortcut that will let you automatically shorten the URL on your iOS clipboard and replace it with the generated short link.

## Viewing Click-Thru Statistics

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

## A Kinda-Sorta JSON API

This project aims to be as simple-to-use as possible by making all commands and interactions go through a simple URL-based API which returns plain-text or HTML. However, if you're looking to run a script against the shortening service, you can do so. Just pass along `Accept: application/json` in your `HTTP` headers and the service will return all of its output as JSON data - including the stats pages.

## Contributions / Pull Requests / Bug Reports

Bug fixes, new features, and improvements are welcome from anyone. Feel free to [open an issue](https://github.com/tylerhall/simple-url-shortener/issues) or [submit a pull request](https://github.com/tylerhall/simple-url-shortener/pulls).

I consider the current state of the project to be feature-complete for my needs and am not looking to add additional features with heavy dependencies or that complicate the simple install process. That said, I'm more than happy to look at any new features or changes you think would make the project better. Feel free to [get in touch](https://tyler.gd/3qumd
).
