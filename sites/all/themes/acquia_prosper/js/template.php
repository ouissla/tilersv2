<?php
// $Id: template.php,v 1.5 2010/09/17 21:36:06 eternalistic Exp $

function acquia_prosper_theme() {
  return array(
    // The form ID.
    'user_login_block' => array(
      // Forms always take the form argument.
      'arguments' => array('form' => NULL),
    ),
  );
}

function acquia_prosper_preprocess_page(&$variables){
    if ($variables['is_front']){

        drupal_add_js("sites/all/themes/acquia_prosper/js/jcarousel/lib/jquery-1.4.2.min.js", "module", "header");
        drupal_add_js("sites/all/themes/acquia_prosper/js/jcarousel/lib/jquery.jcarousel.js", "module", "header");
        drupal_add_css("sites/all/themes/acquia_prosper/js/jcarousel/skins/tango/skin.css", "module", "header");
        $variables['scripts'] = drupal_get_js();
        $variables['styles'] = drupal_get_css();

        $sql = db_query('SELECT nid FROM {node} WHERE type = "%s" ORDER BY nid ASC LIMIT 1', "homepage");
        $nid = db_fetch_object($sql);
        $node = node_load($nid->nid);
        $variables['node'] = $node;

        $top_sellers = '<ul id="top_sellers_list">';
        foreach ($node->field_homepage_topsellers as $top){
            $this_node = node_load($top['nid']);
            $top_sellers .= '<li>' . l($this_node->title, 'node/' . $this_node->nid) . '</li>';
        }
        $top_sellers .= '</ul>';
        $variables['top_sellers'] = $top_sellers;


        /*$specials_offers = db_query('SELECT c.field_special_url_value, f.filepath
            FROM content_type_special c
            LEFT JOIN files f ON c.field_special_image_fid = f.fid');*/

        $arr_pics = array();
        while ($special = db_fetch_object($specials_offers)){
            //$data = unserialize($special->field_special_image_data);
            $arr_pics[] = array(
                'filepath' => $special->filepath,
                //'title'    => $data['title'],
                //'alt'      => $data['alt'],
                'url'      => $special->field_special_url_value
            );
        }
        $variables['carrousel_pictures'] = $arr_pics;


    }
    
	drupal_add_js("sites/all/themes/acquia_prosper/js/jquery.resizeOnApproach.1.0.js", "module", "header");
        $variables['scripts'] = drupal_get_js();
    if (arg(0) == 'node' && is_numeric(arg(1))){
        $tabs = $variables['tabs'];
	
	$tab_content = '';
	if (isset($_GET['variation']) && $_GET['variation'] != ''){
	  $main_product = db_fetch_object(db_query('SELECT nid FROM {uc_product_options} WHERE oid = %d LIMIT 1', $_GET['variation']));
	  if (is_object($main_product)){
	    $tab_content .= '<li>' . l('View main product', 'node/' . $main_product->nid) . '</li>
			   <li>' . l('Edit main product', '/node/' . $main_product->nid . '/edit') . '</li>
			  <li>' . l('Stock', 'node/' . $main_product->nid . '/edit/stock') . '</li>';
	  }
	} else {
	  $tab_content .= '<li>' . l('Stock', 'node/' . arg(1) . '/edit/stock') . '</li>
			  <li>' . l('Variations', 'variations/' . arg(1)) . '</li>
			</ul>';
	}
	
	
	$new_tabs = str_replace('</ul>', $tab_content, $tabs);
	$variables['tabs'] = $new_tabs;
	$node = node_load(arg(1));
    }
    
}


