<?php
/**
 * Plugin Name:       Lean Redirect Editor
 * Plugin URI:        https://github.com/andrejcremoznik/lean-redirect-editor
 * Description:       Lean and easy management for redirects on your WordPress website.
 * Version:           1.1
 * Author:            Andrej Cremoznik
 * Author URI:        https://keybase.io/andrejcremoznik
 * License:           MIT
 */

if (!defined('WPINC')) die();

class LeanRedirectEditor {
  public function __construct() {
    add_action('muplugins_loaded', [$this, 'redirect']);
    add_action('admin_init', [$this, 'init']);
  }

  /**
   * Admin options
   */
  public function init() {
    register_setting('general', 'lre_rules', [
      'type' => 'string',
      'sanitize_callback' => 'sanitize_textarea_field',
      'show_in_rest' => false,
      'default' => ''
    ]);

    add_settings_section(
      'lre_section',
      'Redirects',
      [$this, 'lre_section_cb'],
      'general'
    );

    add_settings_field(
      'lre_field',
      'Redirect rules',
      [$this, 'lre_field_cb'],
      'general',
      'lre_section',
      ['label_for' => 'lre_rules']
    );
  }

  public function lre_section_cb() {
    echo '<p>Redirect URL paths on your current domain elsewhere. Enter 1 rule per line with the path you want redirected, followed by a space, followed by the new URL.<br>Example: <code>/some/path/ https://somewebsite.com/new/path/</code></p>';
  }

  public function lre_field_cb($args) {
    $rules = get_option('lre_rules', '');
    printf(
      '<textarea id="%s" name="lre_rules" rows="12" style="font-family:monospace;width:100%%">%s</textarea>',
      $args['label_for'],
      esc_html($rules)
    );
  }

  /**
   * Redirect
   */
  public function redirect() {
    // Get redirect rules from cache
    $redirects = wp_cache_get('lre_rules');
    // If no cache, get the rules from the stored options, parse them, and cache
    if ($redirects === false) {
      $rules = ($opt = get_option('lre_rules')) ? explode("\n", $opt) : [];
      $redirects = [];
      foreach ($rules as $rule) {
        // Remove extra spaces from line
        $rule = preg_replace('/\s+/', ' ', trim($rule));
        // Split rule in parts
        $parts = explode(' ', $rule);
        // Ignore if incorect number of parts
        if (count($parts) != 2) {
          continue;
        }
        // Ensure that the first part is a valid path to redirect from
        $from = parse_url($parts[0], PHP_URL_PATH);
        if (!$from || substr($from, 0, 1) != '/') {
          continue;
        }
        // Ensure we are redirecting to a valid URL
        $to_parts = parse_url($parts[1]);
        if (!isset($to_parts['scheme']) || !isset($to_parts['host']) || substr($to_parts['scheme'], 0, 4) != 'http') {
          continue;
        }
        $to = $to_parts['scheme'] . '://' . $to_parts['host'];
        $to .= isset($to_parts['port']) ? ':' . $to_parts['port'] : '';
        $to .= isset($to_parts['path']) ? $to_parts['path'] : '/';
        $to .= isset($to_parts['query']) ? '?' . $to_parts['query'] : '';
        $to .= isset($to_parts['fragment']) ? '#' . $to_parts['fragment'] : '';
        // Add rule to final redirects array
        $redirects[$from] = $to;
      }
      // Cache rules for 60 seconds
      wp_cache_set('lre_rules', $redirects);
    }
    // Get current requested path
    $request_url = esc_url($_SERVER['REQUEST_URI']);
    // Redirect if there's a rule for the requested path
    if (isset($redirects[$request_url])) {
      wp_redirect($redirects[$request_url], 301);
      exit;
    }
  }
}

new LeanRedirectEditor();
