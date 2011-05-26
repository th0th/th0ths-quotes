<?php

/*
Plugin Name: th0th's quotes
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Widget for displaying random quotes from your collection.
Version: 0.2
Author: H.Gökhan Sarı
Author URI: http://returnfalse.net
License: GPL3
*/

/*  Copyright 2011 Hüseyin Gökhan Sarı  (email : th0th -at- returnfalse.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $wpdb;
global $th0ths_quotes_plugin_table;
global $th0ths_quotes_plugin_version;

$th0ths_quotes_plugin_table = $wpdb->prefix . "th0ths_quotes";
$th0ths_quotes_plugin_version = '0.3';

/* Plugin activation function */
function th0ths_quotes_activate()
{
    global $wpdb, $th0ths_quotes_plugin_table, $th0ths_quotes_plugin_version;
    
    if($wpdb->get_var("SHOW TABLES LIKE '$th0ths_quotes_plugin_table'") != $th0ths_quotes_plugin_table)
    {
        $sql = "CREATE TABLE " . $th0ths_quotes_plugin_table . " (
          id INT(12) NOT NULL AUTO_INCREMENT,
          quote VARCHAR(255) NOT NULL,
          owner VARCHAR(100) NOT NULL,
          status INT(4) DEFAULT '1',
          UNIQUE KEY id (id)
        );";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Let's add some inital quotes.
        $inital_quotes = array();
        
        $initial_quotes[] = array(
            "quote" => "Happiness is a shadow of harmony; it follows harmony. There is no other way to be happy.",
            "owner" => "Osho"
        );
        
        $initial_quotes[] = array(
            "quote" => "It is better to travel well than to arrive.",
            "owner" => "Buddha"
        );
        
        foreach ($initial_quotes as $quote)
        {
            $row = $wpdb->insert($th0ths_quotes_plugin_table, $quote);
        }
        
        add_option("th0ths_quotes_version", $th0ths_quotes_plugin_version);
    }
}

/* JS */
function th0ths_quotes_admin_head_js()
{
	?>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/admin-head.js"></script>
<?php }

/* Administration menus */
function th0ths_quotes_add_administration_menus()
{
    /* add menu item */
    add_menu_page("th0th's Quotes", "th0th's Quotes", "manage_options", "th0ths-quotes", "th0ths_quotes_manage_quotes");
    
    /* add submenu item */
    add_submenu_page("th0ths-quotes", "th0th's Quotes", "th0th's Quotes", "manage_options", "th0ths-quotes", "th0ths_quotes_manage_quotes");
}

/* Adding CSS */
function th0ths_quotes_include_css()
{
    ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/th0ths-quotes.css" />
    <?php
}

/* Quote management page */
function th0ths_quotes_manage_quotes()
{
	global $wpdb, $th0ths_quotes_plugin_table;
	
	if (!empty($_POST)) /* Action can be both add or delete */
	{
		if ($_POST['action'] == 'addQuote')
		{
			$new_quote = array (
				'quote' => $_POST['quote'],
				'owner' => $_POST['owner']
			);
			
			if (!(empty($new_quote['quote']) || empty($new_quote['owner'])))
			{
				$wpdb->insert($th0ths_quotes_plugin_table, $new_quote);
			}
			else
			{
				echo "error";
			}
		}
		elseif ($_POST['action'] == 'delQuote')
		{
			foreach ($_POST['delQuoteID'] as $id)
			{
				$wpdb->query("DELETE FROM $th0ths_quotes_plugin_table WHERE id = '$id'");
			}
		}
	}
	
	$quotes = $wpdb->get_results('SELECT * FROM ' . $th0ths_quotes_plugin_table, 'ARRAY_A');
	
    echo "<h2>" . __("th0th's Quotes Management") . "</h2>";
    ?>
    
    <div class="wrap">
		<div id="th0ths_quotes_edit_quotes">
			<form method="post" name="edit_quotes">
				<table id="th0ths_quotes_qlist" class="widefat">
					<thead>
						<tr>
							<th class="deleteLink"><input type="checkbox" onClick="checkAll('delQuoteCB',this)" /></th>
							<th>Quote</th>
							<th>Owner</th>
						</tr>
						<?php foreach ($quotes as $quote) { ?>
						<tr>
							<td class="deleteLink"><input type="checkbox" class="delQuoteCB" name="delQuoteID[]" value="<?php echo $quote['id']; ?>" /></td>
							<td class="quote"><?php echo $quote['quote']; ?></td>
							<td class="owner"><?php echo $quote['owner']; ?></td>
						</tr>
						<?php } ?>
					</thead>
				</table>
				<input type="hidden" name="action" value="delQuote" />
				<input class="button" type="submit" value="Delete Selected Quotes" />
			</form>
		</div>
		<form method="post">
			<h3 class="hndle">Add new quote</h3>
			<div id="th0ths_quotes_new_quote" class="postbox">
				<span>Quote</span>
				<div class="th0ths_quotes_cleanser"></div>
				
				<textarea name="quote"></textarea>
				<div class="th0ths_quotes_cleanser"></div>
				
				<span>Owner</span>
				<div class="th0ths_quotes_cleanser"></div>
				
				<input type="text" name="owner" />
				<div class="th0ths_quotes_cleanser"></div>
				
				<input type="hidden" name="action" value="addQuote" />
				<input class="button" type="submit" value="Add" />
			</div>
		</form>
    </div>
    
    <?php
}


/* registering functions */
register_activation_hook(__FILE__, 'th0ths_quotes_activate');

/* registering administration options */
add_action('admin_menu', 'th0ths_quotes_add_administration_menus');

/* add plugin stylesheet to admin_head */
add_action('admin_head', 'th0ths_quotes_include_css');

/* add js to admin_head */
add_action('admin_head', 'th0ths_quotes_admin_head_js');

include "th0ths-quotes-widget.php";

?>
