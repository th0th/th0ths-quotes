<?php

/*
Plugin Name: th0th's quotes
Plugin URI: https://returnfalse.net/log/
Description: A plugin that enables you to display a random quote from your collection on sidebar, posts and pages.
Version: 0.5
Author: Hüseyin Gökhan Sarı
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
$th0ths_quotes_plugin_version = '0.5';

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
    <?php
}

function th0ths_quotes_donate()
{
    ?>
    <div class="wrap">
        <h2>Donate</h2>
        <p><b>th0th's Quotes</b> is a free <i>(both free as in beer and freedom)</i> plugin released under terms of <a target="_blank" href="http://www.gnu.org/licenses/gpl-3.0-standalone.html">GPL</a>. However, if you liked this project you can support its development by a donation.</p>
        
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="7D75SD2JLPCMQ">
            <input type="image" src="https://www.paypalobjects.com/en_US/TR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
        
        <p>You can use Paypal button to donate.</p>
    </div>
    <?php
}

/* Administration menus */
function th0ths_quotes_add_administration_menus()
{
    /* add menu item */
    add_menu_page("th0th's Quotes", "th0th's Quotes", "manage_options", "th0ths-quotes", "th0ths_quotes_manage_quotes");
    
    /* add quote management submenu item */
    add_submenu_page("th0ths-quotes", "Manage Quotes", "Manage Quotes", "manage_options", "th0ths-quotes", "th0ths_quotes_manage_quotes");
    
    /* add new quote submenu item */
    add_submenu_page("th0ths-quotes", "Add New", "Add New", "manage_options", "th0ths-quotes-add-new", "th0ths_quotes_add_new");
    
    /* add import/export submenu item */
    add_submenu_page("th0ths-quotes", "Import/Export", "Import/Export", "manage_options", "th0ths-quotes-import-export", "th0ths_quotes_import_export");
    
    /* add trash submenu item */
    add_submenu_page("th0ths-quotes", "Trash", "Trash", "manage_options", "th0ths-quotes-trash", "th0ths_quotes_trash");

    /* add donate submenu item */
    add_submenu_page("th0ths-quotes", "Donate", "Donate", "manage_options", "th0ths-quotes-donate", "th0ths_quotes_donate");
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
    
    if (!empty($_POST))
    {
        if ($_POST['action'] == 'Send Selected Quotes to Trash')
        {
            if(isset($_POST['quoteIDs']))
            {
                foreach ($_POST['quoteIDs'] as $id)
                {
                    $wpdb->update($th0ths_quotes_plugin_table, array('status' => '0'), array( 'id' => $id ));
                }
            }
        }
    }
    
    $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE status = '1'", ARRAY_A);
    
    ?>
    
    <div class="wrap">
        <h2>Manage Quotes
            <a class="button add-new-h2" href="admin.php?page=th0ths-quotes-add-new">Add New</a>
        </h2>
        <div id="th0ths_quotes_edit_quotes">
            <form method="post" name="edit_quotes">
                <table id="th0ths_quotes_qlist" class="widefat">
                    <thead>
                        <tr>
                            <th class="sendToTrash"><input type="checkbox" onClick="checkAll('quoteCB',this)" /></th>
                            <th>ID</th>
                            <th>Quote</th>
                            <th>Owner</th>
                        </tr>
                        <?php foreach ($quotes as $quote) { ?>
                        <tr>
                            <td class="sendToTrash"><input type="checkbox" class="quoteCB" name="quoteIDs[]" value="<?php echo $quote['id']; ?>" /></td>
                            <td class="id"><?php echo $quote['id']; ?></td>
                            <td class="quote"><?php echo $quote['quote']; ?></td>
                            <td class="owner"><?php echo $quote['owner']; ?></td>
                        </tr>
                        <?php } ?>
                    </thead>
                </table>
                <input name="action" class="button" type="submit" value="Send Selected Quotes to Trash" />
            </form>
        </div>
        
    </div>
    
    <?php
}

function th0ths_quotes_add_new()
{
    global $wpdb, $th0ths_quotes_plugin_table;
    ?>
    <div class="wrap">
    <h2>Add New Quote</h2>
    <?php
    
    if (!empty($_POST))
    {
    if ($_POST['action'] == 'Add')
        {
            $new_quote = array (
                'quote' => $_POST['quote'],
                'owner' => $_POST['owner']
            );
            
            if (empty($new_quote['quote']) && empty($new_quote['owner']))
            {
                ?>
                <script type="text/javascript">
                    alert('You should fill both quote and owner sections.');
                    history.go(-1);
                </script>
                <?php
            }
            else
            {
                $wpdb->insert($th0ths_quotes_plugin_table, $new_quote);
                ?>
                <span>New quote is successfully added to collection.</span>
                <div class="th0ths_quotes_cleanser"></div>
                <span>Click <a href="admin.php?page=th0ths-quotes">here</a> to go to quote management page.</span>
                <?php
            }
        }
    }
    else
    {
    ?>
        <form method="post">
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
    <?php
    }
    ?>
    </div>
    <?php
}

