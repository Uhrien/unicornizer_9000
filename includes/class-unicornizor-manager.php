<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unicornizor_Manager {

    private $option_name = 'unicornizor_blocks';

    public function __construct() {}

    public function get_blocks() {
        return get_option($this->option_name, array());
    }

    public function get_block($block_id) {
        $blocks = $this->get_blocks();
        return isset($blocks[$block_id]) ? $blocks[$block_id] : false;
    }

    public function save_block($block_id, $block_data) {
        $blocks = $this->get_blocks();
        $blocks[$block_id] = $block_data;
        update_option($this->option_name, $blocks);
        return $block_id;
    }

    public function create_block($block_data) {
        $blocks = $this->get_blocks();
        $new_id = time();
        $blocks[$new_id] = $block_data;
        update_option($this->option_name, $blocks);
        return $new_id;
    }

    public function delete_block($block_id) {
        $blocks = $this->get_blocks();
        if(isset($blocks[$block_id])) {
            unset($blocks[$block_id]);
            update_option($this->option_name, $blocks);
            return true;
        }
        return false;
    }
}
