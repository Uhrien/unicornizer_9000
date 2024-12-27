<?php
/**
 * Plugin Name: Unicornizor 9000
 * Plugin URI:  https://esempio.com
 * Description: Crea blocchi di elementi "unicornosi" con posizionamento e animazione personalizzati (live preview in admin). Ora con sfondo trasparente, bounding box ottimizzata e una super UI a griglia!
 * Version:     1.1
 * Author:      TuoNome
 * Author URI:  https://esempio.com
 * Text Domain: unicornizor-9000
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Security
}

// Includiamo i file
require_once plugin_dir_path(__FILE__) . 'includes/class-unicornizor-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-unicornizor-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-unicornizor-admin.php';

class Unicornizor9000 {

    private $manager;
    private $admin;
    private $shortcodes;

    public function __construct() {
        $this->manager = new Unicornizor_Manager();
        if ( is_admin() ) {
            $this->admin = new Unicornizor_Admin( $this->manager );
        }
        $this->shortcodes = new Unicornizor_Shortcodes( $this->manager );

        // Enqueue assets
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_assets' ) );
    }

    public function enqueue_admin_assets() {
        // CSS admin (animazioni e nuova UI)
        wp_enqueue_style(
            'unicornizor-admin-css',
            plugin_dir_url(__FILE__) . 'assets/css/unicornizor-admin.css',
            array(),
            '1.0'
        );

        // jQuery UI (draggable)
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-draggable');

        // Admin JS
        wp_enqueue_script(
            'unicornizor-admin-js',
            plugin_dir_url(__FILE__) . 'assets/js/unicornizor-admin.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'),
            '1.1',
            true
        );
    }

    public function enqueue_front_assets() {
        // CSS front
        wp_enqueue_style(
            'unicornizor-front-css',
            plugin_dir_url(__FILE__) . 'assets/css/unicornizor-front.css',
            array(),
            '1.1'
        );

        // JS front (per bounding box minima, highlight se vuoi)
        wp_enqueue_script(
            'unicornizor-front-js',
            plugin_dir_url(__FILE__) . 'assets/js/unicornizor-front.js',
            array('jquery'),
            '1.1',
            true
        );
    }
}

new Unicornizor9000();
