<?php
/*
Plugin Name: Table de matière - WAD TOC
Description: Ajoute une table des matières automatique et responsive via le shortcode [table_matiere].
Version: 1.0.0
Author: wadweb.fr
Author URI: https://wadweb.fr
License: GPL2
Text Domain: wadtoc
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define('WADTOC_VERSION', '1.0.0');
define('WADTOC_DIR', plugin_dir_path(__FILE__));
define('WADTOC_URL', plugin_dir_url(__FILE__));

// Inclure les fichiers nécessaires
require_once WADTOC_DIR . 'includes/shortcode.php';
require_once WADTOC_DIR . 'includes/admin.php';

// Charger CSS/JS public
add_action('wp_enqueue_scripts', function() {
    if (!is_singular()) return;
    wp_enqueue_style('wadtoc-css', WADTOC_URL . 'assets/wadtoc.css', [], WADTOC_VERSION);
    wp_enqueue_script('wadtoc-js', WADTOC_URL . 'assets/wadtoc.js', [], WADTOC_VERSION, true);
});

// Charger CSS admin uniquement sur la page d'options
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'toplevel_page_wadtoc-settings') {
        wp_enqueue_style('wadtoc-css', WADTOC_URL . 'assets/wadtoc.css', [], WADTOC_VERSION);
    }
});

// Ajouter menu admin
add_action('admin_menu', function() {
    add_menu_page(
        __('Table de matière', 'wadtoc'),
        __('Table de matière', 'wadtoc'),
        'manage_options',
        'wadtoc-settings',
        'wadtoc_render_settings_page',
        'dashicons-book',
        60
    );
});

// Activation : valeurs par défaut
register_activation_hook(__FILE__, function() {
    $defaults = [
        'wadtoc_headings' => ['h1','h2','h3'],
        'wadtoc_open' => '1',
        'wadtoc_title' => 'Sommaire',
        'wadtoc_bgcolor' => '#fefefe',
        'wadtoc_linkcolor' => '#444',
        'wadtoc_headercolor' => '#444',
        'wadtoc_iconcolor' => '#444',
        'wadtoc_maxwidth' => '',
    ];
    foreach ($defaults as $k => $v) {
        if (get_option($k) === false) {
            add_option($k, $v);
        }
    }
});