function acquia_prosper_preprocess_node(&$variables){
	drupal_add_css("sites/all/themes/acquia_prosper/css/print.css", "theme", "print");
  global $user;
  $node = node_load($variables['nid']);
    $stock = db_fetch_object(db_query('SELECT stock FROM {uc_product_stock} WHERE nid = %d', $variables['nid']));
    $variables['stock'] = $stock->stock;
	$tabs = $variables['tabs'];
	
	if ($tabs == ""){
	  
	  if (isset($_GET['variation']) && $_GET['variation'] != ''){
	    $variation_nid = db_fetch_object(db_query('SELECT nid FROM content_type_variation WHERE field_variation_oid_value = %d LIMIT 1', $_GET['variation']));
	    if (is_object($variation_nid)){
	      $edit_path = '<a href="/node/' . $variation_nid->nid . '/edit?variation=' . $_GET['variation'] . '">Edit</a>';
	    } else {
	      // Create a node first
	      $edit_path = '<a href="/createvariation/' . $variables['nid'] . '/' . $_GET['variation'] . '">Edit</a>';
	    }
	  } else {
	    $edit_path = l('Edit', 'node/' . $variables['nid'] . '/edit');
	  }
		$tabs = '<ul class="tabs primary">
					<li class="active">' . l('View', 'node/' . $variables['nid']) . '</li>
					<li>' . $edit_path . '</li>
					<li>' . l('Stock', 'node/' . $variables['nid'] . '/edit/stock') . '</li>
				</ul>';
		$variables['tabs'] = $tabs;
		
	}
	
	$variables['short_description'] = $node->field_product_short_description[0]['value'];
    
  if ($variables['teaser'] == FALSE){
    
    if (isset($_GET['variation']) && $_GET['variation'] != ''){
      $variables['is_variation'] = TRUE;
      $oid = $_GET['variation'];
      
      $variation_nid = db_fetch_object(db_query('SELECT nid FROM content_type_variation WHERE field_variation_oid_value = %d LIMIT 1', $oid));
      if (is_object($variation_nid)){ 
	$variation = node_load($variation_nid->nid);
	$variables['variation'] = $variation;
	
	$variables['short_description'] = $variation->field_product_short_description[0]['value'];
	
	// Find price for this option
	if (in_array('distributor', $user->roles)){
	  $variation_price = tilers_products_get_price_per_option($node, $oid, 5);
	} else if (in_array('wholesale', $user->roles)){
	  $variation_price = tilers_products_get_price_per_option($node, $oid, 4);
	} else {	  
	  $variation_price = tilers_products_get_price_per_option($node, $oid);
	}
	
	if (isset($variation_price)){ 
	$variables['fusion_uc_list_price'] = '<div class="product-info product display">
						<span class="uc-price-product uc-price-display uc-price">$' . $variation_price . '</span>
					      </div>';
	$variables['fusion_uc_display_price'] = '<div class="product-info product display">
						  <span class="uc-price-product uc-price-display uc-price">$' . $variation_price . '</span>
						</div>';
	$variables['fusion_uc_sell_price'] = '<div class="product-info product sell">
						<span class="uc-price-product uc-price-sell uc-price"><span class="price-prefixes">Price: </span>$' . $variation_price . '</span>
					      </div>';
	}
	
	// Preset the model dropdown list with the current value
	drupal_add_js("$(document).ready(function(){
			$('#edit-attributes-1-1').val('" . $oid . "').attr('selected', true);
		      });", 'inline');
					      
	// Find images
	if (isset($variation->field_product_images[0]) && !empty($variation->field_product_images[0])){
	    $variables['option_images'] = acquia_prosper_image_gallery($variation);
	}
	
	// Find description
	if ($variation->body != ''){
	  $variables['option_body'] = $variation->body;
	}
	
	// Find technical
	if (isset($variation->field_pdf[0]) && !empty($variation->field_pdf[0])){
	  $variables['option_tech'] = $variation->field_pdf[0]['filepath'];
	}
	
	// Find manuals
	$manuals = '';
	foreach ($variation->field_product_manuals as $m){
	  if (!is_null($m)){
	    $manuals .= '<p>' . l($m["filename"], $m["filepath"]) . '</p>';
	  }
	}
	if ($manuals != ''){
	  $variables['option_manuals'] = $manuals;
	}
	
	// Find videos
	$vids = '';
	foreach ($variation->field_product_videos as $video){
	  if (!is_null($video)){
	    $vids .= '<iframe width="460" height="254" src="http://www.youtube.com/embed/' . $video['value'] . '?rel=0" frameborder="0" allowfullscreen></iframe><br />';
	  }
	}
	if ($vids != ''){
	  $variables['option_videos'] = $vids;
	}
	
	// Find price
	// Check user role
	$price = '';
	if (in_array('wholesale', $user->roles)){
	  $price = db_fetch_object(db_query('SELECT price FROM {uc_price_per_role_option_prices} WHERE nid = %d AND oid = %d AND rid = 4', $variables['nid'], $variation->field_variation_oid[0]['value']));
	} elseif (in_array('distributor', $user->roles)){
	  $price = db_fetch_object(db_query('SELECT price FROM {uc_price_per_role_option_prices} WHERE nid = %d AND oid = %d AND rid = 5', $variables['nid'], $variation->field_variation_oid[0]['value']));
	} else {
	  $price = db_fetch_object(db_query('SELECT price FROM {uc_attribute_options} WHERE oid = %d', $variation->field_variation_oid[0]['value']));
	}
	if ($price != ''){
	  $option_price = $variables['list_price'] - $price->price;
	}
      
      }
      
    }
  }
  
  if ($variables['teaser']){
    $results = db_query('SELECT * FROM uc_product_options WHERE nid = %d', $node->nid);
    $has_options = 0;
    while ($data = db_fetch_object($results)){
      $has_options = 1;
      break;
    }
    $variables['has_options'] = $has_options;
    
  }
}