/* import/export page */
function th0ths_quotes_import_export()
{
    global $wpdb, $th0ths_quotes_plugin_table;
    
    ?>
    <div class="wrap">
        <h2>Import/Export</h2>
    <?php
    if (isset($_FILES['import_quotes']))
    {
        if ($_FILES['import_quotes']['error'] > 0 ) // Error!
        {
            if ($_FILES['import_quotes']['error'] == 4)
            {
                echo "You should select a quote file to import quotes.";
            }
            else
            {
                echo "An error occured.";
            }
        }
        elseif ($_FILES['import_quotes']['type'] != 'text/xml')
        {
            echo "You can import quotes only from an XML file.";
        }
        else
        {
            $import_quotes = new DOMDocument();
            $import_quotes->load($_FILES['import_quotes']['tmp_name']);
            
            $xml_quotes = $import_quotes->getElementsByTagName("quote");
            
            $imported_quotes = array();
            $i = 0;
            
            foreach ($xml_quotes as $quote)
            {
                $quoted = $quote->getElementsByTagName("quoted"); 
                $imported_quotes[$i]['quote'] = $quoted->item(0)->nodeValue;
                
                $owner = $quote->getElementsByTagName("owner");
                $imported_quotes[$i]['owner'] = $owner->item(0)->nodeValue;
                
                $i = $i + 1;
            }
            
            foreach ($imported_quotes as $quote)
            {
                $wpdb->insert($th0ths_quotes_plugin_table, $quote);
            }
            
            echo "Quotes are imported. You can see new quotes in 'Manage Quotes' page.";
        } 
    }
    else
    {
    ?>
    
    <h3>Import Quotes</h3>
    <div id="th0ths_quotes_import_quotes" class="postbox">
        <form method="post" enctype="multipart/form-data">
            <label for="file">Filename:</label>
            <input type="file" name="import_quotes" id="file" /> 
            <div class="th0ths_quotes_cleanser"></div>
            <input type="submit" name="submit" value="Import Quotes" />
        </form>
    </div>
    <h3>Export Quotes</h3>
    <div id="th0ths_quotes_export_quotes">
        <a href="<?php echo add_query_arg("export", "true", admin_url() . "admin.php?page=th0ths-quotes-import-export"); ?>">Click here to export quotes</a>
    </div>
    <br /><br />
    <?php
    }
    ?>
    </div>
    <?php
}

/* exporting quotes to xml */
function th0ths_quotes_export_to_xml()
{
    global $wpdb, $th0ths_quotes_plugin_table;
    
    $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE status = '1'", ARRAY_A);
    
    header('Content-disposition: attachment; filename=quotes.xml');

    $exported = new DOMDocument;
    $exported->formatOutput = true;
    $root_element = $exported->createElement('exported_quotes');
    $exported->appendChild($root_element);
    
    foreach ($quotes as $quote)
    {
        $quote_element = $exported->createElement('quote');
        $root_element->appendChild($quote_element);
        
        $quote_text_element = $exported->createElement('quoted');
        $quote_element->appendChild($quote_text_element);
        
        $quote_text_c_element = $exported->createTextNode($quote['quote']);
        $quote_text_element->appendChild($quote_text_c_element);
        
        $quote_owner_element = $exported->createElement('owner');
        $quote_element->appendChild($quote_owner_element);
        
        $quote_owner_c_element = $exported->createTextNode($quote['owner']);
        $quote_owner_element->appendChild($quote_owner_c_element);
    }
    
    echo $exported->saveXML();
    die();
}

/* trash management function */
function th0ths_quotes_trash()
{
    global $wpdb, $th0ths_quotes_plugin_table;
    
    if (!empty($_POST))
    {
        if ($_POST['action'] == 'Restore Selected Quotes')
        {
            if (isset($_POST['quoteIDs']))
            {
                foreach ($_POST['quoteIDs'] as $id)
                {
                    $wpdb->update($th0ths_quotes_plugin_table, array('status' => '1'), array( 'id' => $id ));
                }
            }
        }
        elseif ($_POST['action'] == 'Delete Selected Quotes Permanently')
        {
            if (isset($_POST['quoteIDs']))
            {
                foreach ($_POST['quoteIDs'] as $id)
                {
                    $wpdb->query("DELETE FROM $th0ths_quotes_plugin_table WHERE id = '$id'");
                }
            }
        }
    }
    
    $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE status = '0'", ARRAY_A);
    
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

function th0ths_quotes_shortcode($atts)
{
    global $wpdb, $th0ths_quotes_plugin_table;

    extract(shortcode_atts(array(
                    'class' => 'th0ths_quotes_sc',
                    'id' => '',
                    'owner' => ''
                ), $atts));
    
    /* if 'id' is set owner attribute will be ignored */
    if ($id == '')
    {
        if ($owner == '')
        {
            $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table, ARRAY_A);
        }
        else
        {
            $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE owner = '$owner'", ARRAY_A);
        }

    $quote = $quotes[array_rand($quotes)];
    }
    else
    {
        $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE id = '$id'", ARRAY_A);
        
        if (!empty($quotes))
        {
            $quote = $quotes[0];
        }
        else
        {
            $quote = array(
                    'quote' => "There is no such quote with this ID.",
                    'owner' => "Inspector Gadget"
                );
        }
    }

    ?>
        <div class="<?php echo $class; ?>">
            <blockquote>
                <div id="th0ths_quotes_sc_quote" style="font-style: oblique;"><?php echo $quote['quote']; ?></div>
                <div id="th0ths_quotes_sc_owner" style="text-align: right;">-<?php echo $quote['owner']; ?></div>
            </blockquote>
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

/* register shortcodes */
add_shortcode('th0ths_quotes', 'th0ths_quotes_shortcode');

if (array_key_exists('page', $_GET) && array_key_exists('export', $_GET))
{
    if ($_GET['page'] == 'th0ths-quotes-import-export' && $_GET['export'] == 'true')
    {
        add_action('admin_init', 'th0ths_quotes_export_to_xml');
    }
}

include "th0ths-quotes-widget.php";

?>
