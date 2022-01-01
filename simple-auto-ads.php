<?php
/**
 * Plugin Name: Simple Auto Ads
 * Version: 1.0
 * Description: Insert your ads with ease.
 * Author: Feriyadi Iskandar
 * Author URI: https://www.upwork.com/freelancers/~0151e239ab7aa2d5cc
 */

class SimpleAutoAds {

    private static $instance = null;

    public static function init() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {
        add_filter('the_content', array($this, 'sad_content'));
        add_action('admin_menu', array($this, 'sad_menu'));
        add_action('admin_init', array($this, 'sad_setting'));
    }

    public function sad_content($content) {
        global $post;
        if ( is_singular() && in_the_loop() && is_main_query() ) {
            $code = '<div style="text-align: center">' . get_option('ads-code') . '</div>';
            $content = $this->sad_insert($code, $content);
        }
        return $content;
    }

    public function sad_menu() {
        add_menu_page('Simple Auto Ads', 'Simple Auto Ads', 'manage_options', 'simple-auto-ads', array($this, 'sad_menu_cb'), 'dashicons-money-alt');
    }

    public function sad_menu_cb() {
        echo '<div class="wrap">';
        echo '<h1>Simple Auto Ads</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('simple-auto-ads');
        do_settings_sections('simple-auto-ads');
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function sad_setting() {
        add_settings_section('simple-auto-ads', '', '', 'simple-auto-ads');
        register_setting('simple-auto-ads', 'ads-code');
        add_settings_field('ads-code', 'Ads Code', array($this, 'sad_field_cb'), 'simple-auto-ads', 'simple-auto-ads');
    }

    public function sad_field_cb() {
        ?>
        <textarea class="large-text code" rows="15" name="ads-code"><?php echo get_option('ads-code'); ?></textarea>
        <?php            
    }

    private function sad_insert($insertion, $content) {
        $closing_p = '</p>';
        $paragraphs = explode( $closing_p, $content );
        if (count($paragraphs) >= $insertion) {
            $wordcount = 0;
            $p_index   = 0;
            foreach ($paragraphs as $index => $paragraph) {
                $realword   = strip_tags($paragraph);
                $wordcount += str_word_count($realword, 0);

                if ( trim( $paragraph ) ) {
                    $paragraphs[$index] .= $closing_p;
                }

                if($p_index == 0) {
                    $max = rand(50, 80);
                } else {
                    $max = rand(90, 150);
                }

                if ($wordcount > $max) {
                    $paragraphs[$index] .= $insertion;
                    $wordcount = 0;
                    $p_index += 1;
                }
            }
    
            return implode( '', $paragraphs );
        }
    
        return $content;
    }

}

SimpleAutoAds::init();
