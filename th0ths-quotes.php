<?php

/*
Plugin Name: th0th's Quotes
Plugin URI: https://returnfalse.net/log/th0ths-quotes-wordpress-plugin/
Description: A plugin that enables you to display a random quote from your collection on sidebar, posts and pages.
Version: 0.94
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
$th0ths_quotes_plugin_version = '0.94';

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
          source VARCHAR(200),
          tags VARCHAR(255),
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
    global $th0ths_quotes_plugin_version, $th0ths_quotes_plugin_table, $wpdb;
    
    $current_version = get_option("th0ths_quotes_version");
    
    $mysql_columns_list = $wpdb->get_results("SHOW COLUMNS FROM $th0ths_quotes_plugin_table", 'ARRAY_A');
    
    $columns = array();
    
    foreach ($mysql_columns_list as $column)
    {
        $columns[] = $column['Field'];
    }
    
    if (!in_array('source', $columns))
    {
        $wpdb->query("ALTER TABLE $th0ths_quotes_plugin_table ADD source VARCHAR(200);");
    }
    
    if (!in_array('tags', $columns))
    {
        $wpdb->query("ALTER TABLE $th0ths_quotes_plugin_table ADD tags VARCHAR(255);");
    }
    
    # Check for existing quotes' source field
    $existing_quotes = $wpdb->get_results("SELECT * FROM $th0ths_quotes_plugin_table", 'ARRAY_A');
    
    foreach ($existing_quotes as $quote)
    {
        if (!empty($quote['source']) && !@unserialize($quote['source']))
        {
            $new_source = serialize(array($quote['source'], ''));
            
            $wpdb->update($th0ths_quotes_plugin_table, array('source' => $new_source), array('id' => $quote['id']));
        }
    }
    
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
        <h2><?php _e("Donate", 'th0ths-quotes'); ?></h2>
        <p><?php printf(__('%sth0th\'s Quotes%s is a free <i>(both free as in beer and freedom)</i> plugin released under terms of %sGPL%s. However, if you liked this project you can support its development by a donation.', 'th0ths-quotes'), '<b>', '</b>', '<a target="_blank" href="http://www.gnu.org/licenses/gpl-3.0-standalone.html">', '</a>'); ?></p>
        
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="7D75SD2JLPCMQ">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG_global.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
        
        <p><?php _e("You can use Paypal button to donate.", 'th0ths-quotes'); ?></p>
    </div>
    <?php
}

/* Administration menus */
function th0ths_quotes_add_administration_menus()
{
    /* add menu item */
    add_menu_page(__("th0th's Quotes", 'th0ths-quotes'), __("th0th's Quotes", 'th0ths-quotes'), "manage_options", "th0ths-quotes", "th0ths_quotes_manage_quotes");
    
    /* add quote management submenu item */
    add_submenu_page("th0ths-quotes", __("Manage Quotes", 'th0ths-quotes'), __("Manage Quotes", 'th0ths-quotes'), "manage_options", "th0ths-quotes", "th0ths_quotes_manage_quotes");
    
    /* add new quote submenu item */
    add_submenu_page("th0ths-quotes", __("Add New", 'th0ths-quotes'), __("Add New", 'th0ths-quotes'), "manage_options", "th0ths-quotes-add-new", "th0ths_quotes_add_new");
    
    /* add import/export submenu item */
    add_submenu_page("th0ths-quotes", __("Import/Export", 'th0ths-quotes'), __("Import/Export", 'th0ths-quotes'), "manage_options", "th0ths-quotes-import-export", "th0ths_quotes_import_export");
    
    /* add trash submenu item */
    add_submenu_page("th0ths-quotes", __("Trash", 'th0ths-quotes'), __("Trash", 'th0ths-quotes'), "manage_options", "th0ths-quotes-trash", "th0ths_quotes_trash");

    /* add donate submenu item */
    add_submenu_page("th0ths-quotes", __("Donate", 'th0ths-quotes'), __("Donate", 'th0ths-quotes'), "manage_options", "th0ths-quotes-donate", "th0ths_quotes_donate");
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
    
    if ($_GET['action'] == 'edit_quote')
    {
        if ($_POST['action'] == 'Submit')
        {
            
            if (empty($_POST['owner']) || empty($_POST['quote']))
            {
                ?>
                <script type="text/javascript">
                    alert('<?php _e("You should fill both quote and owner sections.", 'th0ths-quotes'); ?>');
                    history.go(-1);
                </script>
                <?php
            }
            else
            {            
                $edited_quote = array(
                    'id' => $_GET['id'],
                    'quote' => $_POST['quote'],
                    'owner' => $_POST['owner'],
                    'tags' => serialize(explode(',', $_POST['tags'])),
                    'source' => serialize(array($_POST['source'], $_POST['open_in_new_page']))
                );
                
                $wpdb->update($th0ths_quotes_plugin_table, $edited_quote, array('id' => $edited_quote['id']));
                
                ?>
                <div class="wrap">
                    <h2><?php _e("Edit Quote", 'th0ths-quotes'); ?></h2>
                    <span><?php _e("Quote is successfully updated.", 'th0ths-quotes'); ?></span>
                    <div class="th0ths_quotes_cleanser"></div>
                    <span><?php printf(__('Click %shere%s to go to quote management page.', 'th0ths-quotes'), '<a href="admin.php?page=th0ths-quotes">' ,'</a>', '<a href="admin.php?page=th0ths-quotes-add-new">', '</a>'); ?></span>
                <?php
            }
        }
        else
        {
            $quote = $wpdb->get_results("SELECT * FROM $th0ths_quotes_plugin_table WHERE id='" . $_GET['id'] . "'", 'ARRAY_A');
            ?>
            <div class="wrap">
            <h2><?php _e("Edit Quote", 'th0ths-quotes'); ?></h2>
            
            <form method="post">
                <div id="th0ths_quotes_new_quote" class="postbox">
                    <span><?php _e("Quote", 'th0ths-quotes'); ?></span>
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <textarea name="quote"><?php echo $quote[0]['quote']; ?></textarea>
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <span><?php _e("Owner", 'th0ths-quotes'); ?></span>
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <input type="text" name="owner" value="<?php echo $quote[0]['owner']; ?>" />
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <span><?php _e("Tags", 'th0ths-quotes'); ?></span>
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <input type="text" name="tags" value="<?php echo @implode(',', unserialize($quote[0]['tags'])); ?>" />
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <span><?php _e("Source", 'th0ths-quotes'); ?></span>
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <?php $source_array = unserialize($quote[0]['source']); ?>
                    
                    <input id="source" type="text" name="source" value="<?php echo $source_array[0]; ?>" /><span class="description">(<?php _e("Optional", 'th0ths-quotes'); ?>)</span>
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <label><input type="checkbox" name="open_in_new_page" value="true" <?php if ($source_array[1] == 'true') { ?>checked="checked"<?php } ?> /><?php _e("Open source link in new page", 'th0ths-quotes'); ?></label>
                    <div class="th0ths_quotes_cleanser"></div>
                    
                    <input name="action" class="button" type="submit" value="<?php _e("Submit", 'th0ths-quotes'); ?>" />
                    <a class="button" href="admin.php?page=th0ths-quotes"><?php _e("Go back", 'th0ths-quotes'); ?></a>
                </div>
            </form>
            </div>
            <?php
        }
    }
    else
    {
        if ($_POST['action'] == __("Send Selected Quotes to Trash", 'th0ths-quotes'))
        {
            if(isset($_POST['quoteIDs']))
            {
                foreach ($_POST['quoteIDs'] as $id)
                {
                    $wpdb->update($th0ths_quotes_plugin_table, array('status' => '0'), array( 'id' => $id ));
                }
            }
        }
        
        $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE status = '1'", ARRAY_A);
        
        ?>
        
        <div class="wrap">
            <h2><?php _e("Manage Quotes", 'th0ths-quotes'); ?>
                <a class="button add-new-h2" href="admin.php?page=th0ths-quotes-add-new"><?php _e("Add New", 'th0ths-quotes'); ?></a>
            </h2>
            <div id="th0ths_quotes_edit_quotes">
                <form method="post" name="edit_quotes">
                    <table id="th0ths_quotes_qlist" class="widefat">
                        <thead>
                            <tr>
                                <th class="sendToTrash"><input type="checkbox" onClick="checkAll('quoteCB',this)" /></th>
                                <th><?php _e("ID", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Edit", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Quote", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Owner", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Tags", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Source", 'th0ths-quotes'); ?></th>
                            </tr>
                            <?php foreach ($quotes as $quote) { ?>
                            <tr>
                                <td class="sendToTrash"><input type="checkbox" class="quoteCB" name="quoteIDs[]" value="<?php echo $quote['id']; ?>" /></td>
                                <td class="id"><?php echo $quote['id']; ?></td>
                                <td class="edit"><a href="<?php echo add_query_arg(array("action" => "edit_quote", "id" => $quote['id']), admin_url() . "admin.php?page=th0ths-quotes"); ?>"><img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/edit.png" /></a></td>
                                <td class="quote"><?php echo $quote['quote']; ?></td>
                                <td class="owner"><?php echo $quote['owner']; ?></td>
                                
                                <?php $tags = @implode(',', unserialize($quote['tags'])); ?>
                                
                                <td class="tags">
                                    <a title="<?php echo $tags; ?>">
                                        <?php if (!empty($tags)) { ?>
                                        <img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/tags.png" />
                                        <?php } else { ?>
                                        <img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/notags.png" />
                                        <?php } ?>
                                    </a>
                                </td>
                                
                                <?php $source_array = @unserialize($quote['source']); ?>
                                
                                <td class="source"><?php if (th0ths_quotes_is_valid_source($source_array[0])) { ?><a title="<?php echo $source_array[0]; ?>" href="<?php echo $source_array[0]; ?>" target="_blank"><img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/link.png" /></a><?php } else {?><a title="No link"><img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/nolink.png" /></a><?php } ?></td>
                            </tr>
                            <?php } ?>
                        </thead>
                    </table>
                    <input name="action" class="button" type="submit" value="<?php _e("Send Selected Quotes to Trash", 'th0ths-quotes'); ?>" />
                </form>
            </div>
            
        </div>
        
        <?php
    }
}

function th0ths_quotes_add_new()
{
    global $wpdb, $th0ths_quotes_plugin_table;
    ?>
    <div class="wrap">
    <h2><?php _e("Add New Quote", 'th0ths-quotes'); ?></h2>
    <?php
    
    if (!empty($_POST))
    {
    if ($_POST['action'] == __("Add", 'th0ths-quotes'))
        {
            $new_quote = array (
                'quote' => $_POST['quote'],
                'owner' => $_POST['owner'],
                'tags' => serialize(explode(',', $_POST['tags'])),
                'source' => serialize(array($_POST['source'], $_POST['open_in_new_page']))
            );
            
            if (empty($new_quote['quote']) || empty($new_quote['owner']))
            {
                ?>
                <script type="text/javascript">
                    alert('<?php _e("You should fill both quote and owner sections.", 'th0ths-quotes'); ?>');
                    history.go(-1);
                </script>
                <?php
            }
            else
            {
                $wpdb->insert($th0ths_quotes_plugin_table, $new_quote);
                ?>
                <span><?php _e("New quote is successfully added to collection.", 'th0ths-quotes'); ?></span>
                <div class="th0ths_quotes_cleanser"></div>
                <span><?php printf(__('Click %shere%s to go to quote management page or %shere%s to add a new quote.', 'th0ths-quotes'), '<a href="admin.php?page=th0ths-quotes">' ,'</a>', '<a href="admin.php?page=th0ths-quotes-add-new">', '</a>'); ?></span>
                <?php
            }
        }
    }
    else
    {
    ?>
        <form method="post">
            <div id="th0ths_quotes_new_quote" class="postbox">
                <span><?php _e("Quote", 'th0ths-quotes'); ?></span>
                <div class="th0ths_quotes_cleanser"></div>
                
                <textarea name="quote"></textarea>
                <div class="th0ths_quotes_cleanser"></div>
                
                <span><?php _e("Owner", 'th0ths-quotes'); ?></span>
                <div class="th0ths_quotes_cleanser"></div>
                
                <input type="text" name="owner" />
                <div class="th0ths_quotes_cleanser"></div>
                
                <span><?php _e("Tags", 'th0ths-quotes'); ?></span>
                <div class="th0ths_quotes_cleanser"></div>
                
                <input type="text" name="tags" /><span class="description">(<?php _e("Seperate tags with comma", 'th0ths-qu0tes'); ?>)</span>
                <div class="th0ths_quotes_cleanser"></div>
                
                <span><?php _e("Source", 'th0ths-quotes'); ?></span>
                <div class="th0ths_quotes_cleanser"></div>
                
                <input id="source" type="text" name="source" value="http://" /><span class="description">(<?php _e("Optional", 'th0ths-quotes'); ?>)</span>
                <div class="th0ths_quotes_cleanser"></div>
                
                <label><input type="checkbox" name="open_in_new_page" value="true" /><?php _e("Open source link in new page", 'th0ths-quotes'); ?></label>
                <div class="th0ths_quotes_cleanser"></div>
                
                <input name="action" class="button" type="submit" value="<?php _e("Add", 'th0ths-quotes'); ?>" />
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
        <h2><?php _e("Import/Export", 'th0ths-quotes'); ?></h2>
    <?php
    if (isset($_FILES['import_quotes']))
    {
        if ($_FILES['import_quotes']['error'] > 0 ) // Error!
        {
            if ($_FILES['import_quotes']['error'] == 4)
            {
                _e("You should select a quote file to import quotes.", 'th0ths-quotes');
            }
            else
            {
                echo "An error occured.";
            }
        }
        elseif ($_FILES['import_quotes']['type'] != 'text/xml')
        {
            _e("You can import quotes only from an XML file.", 'th0ths-quotes');
        }
        else
        {
            $import_quotes = new DOMDocument();
            $import_quotes->load($_FILES['import_quotes']['tmp_name']);
            
            $xml_quotes = $import_quotes->getElementsByTagName('quote');
            
            $imported_quotes = array();
            $i = 0;
            
            foreach ($xml_quotes as $quote)
            {
                $quoted = $quote->getElementsByTagName('quoted'); 
                $imported_quotes[$i]['quote'] = $quoted->item(0)->nodeValue;
                
                $owner = $quote->getElementsByTagName('owner');
                $imported_quotes[$i]['owner'] = $owner->item(0)->nodeValue;
                
                $tags = $quote->getElementsByTagName('tags');
                $imported_quotes[$i]['tags'] = $tags->item(0)->nodeValue;
                
                $source = $quote->getElementsByTagName('source');
                
                $source_url = $source->item(0)->getElementsByTagName('URL')->item(0)->nodeValue;
                $source_behaviour = $source->item(0)->getElementsByTagName('open_in_new_page')->item(0)->nodeValue;
                
                $imported_quotes[$i]['source'] = serialize(array($source_url, $source_behaviour));
                
                $i = $i + 1;
            }
            
            foreach ($imported_quotes as $quote)
            {
                $wpdb->insert($th0ths_quotes_plugin_table, $quote);
            }
            
            _e("Quotes are imported. You can see new quotes in 'Manage Quotes' page.", 'th0ths-quotes');
        } 
    }
    else
    {
    ?>
    
    <h3><?php _e("Import Quotes", 'th0ths-quotes'); ?></h3>
    <div id="th0ths_quotes_import_quotes" class="postbox">
        <form method="post" enctype="multipart/form-data">
            <label for="file"><?php _e("Filename", 'th0ths-quotes'); ?>:</label>
            <input type="file" name="import_quotes" id="file" /> 
            <div class="th0ths_quotes_cleanser"></div>
            <input type="submit" name="submit" value="<?php _e("Import Quotes", 'th0ths-quotes'); ?>" />
        </form>
    </div>
    <h3><?php _e("Export Quotes", 'th0ths-quotes'); ?></h3>
    <div id="th0ths_quotes_export_quotes">
        <a href="<?php echo add_query_arg("export", "true", admin_url() . "admin.php?page=th0ths-quotes-import-export"); ?>"><?php _e("Click here to export quotes", 'th0ths-quotes'); ?></a>
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
        
        $quote_tags_element = $exported->createElement('tags');
        $quote_element->appendChild($quote_tags_element);
        
        $quote_tags_c_element = $exported->createTextNode(@implode(',', unserialize($quote['tags'])));
        $quote_tags_element->appendChild($quote_tags_c_element);
        
        $quote_source_element = $exported->createElement('source');
        $quote_element->appendChild($quote_source_element);
        
        $quote_source = unserialize($quote['source']);
        
        $source = array(
            'URL' => $quote_source[0],
            'open_in_new_page' => $quote_source[1]
        );
        
        $quote_source_url_element = $exported->createElement('URL');
        $quote_source_element->appendChild($quote_source_url_element);
        
        $quote_source_url = $exported->createTextNode($source['URL']);
        $quote_source_url_element->appendChild($quote_source_url);
        
        $quote_source_behaviour_element = $exported->createElement('open_in_new_page');
        $quote_source_element->appendChild($quote_source_behaviour_element);
        
        $quote_source_behaviour = $exported->createTextNode($source['open_in_new_page']);
        $quote_source_behaviour_element->appendChild($quote_source_behaviour);
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
        if ($_POST['action'] == __('Restore Selected Quotes', 'th0ths-quotes'))
        {
            if (isset($_POST['quoteIDs']))
            {
                foreach ($_POST['quoteIDs'] as $id)
                {
                    $wpdb->update($th0ths_quotes_plugin_table, array('status' => '1'), array( 'id' => $id ));
                }
            }
        }
        elseif ($_POST['action'] == __('Delete Selected Quotes Permanently', 'th0ths-quotes'))
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
        <h2><?php _e("Trash", 'th0ths-quotes'); ?></h2>
        <div id="th0ths_quotes_edit_quotes">
            <form method="post" name="edit_quotes">
                <table id="th0ths_quotes_qlist" class="widefat">
                    <thead>
                        <tr>
                            <th class="sendToTrash"><input type="checkbox" onClick="checkAll('quoteCB',this)" /></th>
                                <th><?php _e("ID", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Quote", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Owner", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Tags", 'th0ths-quotes'); ?></th>
                                <th><?php _e("Source", 'th0ths-quotes'); ?></th>
                        </tr>
                        <?php foreach ($quotes as $quote) { ?>
                        <tr>
                                <td class="sendToTrash"><input type="checkbox" class="quoteCB" name="quoteIDs[]" value="<?php echo $quote['id']; ?>" /></td>
                                <td class="id"><?php echo $quote['id']; ?></td>
                                <td class="quote"><?php echo $quote['quote']; ?></td>
                                <td class="owner"><?php echo $quote['owner']; ?></td>
                                
                                <?php $tags = @implode(',', unserialize($quote['tags'])); ?>
                                
                                <td class="tags">
                                    <a title="<?php echo $tags; ?>">
                                        <?php if (!empty($tags)) { ?>
                                        <img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/tags.png" />
                                        <?php } else { ?>
                                        <img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/notags.png" />
                                        <?php } ?>
                                    </a>
                                </td>
                                
                                <?php $source_array = unserialize($quote['source']); ?>
                                
                                <td class="source"><?php if (th0ths_quotes_is_valid_source($source_array[0])) { ?><a title="<?php echo $source_array[0]; ?>" href="<?php echo $source_array[0]; ?>" target="_blank"><img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/link.png" /></a><?php } else {?><a title="No link"><img src="<?php echo WP_PLUGIN_URL; ?>/th0ths-quotes/images/nolink.png" /></a><?php } ?></td>
                        </tr>
                        <?php } ?>
                    </thead>
                </table>
                <input name="action" class="button" type="submit" value="<?php _e("Restore Selected Quotes", 'th0ths-quotes'); ?>" />
                <input name="action" class="button" type="submit" value="<?php _e("Delete Selected Quotes Permanently", 'th0ths-quotes'); ?>" />
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
                    'owner' => '',
                    'tag' => ''
                ), $atts));
    
    /* if 'id' is set owner attribute will be ignored */
    if ($id == '')
    {
        if ($owner == '')
        {
            if ($tag == '')
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
                        if (in_array($tag, unserialize($pre_quote['tags'])))
                        {
                            $quotes[] = $pre_quote;
                        }
                    }
                }
            }
        }
        else
        {
            $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE owner = '$owner'", ARRAY_A);
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
    
    }
    else
    {
        $quotes = $wpdb->get_results("SELECT * FROM " . $th0ths_quotes_plugin_table . " WHERE id = '$id'", ARRAY_A);
        
        if (!empty($quotes) && $quotes[0]['status'] == 1)
        {
            $quote = $quotes[0];
        }
        else
        {
            $quote = array(
                    'quote' => __("No such quote with this ID.", 'th0ths-quotes'),
                    'owner' => __("Inspector Gadget", 'th0ths-quotes'),
                    'source' => ''
                );
        }
    }

    ?>
        <div class="<?php echo $class; ?>">
            <blockquote>
                <div id="th0ths_quotes_sc_quote" style="font-style: oblique;"><?php echo $quote['quote']; ?></div>
                <div id="th0ths_quotes_sc_owner" style="text-align: right;">
                    
                    <?php $source_array = unserialize($quote['source']); ?>
                    
                    <?php if (th0ths_quotes_is_valid_source($source_array[0])) { ?><a href="<?php echo $source_array[0]; ?>" <?php if ($source_array[1] == 'true') { ?>target="_blank"<?php } ?>>-<?php echo $quote['owner']; ?></a><?php } else { ?>
                    -<?php echo $quote['owner']; } ?>
                </div>
            </blockquote>
        </div>
    <?php
}

function th0ths_quotes_is_valid_source($url)
{
    if ($url == 'http://' || $url == '')
    {
        return false;
    }
    else
    {
        return true;
    }
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

load_plugin_textdomain('th0ths-quotes', false, dirname(plugin_basename(__FILE__)) . '/languages/');

include "th0ths-quotes-widget.php";

?>
