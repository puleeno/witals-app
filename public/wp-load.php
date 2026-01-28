<?php
define('ABSPATH', __DIR__ . '/');
define('WPINC', 'wp-includes');

if (!defined('WP_USE_THEMES')) {
    define('WP_USE_THEMES', true);
}

function wp() { 
    global $wp;
    $wp = new stdClass();
    $wp->main = function() {};
}

function add_filter($tag, $function_to_add) {
    global $wp_filters;
    $wp_filters[$tag][] = $function_to_add;
}

function apply_filters($tag, $value) {
    global $wp_filters;
    if (isset($wp_filters[$tag])) {
        foreach ($wp_filters[$tag] as $callback) {
            $value = $callback($value);
        }
    }
    return $value;
}
