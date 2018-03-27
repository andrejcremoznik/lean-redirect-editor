# Lean Redirect Editor (WordPress plugin)

Tiny plugin that allows you to create permanent (301) redirects on your WordPress website. You can redirect any path on your current hostname to any URL you wish.

If you find any bugs or security issues, please let me know. Contributions welcome.

**About in-app redirects**

Redirecting via a plugin means that each time a redirect rule matches, the application (WordPress) will still be executed and take up unnecessary milliseconds and CPU cycles. If you can, set up redirects inside your HTTP server configuration and don't use a plugin.


## Documentation

1. [Install the plugin](#installation)
1. Activate the plugin, go to `Settings > General` and scroll to the bottom.
2. Enter your redirect rules in the **Redirect rules** text area.
    1. Enter 1 rule per line
    2. Each rule needs to start with a path from your hostname (domain) starting with a `/`, followed by 1 or more spaces, followed by a full URL starting with the HTTP/S protocol.
    3. Malformed rules will be ignored - You can use empty lines for readability and/or leave comments.

**Example:**

```
/twitter    https://twitter.com/mytwitter
/orange.jpg https://mydomain.com/orange-juice-should-be-freshly-squeezed-at-home/

# Comment.
/spring-is-coming-2017/ https://mydomain.com/spring-is-coming-2018/
```

### How it works

1. On every page load this plugin will use the earliest available action hook to execute the redirect function.
2. The redirect function will try to fetch parsed redirect rules from cache.
    1. If cache doesn't exist, the raw rules as configured in the dashboard will be fetched from the DB, parsed and cached.
3. If the current requested path matches a configured redirect rule, a 301 redirect will be send to the browser.

**Object Cache**

* To avoid looking up redirect rules in MySQL, you need an in-memory object cache like Redis or Memcached. If you have a high traffic site, chances are you have it set up already.
* The lifetime of the cached redirects is indefinite. After you update the rules, you'll need to flush the cache for them to apply.
* My preferred object cache is Redis with [this plugin](https://wordpress.org/plugins/redis-cache/).


## Requirements

* PHP 5.6 (developed on PHP 7.2)
* Parsed redirect rules are cached if an object cache backend exists. You *should* use a caching backend for faster lookups.


## Installation

`WP_CONTENT_DIR` is a WordPress variable that points to the directory containing `plugins`, `themes`, `uploads`, etc. Default is `/absolute/path/wordpress/wp-content`.

Download the [zip](https://github.com/andrejcremoznik/lean-redirect-editor/archive/1.1.zip) and extract it to `WP_CONTENT_DIR/plugins/lean-redirect-editor`.

**CLI one-liner:**

```
# WP_CONTENT_DIR = wordpress/wp-content (by default)
# If WP_CONTENT_DIR/plugins/lean-lightbox doesn't exist, create it first then:

curl -L https://github.com/andrejcremoznik/lean-redirect-editor/archive/1.1.tar.gz | tar zxf - --strip-components=1 -C WP_CONTENT_DIR/plugins/lean-redirect-editor/
```


## Why is this not in the WordPress plugin directory?

Two reasons:

1. I won't bother with SVN. WordPress, please start using Git.
2. I made this because a new maintainer took over [Redirect Editor](https://wordpress.org/plugins/redirect-editor/) and turned it to crap. That plugin used to be perfect until the new developer:
    1. Introduced functions that have nothing to do with the functionality that the name of the plugin suggests. It's not a *Redirect Editor*'s job to remove junk from the `wp_head` function.
    2. Used `wp_safe_redirect` instead of `wp_redirect`. Just because there's `safe` in the name of the first function, the second isn't any less safe. This essentially broke the ability to redirect to off-site URLs which is one of the most essential reasons for a redirect plugin in the first place.
    3. Added an ugly self promoting paragraph to the settings screen.


## Changelog

### 1.1

* Use WP object cache functions instead of transients.
* Documentation updates

### 1.0

* Initial public release


## License

Lean Redirect Editor is licensed under the MIT license. See LICENSE.md
