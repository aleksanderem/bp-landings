<?php
/**
 * Plugin Name: BP Landings
 * Description: Landing pages custom post type with Elementor support and root-level URLs
 * Version: 1.1.0
 * Author: Alex M.
 * Text Domain: bp-landings
 */

defined('ABSPATH') || exit;

define('BP_LANDINGS_VERSION', '1.1.0');
define('BP_LANDINGS_PATH', plugin_dir_path(__FILE__));
define('BP_LANDINGS_URL', plugin_dir_url(__FILE__));

require_once BP_LANDINGS_PATH . 'includes/class-cpt-landings.php';
require_once BP_LANDINGS_PATH . 'includes/class-acf-fields.php';

// Load textdomain
add_action('init', function() {
    load_plugin_textdomain('bp-landings', false, dirname(plugin_basename(__FILE__)) . '/languages');
}, 0);

// Register CPT
add_action('init', ['BP_Landings_CPT', 'register']);

// Register ACF fields
add_action('acf/init', ['BP_Landings_ACF_Fields', 'register']);

// Register as Starter Dashboard addon (priority 5, before addon loader)
add_filter('starter_register_external_addons', function($addons) {
    $addons['bp-landings'] = [
        'name'         => __('BP Landings', 'bp-landings'),
        'description'  => __('Landing pages with Elementor support and root-level URLs', 'bp-landings'),
        'icon'         => 'browser',
        'category'     => 'integration',
        'file'         => BP_LANDINGS_PATH . 'includes/class-cpt-landings.php',
        'has_settings' => false,
        'version'      => BP_LANDINGS_VERSION,
        'plugin_file'  => BP_LANDINGS_PATH . 'bp-landings.php',
    ];
    return $addons;
}, 5);

// GitHub update checker (uses PUC library loaded by Starter Dashboard)
add_action('plugins_loaded', function() {
    if (class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory')) {
        $bp_landings_updater = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            'https://github.com/aleksanderem/bp-landings/',
            __FILE__,
            'bp-landings'
        );
        $bp_landings_updater->setBranch('main');
    }
}, 20);