/**
 * Changed breadcrumb separator
 */
function acquia_prosper_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return '<div class="breadcrumb">'. implode(' &rarr; ', $breadcrumb) .'</div>';
  }
}

function acquia_prosper_display_menu(){
    $menu_tree = menu_tree_all_data('primary-links');
    
    $mine = $menu_tree;
    foreach ($menu_tree as $key => $item){
        if (!empty($item['below'])){
            $menu_tree[$key]['link']['link_path'] = '#';
            $menu_tree[$key]['link']['href'] = '#';
            
            foreach ($item['below'] as $key2 => $item2){
                if (!empty($item2['below'])){
                    $menu_tree[$key]['below'][$key2]['link']['link_path'] = '#';
                    $menu_tree[$key]['below'][$key2]['link']['href'] = '#';
                    
                    foreach ($item2['below'] as $key3 => $item3){
                        if (!empty($item3['below'])){
                          $menu_tree[$key]['below'][$key2]['below'][$key3]['link']['link_path'] = '#';
                          $menu_tree[$key]['below'][$key2]['below'][$key3]['link']['href'] = '#';
                        }
                    }
                }
            }
        }
    }
    
    $output = acquia_prosper_tree_output($menu_tree);
    return $output;

    /*$output = '<div id="menu-grey"><ul class="accordion" id="accordion-1">';

    foreach ($menu_tree as $item){

        $output .= '<li>
                        <a href="#">' . $item['link']['title'] . '</a>';

        if (!empty($item['below'])){
            $output .= '<ul>';

            foreach ($item['below'] as $item_niv2){

                $class = '';                

                if (!empty($item_niv2['below'])){
                    $output .= '<li class="' . $class . '"><a href="#">' . $item_niv2['link']['title'] . '</a>';
                    $output .= '<ul>';
                    foreach ($item_niv2['below'] as $item_niv3){
                        $class = '';

                        if (!empty($item_niv3['below'])){
                            $output .= '<li class="' . $class . '"><a href="#">' . $item_niv3['link']['title'] . '</a>';
                            $output .= '<ul>';
                            foreach ($item_niv3['below'] as $item_niv4){
                                $output .= '<li>' . l($item_niv4['link']['title'], $item_niv4['link']['href']) . '</li>';
                            }
                            $output .= '</ul>';
                        } else {
                            $output .= '<li class="' . $class . '">' . l($item_niv3['link']['title'], $item_niv3['link']['href']);
                        }
                        $output .= '</li>';
                    }
                    $output .= "</ul>";
                } else {
                    $output .= '<li class="' . $class . '">' . l($item_niv2['link']['title'], $item_niv2['link']['href']);
                }
                $output .= '</li>';

            }
            $output .= '</ul>';
        }
        $output .= '</li>';
    }
    $output .= '</ul></div>';

    return $output; */

}





function acquia_prosper_tree_output($tree) {
  $output = '';
  $items = array();

  // Pull out just the menu items we are going to render so that we
  // get an accurate count for the first/last classes.
  foreach ($tree as $data) {
    if (!$data['link']['hidden']) {
      $items[] = $data;
    }
  }

  $num_items = count($items);
  foreach ($items as $i => $data) {
    $extra_class = array();
    if ($i == 0) {
      $extra_class[] = 'first';
    }
    if ($i == $num_items - 1) {
      $extra_class[] = 'last';
    }
    $extra_class = implode(' ', $extra_class);
    $expandable_class = ($data['link']['href'] == "#") ? ' class="expandable"' : '' ;
    $link = '<a href="' . $data['link']['href'] . '"' . $expandable_class . '>' . $data['link']['title'] . '</a>';
    
    if ($data['below']) {
      $output .= theme('menu_item', $link, $data['link']['has_children'], menu_tree_output($data['below']), $data['link']['in_active_trail'], $extra_class);
    }
    else {
      $output .= theme('menu_item', $link, $data['link']['has_children'], '', $data['link']['in_active_trail'], $extra_class);
    }
  }
  return $output ? theme('menu_tree', $output) : '';
}

