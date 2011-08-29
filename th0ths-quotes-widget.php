<?php

/*  This code is a part of the th0th's Quotes Wordpress plugin.  */

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
        if ($instance['owner'] == '')
        {
            if ($instance['tag'] == '')
            {
                $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table, ARRAY_A);
            }
            else
            {
                $pre_quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table, ARRAY_A);
                
                $quotes = array();
                
                foreach ($pre_quotes as $pre_quote)
                {
                    if (@unserialize($pre_quote['tags']))
                    {
                        if (in_array($instance['tag'], unserialize($pre_quote['tags'])))
                        {
                            $quotes[] = $pre_quote;
                        }
                    }
                }
            }
        }
        else
        {
            $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE owner = '" . $instance['owner'] . "'", ARRAY_A);
        }
        
        if (!empty($quotes))
        {
            $quote = $quotes[array_rand($quotes)];
        }
        else
        {
            $quote = array(
                'quote' => __("There is no quote with this tag/owner.", 'th0ths-quotes'),
                'owner' => __("Tux", 'th0ths-quotes'),
                'source' => ''
            );
        }
        ?>
        <?php echo $before_widget; ?>
        <?php if ( $title ) {
            echo $before_title . $title . $after_title; }
        else {
                echo $before_title . __("Quotes" , 'th0ths-quotes') . $after_title; } ?>
        <div id="th0ths_quotes_widget_quote" style="font-style: oblique;"><?php echo nl2br($quote['quote']); ?></div>
        <div id="th0ths_quotes_widget_owner" style="text-align: right;">
                      
        <?php $source_array = unserialize($quote['source']); ?>
                      
        <?php if (th0ths_quotes_is_valid_source($source_array[0])) { ?>
            <a href="<?php echo $source_array[0]; ?>" <?php if ($source_array[1] == 'true') { ?>target="_blank"<?php } ?>>-<?php echo $quote['owner']; ?></a>
        <?php } else  { ?>
            -<?php echo $quote['owner']; } ?>
        </div>
        <?php echo $after_widget; }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['tag'] = strip_tags($new_instance['tag']);
        $instance['owner'] = strip_tags($new_instance['owner']);
        
        return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $tag = esc_attr($instance['tag']);
        $owner = esc_attr($instance['owner']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo $tag; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('owner'); ?>"><?php _e('Owner:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('owner'); ?>" name="<?php echo $this->get_field_name('owner'); ?>" type="text" value="<?php echo $owner; ?>" />
        </p>
        <p class="description"><?php _e("Owner filter supersedes tag filter. So if 'owner' is entered 'tag' will be ignored."); ?></p>
        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("th0ths_Quotes_Widget");'));

?>
