# Lean Redirect Editor (WordPress plugin)

Tiny plugin that allows you to create permanent (301) redirects on your WordPress website. You can redirect any path on your current hostname to any URL you wish.

If you find any bugs or security issues, please let me know. Contributions welcome.

**About in-app redirects**

Redirecting via a plugin means that each time a redirect rule matches, the application (WordPress) will still be executed and take up unnecessary milliseconds and CPU cycles. If you can, set up redirects inside your HTTP server configuration and don't use a plugin.


## Documentation

1. [Install the plugin](#installation)
1. Activate the plugin, go to `Settings > General` and scroll to the bottom.
2. Enter your redirect rules in the **Redirect rules** text area.
  * Enter 1 rule per line
  * Each rule needs to start with a path from your hostname (domain) starting with a `/`, followed by 1 or more spaces, followed by a full URL starting with the HTTP/S protocol.
  * Malformed rules will be ignored - You can use empty lines for readability and/or leave comments.

**Example:**

```
/twitter    https://twitter.com/mytwitter
/orange.jpg https://mydomain.com/orange-juice-should-be-freshly-squeezed-at-home/

# Comment.
/spring-is-coming-2017/ https://mydomain.com/spring-is-coming-2018/
```


## Requirements

* PHP 5.6 (developed on PHP 7.2)
* Parsed redirect rules are cached via Transients. You should use a caching backend for faster lookups.


## Installation

`WP_CONTENT_DIR` is a WordPress variable that points to the directory containing `plugins`, `themes`, `uploads`, etc. Default is `/absolute/path/wordpress/wp-content`.

Download the [zip](https://github.com/andrejcremoznik/lean-redirect-editor/archive/1.0.zip) and extract it to `WP_CONTENT_DIR/plugins/lean-redirect-editor`.

**CLI one-liner:**

```
# WP_CONTENT_DIR = wordpress/wp-content (by default)
# If WP_CONTENT_DIR/plugins/lean-lightbox doesn't exist, create it first then:

curl -L https://github.com/andrejcremoznik/lean-redirect-editor/archive/1.0.tar.gz | tar zxf - --strip-components=1 -C WP_CONTENT_DIR/plugins/lean-redirect-editor/
```


## Why is this not in the WordPress plugin directory?

Two reasons:

1. I won't bother with SVN. WordPress, please start using Git.
2. I made this because a new maintainer took over [Redirect Editor](https://wordpress.org/plugins/redirect-editor/) and turned it to crap. That plugin used to be perfect until the new developer:
  * Introduced functions that have nothing to do with the functionality that the name of the plugin suggests. It's not a *Redirect Editor*'s job to remove junk from the `wp_head` function.
  * Used `wp_safe_redirect` instead of `wp_redirect`. Just because there's `safe` in the name of the first function, the second isn't any less safe. This essentially broke the ability to redirect to off-site URLs which is one of the most essential reasons for a redirect plugin in the first place.
  * Added an ugly self promoting paragraph to the settings screen.


## Changelog

### 1.0

* Initial public release


## License

Lean Redirect Editor is licensed under the MIT license. See LICENSE.md