function acquia_prosper_image_gallery($node){
  $output = '';
  $count = 0;
  
  foreach ($node->field_product_images as $image){
    if ($count != 0){
      $output .= '<a href="/' . $image['filepath'] . '" title="' . $node->title . '" class="product-img">';
      $output .= theme('imagecache', 'gallery', $image['filepath'], $image['data']['alt'], $image['data']['title'], array('id' => "main_picture"));
      $output .= '</a>';
    } else {
      $class = 'clickme';
      $id = 'product-main-img';
      
      $output .= '<a href="/' . $image['filepath'] . '" title="' . $node->title . '" class="product-img" id="product-main-img">';
      $output .= theme('imagecache', 'product_view', $image['filepath'], $image['data']['alt'], $image['data']['title'], array('id' => "main_picture"));
      $output .= '</a>';
    }
    $count++;
  }
  return $output;

}

/**
* Theme override for user login block.
*/
function acquia_prosper_user_login_block($form) {
  $form['name']["#title"] = "";
  $form['name']["#value"] = "username";
  $form['pass']["#title"] = "";
  $form['pass']["#value"] = "password";
  return (drupal_render($form));
}


function acquia_prosper_cart_display($item) {
  $node = node_load($item->nid);
  $element = array();
  $element['nid'] = array('#type' => 'value', '#value' => $node->nid);
  $element['module'] = array('#type' => 'value', '#value' => 'uc_product');
  $element['remove'] = array('#type' => 'checkbox');
  $op_names = '';
  if (module_exists('uc_attribute')){
    $op_names = "<ul class=\"cart-options\">\n";
    foreach ($item->options as $option){
      $op_names .= '<li>'. $option['attribute'] .': '. $option['name'] ."</li>\n";
    }
    $op_names .= "</ul>\n";
  }
  $element['options'] = array('#value' => $op_names);
  $element['title'] = array(
    '#value' => l($node->title, 'node/'. $node->nid),
  );
  $element['#total'] = $item->price * $item->qty;
  $element['data'] = array('#type' => 'hidden', '#value' => serialize($item->data));
  $element['qty'] = array(
    '#type' => 'textfield',
    '#default_value' => $item->qty,
    '#size' => 3,
    '#maxlength' => 3
  );
  return $element;
}

/**
 * Theming uc_cart summary block from sites/all/modules/contrib/ubercart/uc_cart/uc_cart.module
 **/
function acquia_prosper_uc_cart_block_summary($item_count, $item_text, $total, $summary_links) {
  global $user;
  $outstock_products = (isset($_SESSION['outstock_nids']) && !empty($_SESSION['outstock_nids'])) ? $_SESSION['outstock_nids'] : array();
  /*$outstock_nr = 0;
  foreach ($outstock_nr as $data){
      if ($data['status']){
          $outstock_nr++;
      }
  }*/

  $item_nr = $item_count + count($outstock_nr);
  $item_text = ($item_nr > 1) ? "items" : "item";

  if (!isset($user->uid) || $user->uid == 0){
      $submit = l('Login / Register', 'signin', array('rel' => 'no-follow'));
  } else {
      $submit = l('Checkout', 'cart/checkout', array('rel' => 'no-follow'));
  }

  $output = '<table class="cart-block-summary">
                <tbody>
                    <tr>
                        <td class="cart-block-summary-items"><span class="num-items">' . $item_nr . '</span> ' . $item_text . '</td>
                        <td class="cart-block-summary-total">
                            <label>Total:</label> <span class="uc-price">$' . $total . '</span>
                        </td>
                    </tr>
                    <tr class="cart-block-summary-links">
                        <td colspan="2">
                            <ul class="links">
                                <li class="cart-block-view-cart first">' . l('View cart', 'cart', array('rel' => 'no-follow')) . '</li>
                                <li class="cart-block-checkout last">' . $submit . '</li>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>';
  return $output;
}


