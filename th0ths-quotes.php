<?php

/*
Plugin Name: th0th's quotes
Plugin URI: https://returnfalse.net/log/
Description: Widget for displaying random quotes from your collection.
Version: 0.4
Author: H.Gökhan Sarı
Author URI: http://returnfalse.net
License: GPL3
*/

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

global $wpdb;
global $th0ths_quotes_plugin_table;
global $th0ths_quotes_plugin_version;

$th0ths_quotes_plugin_table = $wpdb->prefix . "th0ths_quotes";
$th0ths_quotes_plugin_version = '0.4';

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

/* Plugin upgrade function */
function th0ths_quotes_upgrade_check()
{
	global $th0ths_quotes_plugin_version;
	
	$current_version = get_option("th0ths_quotes_version");
	
	if ($current_version != $th0ths_quotes_plugin_version)
	{
		update_option("th0ths_quotes_version", $th0ths_quotes_plugin_version);
	}
}

/* Plugin deactivation function */
function th0ths_quotes_deactivate()
{
	delete_option("th0ths_quotes_version");
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
    
    /* add quote management submenu item */
    add_submenu_page("th0ths-quotes", "Manage Quotes", "Manage Quotes", "manage_options", "th0ths-quotes", "th0ths_quotes_manage_quotes");
    
    /* add import/export submenu item */
    add_submenu_page("th0ths-quotes", "Import/Export", "Import/Export", "manage_options", "th0ths-quotes-import-export", "th0ths_quotes_import_export");
    
    /* add trash submenu item */
    add_submenu_page("th0ths-quotes", "Trash", "Trash", "manage_options", "th0ths-quotes-trash", "th0ths_quotes_trash");
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
		if ($_POST['action'] == 'Add')
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
				?>
				<script type="text/javascript">
					alert('You should fill both quote and owner sections.');
				</script>
				<?php
			}
		}
		elseif ($_POST['action'] == 'Send Selected Quotes to Trash')
		{
			foreach ($_POST['quoteIDs'] as $id)
			{
				$wpdb->update($th0ths_quotes_plugin_table, array('status' => '0'), array( 'id' => $id ));
			}
		}
	}
	
	$quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE status = '1'", 'ARRAY_A');
	
    ?>
    
    <div class="wrap">
		<h2>Manage Quotes</h2>
		<div id="th0ths_quotes_edit_quotes">
			<form method="post" name="edit_quotes">
				<table id="th0ths_quotes_qlist" class="widefat">
					<thead>
						<tr>
							<th class="sendToTrash"><input type="checkbox" onClick="checkAll('quoteCB',this)" /></th>
							<th>Quote</th>
							<th>Owner</th>
						</tr>
						<?php foreach ($quotes as $quote) { ?>
						<tr>
							<td class="sendToTrash"><input type="checkbox" class="quoteCB" name="quoteIDs[]" value="<?php echo $quote['id']; ?>" /></td>
							<td class="quote"><?php echo $quote['quote']; ?></td>
							<td class="owner"><?php echo $quote['owner']; ?></td>
						</tr>
						<?php } ?>
					</thead>
				</table>
				<input name="action" class="button" type="submit" value="Send Selected Quotes to Trash" />
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
				
				<input name="action" class="button" type="submit" value="Add" />
			</div>
		</form>
    </div>
    
    <?php
}

/* import/export page */
function th0ths_quotes_import_export()
{
    ?>
    
    <div class="wrap">
		<h2>Import/Export</h2>
        <h3>Import Quotes</h3>
        <div id="th0ths_quotes_import_quotes" class="postbox">
            <form method="post" enctype="multipart/form-data">
                <label for="file">Filename:</label>
                <input type="file" name="file" id="file" /> 
                <br />
                <input type="submit" name="submit" value="Import Quotes" />
            </form>
        </div>
        <h3>Export Quotes</h3>
        <div id="th0ths_quotes_import_quotes">
            <span>Click here to export quotes</span>
        </div>
    </div>
    <br /><br />
    <?php
    th0ths_quotes_export_n_download();
}

/* exporting quotes to xml */
function th0ths_quotes_export_n_download()
{
    global $wpdb, $th0ths_quotes_plugin_table;
    
    $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table, ARRAY_A);
    
    echo "<pre>";
    echo htmlentities("<?xml version=\"1.0\"?>");
    echo htmlentities("<quotes>");

    foreach ($quotes as $quote) {
        echo "hi";
    }
    echo "</pre>";
}

/* trash management function */
function th0ths_quotes_trash()
{
	global $wpdb, $th0ths_quotes_plugin_table;
	
	if (!empty($_POST))
	{
		if ($_POST['action'] == 'Restore Selected Quotes')
		{
			foreach ($_POST['quoteIDs'] as $id)
			{
				$wpdb->update($th0ths_quotes_plugin_table, array('status' => '1'), array( 'id' => $id ));
			}
		}
		elseif ($_POST['action'] == 'Delete Selected Quotes Permanently')
		{
			foreach ($_POST['quoteIDs'] as $id)
			{
				$wpdb->query("DELETE FROM $th0ths_quotes_plugin_table WHERE id = '$id'");
			}
		}
	}
	
	$quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE status = '0'", 'ARRAY_A');
	
    ?>
    
    <div class="wrap">
		<h2>Trash</h2>
		<div id="th0ths_quotes_edit_quotes">
			<form method="post" name="edit_quotes">
				<table id="th0ths_quotes_qlist" class="widefat">
					<thead>
						<tr>
							<th class="sendToTrash"><input type="checkbox" onClick="checkAll('quoteCB',this)" /></th>
							<th>Quote</th>
							<th>Owner</th>
						</tr>
						<?php foreach ($quotes as $quote) { ?>
						<tr>
							<td class="sendToTrash"><input type="checkbox" class="quoteCB" name="quoteIDs[]" value="<?php echo $quote['id']; ?>" /></td>
							<td class="quote"><?php echo $quote['quote']; ?></td>
							<td class="owner"><?php echo $quote['owner']; ?></td>
						</tr>
						<?php } ?>
					</thead>
				</table>
				<input name="action" class="button" type="submit" value="Restore Selected Quotes" />
				<input name="action" class="button" type="submit" value="Delete Selected Quotes Permanently" />
			</form>
		</div>
		<?php
}


/* registering functions */
register_activation_hook(__FILE__, 'th0ths_quotes_activate');
register_deactivation_hook( __FILE__, 'th0ths_quotes_deactivate' );

/* upgrade version check function */
add_action('plugins_loaded', 'th0ths_quotes_upgrade_check');

/* registering administration options */
add_action('admin_menu', 'th0ths_quotes_add_administration_menus');

/* add plugin stylesheet to admin_head */
add_action('admin_head', 'th0ths_quotes_include_css');

/* add js to admin_head */
add_action('admin_head', 'th0ths_quotes_admin_head_js');

include "th0ths-quotes-widget.php";

?>
