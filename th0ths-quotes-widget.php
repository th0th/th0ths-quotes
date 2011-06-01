<?php

/*  Copyright 2011 Hüseyin Gökhan Sarı  (email : th0th -at- returnfalse.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class th0ths_Quotes_Widget extends WP_Widget {
    function th0ths_quotes_widget() {
        parent::WP_Widget(false, $name = 'Quotes');    
    }

    function widget($args, $instance) {
		global $wpdb, $th0ths_quotes_plugin_table;
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE status = '1'", 'ARRAY_A');
        $quote = $quotes[array_rand($quotes)];
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title ) {
                            echo $before_title . $title . $after_title; }
                        else {
                                echo $before_title . "Quotes" . $after_title; } ?>
                  <div id="th0ths_quotes_widget_quote" style="font-style: oblique;"><?php echo $quote['quote']; ?></div>
                  <div id="th0ths_quotes_widget_owner" style="text-align: right;">-<?php echo $quote['owner']; ?></div>
              <?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("th0ths_Quotes_Widget");'));

?>