function acquia_prosper_uc_cart_view_form($form) {
  
  drupal_add_css(drupal_get_path('module', 'uc_cart') .'/uc_cart.css');

  $output = '<div class="uc-default-submit">';
  $output .= drupal_render($form['update']);
  $output .= '</div>';
  unset($form['update']['#printed']);

  $output .= '<div id="cart-form-products">'
          . drupal_render($form['items']) .'</div>';

  foreach (element_children($form['items']) as $i) {
    foreach (array('title', 'options', 'remove', 'image', 'qty') as $column) {
      $form['items'][$i][$column]['#printed'] = TRUE;
    }
    $form['items'][$i]['#printed'] = TRUE;
  }

  // Add the continue shopping element and cart submit buttons.
  if (($type = variable_get('uc_continue_shopping_type', 'link')) != 'none') {
    // Render the continue shopping element into a variable.
    $cs_element = drupal_render($form['continue_shopping']);

    // Add the element with the appropriate markup based on the display type.
    if ($type == 'link') {
      $output .= '<div id="cart-form-buttons"><div id="continue-shopping-link">'
               . $cs_element .'</div>'. drupal_render($form) .'</div>';
    }
    elseif ($type == 'button') {
      $output .= '<div id="cart-form-buttons"><div id="update-checkout-buttons">'
               . drupal_render($form) .'</div><div id="continue-shopping-button">'
               . $cs_element .'</div></div>';
    }
  }
  else {
    $output .= '<div id="cart-form-buttons">'. drupal_render($form) .'</div>';
  }

  return $output;
}




function acquia_prosper_cart_review_table($show_subtotal = TRUE) {
  $subtotal = 0;
  
  // Set up table header.
  $header = array(
    array('data' => t('Qty'), 'class' => 'qty'),
    array('data' => t('Products'), 'class' => 'products'),
    array('data' => t('Price'), 'class' => 'price'),
  );

  $context = array();

  // Set up table rows.
  $contents = uc_cart_get_contents();
  foreach ($contents as $item) {
    $price_info = array(
      'price' => $item->price,
      'qty' => $item->qty,
    );

    $context['revision'] = 'altered';
    $context['type'] = 'cart_item';
    $context['subject'] = array(
      'cart' => $contents,
      'cart_item' => $item,
      'node' => node_load($item->nid),
    );

    $total = uc_price($price_info, $context);
    $subtotal += $total;
    
    // Get attribute
    $oid = $item->data['attributes'][1];
    $result = db_fetch_object(db_query('SELECT * FROM {uc_attribute_options} WHERE oid = %d', $oid));
    $description = $result->name;

    // Remove node from context to prevent the price from being altered.
    $context['revision'] = 'themed-original';
    $context['type'] = 'amount';
    unset($context['subject']);
    $rows[] = array(
      array('data' => t('@qty&times;', array('@qty' => $item->qty)), 'class' => 'qty'),
      array('data' => $description, 'class' => 'products'),
      array('data' => uc_price($total, $context), 'class' => 'price'),
    );
  }

  // Add the subtotal as the final row.
  if ($show_subtotal) {
    $context = array(
      'revision' => 'themed-original',
      'type' => 'amount',
    );
    $rows[] = array(
      'data' => array(array('data' => '<span id="subtotal-title">' . t('Subtotal:') . '</span> ' . uc_price($subtotal, $context), 'colspan' => 3, 'class' => 'subtotal')),
      'class' => 'subtotal',
    );
  }

  return theme('table', $header, $rows, array('class' => 'cart-review'));
}

function acquia_prosper_uc_checkout_pane_cart_review($items) {
  $context = array(
    'revision' => 'themed',
    'type' => 'cart_item',
    'subject' => array(),
  );

  $rows = array();

  foreach ($items as $item) {
    $price_info = array(
      'price' => $item->price,
      'qty' => $item->qty,
    );

    $context['subject'] = array(
      'cart' => $items,
      'cart_item' => $item,
      'node' => node_load($item->nid),
    );

    $myarray = array(
      array('data' => $item->qty . '&times;', 'class' => 'qty')
    );
    
    
    // Get attribute
    $oid = $item->data['attributes'][1];
    $result = db_fetch_object(db_query('SELECT * FROM {uc_attribute_options} WHERE oid = %d', $oid));
    $description = $result->name;
    $myarray[] = array('data' => $description, 'class' => 'products');
      
      
    $myarray[] = array('data' => uc_price($price_info, $context), 'class' => 'price');
    
    $rows[] = $myarray;
  }
  return theme('table', NULL, $rows, array('class' => 'cart-review'));
}