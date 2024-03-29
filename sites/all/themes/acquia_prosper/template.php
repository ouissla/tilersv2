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
  drupal_add_js("sites/all/themes/acquia_prosper/js/jquery.resizeOnApproach.1.0.js", "module", "header");
  drupal_add_js("sites/all/themes/acquia_prosper/js/jquery.hoverIntent.minified.js", "module", "header");
 

    if ($variables['is_front']){

        //drupal_add_js("sites/all/themes/acquia_prosper/js/acquia-prosper-script.js", "module", "footer");
	
        //$variables['scripts'] = drupal_get_js();
        //$variables['styles'] = drupal_get_css();

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

    }
    
    $variables['scripts'] = drupal_get_js();
    if (arg(0) == 'node' && is_numeric(arg(1))){
      $tabs = $variables['tabs'];
	
    	$tab_content = '';
    	  $tab_content .= '<li>' . l('Stock', 'node/' . arg(1) . '/edit/stock') . '</li>
    			  <li>' . l('Variations', 'variations/' . arg(1)) . '</li>
    			</ul>';
	
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

  if (!$teaser){
    // Check if product has variaitions
    if ($node->field_product_is_master[0]['value'] || $node->field_product_master_id[0]['value'] != ''){
      // Get variations
      if ($node->field_product_is_master[0]['value']){
        $sql = db_query('SELECT n.nid FROM {node} n
          LEFT JOIN {content_field_product_master_id} c ON c.nid = n.nid
          WHERE c.field_product_master_id_value = %d
          AND n.status = 1', $node->nid);

        if (isset($user->roles[4]) || isset($user->roles[5])){
          if (isset($user->roles[4])){
            $price = $node->role_price[4];
          } else {
            $price = $node->role_price[5];
          }
        } else {
          $price = $node->sell_price;
        }

        $variations = array(
          0 => array(
            'title' => $node->title,
            'sku'   => $node->model,
            'price' => uc_currency_format($price),
            'raw_price' => $price,
            'nid'   => $node->nid
          )
        );

        while ($data = db_fetch_object($sql)){
          $var = node_load($data->nid);

          if (isset($user->roles[4]) || isset($user->roles[5])){
            if (isset($user->roles[4])){
              $price = $var->role_price[4];
            } else {
              $price = $var->role_price[5];
            }
          } else {
            $price = $var->sell_price;
          }

          $variations[] = array(
              'title' => $var->title,
              'sku'   => $var->model,
              'price' => uc_currency_format($price),
              'raw_price' => $price,
              'nid'   => $var->nid
          );
        } 
        $variables['variations'] = $variations;

        usort($variables['variations'], function($a, $b) {
            if ($a['raw_price'] == $b['raw_price']) {
                return 0;
            }
            return ($a['raw_price'] < $b['raw_price']) ? -1 : 1;
        });

      } else {
        $sql = db_query('SELECT n.nid FROM {node} n
          LEFT JOIN {content_field_product_master_id} c ON c.nid = n.nid
          WHERE c.field_product_master_id_value = %d
          AND n.status = 1', $node->field_product_master_id[0]['value']);
        // Load master
        $master = node_load($node->field_product_master_id[0]['value']);

        if (isset($user->roles[4]) || isset($user->roles[5])){
          if (isset($user->roles[4])){
            $price = $master->role_price[4];
          } else {
            $price = $master->role_price[5];
          }
        } else {
          $price = $master->sell_price;
        }

        $variations = array(
          0 => array(
            'title'     => $master->title,
            'sku'       => $master->model,
            'price'     => uc_currency_format($price),
            'raw_price' => $price,
            'nid'       => $master->nid
          )
        );
        while ($data = db_fetch_object($sql)){
          $var = node_load($data->nid);  
          
          if (isset($user->roles[4]) || isset($user->roles[5])){
            if (isset($user->roles[4])){
              $price = $var->role_price[4];
            } else {
              $price = $var->role_price[5];
            }
          } else {
            $price = $var->sell_price;
          }

          $variations[] = array(
            'title'     => $var->title,
            'sku'       => $var->model,
            'price'     => uc_currency_format($price),
            'raw_price' => $price,
            'nid'       => $var->nid
          );

        }
        $variables['variations'] = $variations;
        usort($variables['variations'], function($a, $b) {
            if ($a['raw_price'] == $b['raw_price']) {
                return 0;
            }
            return ($a['raw_price'] < $b['raw_price']) ? -1 : 1;
        });
      }
    }
  }
	
	$variables['short_description'] = $node->field_product_short_description[0]['value'];
    
  
  if ($node->field_product_is_master[0]['value']){
    $variables['has_options'] = 1;
  }

  if (!isset($variables['tabs']) || $variables['tabs'] == ''){
    $variables['tabs'] == '<ul class="tabs primary">
    <li>' . l('Edit', 'node/' . $node->nid . '/edit') . '</li>
    <li>' . l('Stock', 'node/' . $node->nid . '/edit/stock') . '</li>
    </ul>';
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
    
    $output = '<ul id="custom-mega">';

    foreach ($menu_tree as $item){
      
      // CREATE SUBMENU
      if (!empty($item['below']) && !$item['link']['hidden']){
        $output .= '<li class="lvl1">' . l($item['link']['title'], $item['link']['title'], array('attributes' => array('class' => 'lvl1 nofollow')));
        $output .= '<div class="megawrapper"><div class="megawrapper-wrapper">';
        $counter = 5;
        foreach ($item['below'] as $item2){
          $class = ($counter % 5 == 0) ? ' first' : '';
          $output .= '<div class="subwrapper' . $class . '">';
          
          
          /*if (!empty($item2['below'])){
            $output .= '<h2>' . l($item2['link']['title'], $item2['link']['href'], array('attributes' => array('class' => 'lvl2 nofollow'))) . '</h2>';
            $output .= '<ul>';
            foreach ($item2['below'] as $item3){
              $output .= '<li>' . l($item3['link']['title'], $item3['link']['href'], array('class' => array('lvl3')));
            
              if (!empty($item3['below'])){
                $output .= '<ul>';
                foreach ($item3['below'] as $item4){
                  $output .= '<li>' . l($item4['link']['title'], $item4['link']['href'], array('class' => array('lvl4'))) . '</li>';
                }
                $output .= '</ul>';
              }

              $output .= '</li>';
            }
            $output .= '</ul>';
          } else {*/
            $output .= '<h2>' . l($item2['link']['title'], $item2['link']['href'], array('class' => array('lvl2'))) . '</h2>';
          //}

          $output .= '</div>';
          if ($counter % 5 == 4){
            // Add a line break
            $output .= '<div class="menu-breaker"></div>';
          }
          $counter++;
        }
        $output .= '</div></div>';
      }
      $output .= '</li>';
    }

    $output .= '</ul>';
    
    return $output;

}

function acquia_prosper_image_gallery($node){
  $output = '';
  $count = 0;
  
  foreach ($node->field_product_images as $image){
    if ($count != 0){
      $output .= '<a href="/' . $image['filepath'] . '" title="' . $node->title . '" class="product-img colorbox-load" rel="product-gallery">';
      $output .= theme('imagecache', 'gallery', $image['filepath'], $image['data']['alt'], $image['data']['title'], array('id' => "main_picture"));
      $output .= '</a>';
    } else {
      $class = 'clickme';
      $id = 'product-main-img';
      
      $output .= '<a href="/' . $image['filepath'] . '" title="' . $node->title . '" class="product-img colorbox-load" id="product-main-img" rel="product-gallery">';
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

  $cart_content = uc_cart_get_contents();
  if (empty($cart_content)) { return ''; }

  $item_nr = $item_count + count($outstock_nr);
  $item_text = ($item_nr > 1) ? "items" : "item";

  if (!isset($user->uid) || $user->uid == 0){
      $submit = l('Login / Register', 'signin', array('rel' => 'no-follow'));
  } else {
      $submit = l('Checkout', 'cart/checkout', array('rel' => 'no-follow'));
  }

  $output = '<div id="cart-block-wrapper">
              <ul>
                <li id="cart-block-title">
                  <a href="/cart" class="title css3red">Checkout now</a>
                  <ul id="subcontent">
                    <li class="cart-preview-message">
                      <p>You cart preview</p>
                    </li>
                    <li id="cart-summary-content">
                      <ul>';

                    foreach ($cart_content as $cart_item){
                      $product = node_load($cart_item->nid);
                      $image = '';
                      if (!empty($product->field_product_images)){
                        $image = '<div class="cart-item-image">' . theme('imagecache', 'uc_thumbnail', $product->field_product_images[0]['filepath']) . '</div>';
                      }
                      $output .= '<li class="cart-item">';
                      $output .= $image;
                      $output .= '<div class="cart-item-title">
                                    <p class="title">' . $cart_item->title . '</p><p class="qty"> x' . $cart_item->qty . '</p></div>';
                      $output .= '</li>';
                    }
                    $output .= '</ul></li>';
                    $output .= '<li id="cart-block-total">
                                  <div class="cart-block-total-desc">
                                    <p>Total <span>Inc. GST when applicable</span></p>
                                  </div>
                                  <div class="cart-block-total-total">
                                    <p>' . format_price($total) . '</p>
                                  </div>
                                </li>';

                    $output .= '<li id="gotocart">
                                  <a href="/cart" id="view-cart" class="css3button css3red">View Cart</a>
                                  <span>Go to the secure checkout</span>
                                </li>
                  </ul>
                </li>
              </ul>
            </div>';

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
      $product = node_load($form['items'][$i]['nid']['#value']);

      $form['items'][$i]['title']['#value'] = $form['items'][$i]['title']['#value'] . $stock_level;
      $form['items']['#rows'][$i]['desc'] = $form['items'][$i]['title']['#value'] . $stock_level;

    foreach (array('title', 'options', 'remove', 'image', 'qty') as $column) {
      $form['items'][$i][$column]['#value'] = 'toto';
      $form['items'][$i][$column]['#printed'] = FALSE;
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




/*function acquia_prosper_cart_review_table($show_subtotal = TRUE) { 
  $subtotal = 0;
  
  // Set up table header.
  $header = array(
    array('data' => t('Qty'), 'class' => 'qty'),
    array('data' => t('Products'), 'class' => 'products'),
    array('data' => t('Stock'), 'class' => 'stock'),
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
      array('data' => 50, 'class' => 'stock'),
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
*/

/*function acquia_prosper_uc_checkout_pane_cart_review($items) { 
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
      
      
    $myarray[] = array('data' => 60, 'class' => 'stock');
    $myarray[] = array('data' => uc_price($price_info, $context), 'class' => 'price');
    
    $rows[] = $myarray;
  }
  return theme('table', NULL, $rows, array('class' => 'cart-review'));
} */

/**
 * Theme the subqueue overview as a sortable list.
 *
 * @ingroup themeable
 */
function acquia_prosper_nodequeue_arrange_subqueue_form($form) {
  $output = '';

  $subqueue = $form['#subqueue'];

  // get css to hide some of the help text if javascript is disabled
  drupal_add_css(drupal_get_path('module', 'nodequeue') .'/nodequeue.css');

  // TODO: Would be nice to expose qid, sqid, reference as classes for more custom theming :).
  // TODO: Create unique ID to make multiple tabledrag forms on a page possible
  drupal_add_tabledrag('nodequeue-dragdrop', 'order', 'sibling', 'node-position');
  drupal_add_js(drupal_get_path('module', 'nodequeue') .'/nodequeue_dragdrop.js');

  drupal_add_js(array('nodequeue' => array('reverse' => (bool) $form['#queue']['reverse'])), 'setting');

  // render form as table rows
  $rows = array();
  $counter = 1;
  foreach (element_children($form) as $key) {
    if (isset($form[$key]['title'])) {
      $row = array();
      
      $image = '';
      if ($form[$key]['image'] != ''){
	$image = theme('imagecache', 'uc_thumbnail', $form[$key]['image']['#filepath']);
      }

      $row[] = $image;
      $row[] = drupal_render($form[$key]['title']);
      $row[] = drupal_render($form[$key]['author']);
      $row[] = drupal_render($form[$key]['date']);
      $row[] = drupal_render($form[$key]['position']);
      $row[] = drupal_render($form[$key]['edit']);
      $row[] = drupal_render($form[$key]['remove']);
      $row[] = array(
        'data' => $counter,
        'class' => 'position'
      );

      $rows[] = array(
        'data'  => $row,
        'class' => 'draggable',
      );
    }

    $counter++;
  }
  if (empty($rows)) {
    $rows[] = array(array('data' => t('No nodes in this queue.'), 'colspan' => 7));
  }

  // render the main nodequeue table
  $header = array(t('Image'), t('Title'), t('Author'), t('Post Date'), t('Position'), array('data' => t('Operations'), 'colspan' => 2), t('Position'));
  $output .= theme('table', $header, $rows, array('id' => 'nodequeue-dragdrop', 'class' => 'nodequeue-dragdrop'));

  // render the autocomplete field for adding a node to the table
  $output .= '<div class="container-inline">';
  $output .= drupal_render($form['add']['nid']);
  $output .= drupal_render($form['add']['submit']);
  $output .= '</div>';

  // render the remaining form elements
  $output .= drupal_render($form);

  return $output;
}

function acquia_prosper_show_slider(){
  $sql = db_query('SELECT nid FROM node WHERE type = "slider" AND status = 1');
  $sliders = array();
  while ($data = db_fetch_object($sql)){
    $sliders[] = node_load($data->nid);
  }
  
  $output = '<ul class="slides">';
  foreach ($sliders as $slider){
	
	$output .= '<li>';
	$output .= theme('imagecache', 'slider-big', $slider->field_slider_image[0]['filepath']);
	$output .= '</li>';
		
  }
  $output .= '</ul>';
  
  return $output;
}


function acquia_prosper_variation_list($nid, $vars){
  $output = '<p style="margin: 0;"><strong>Select your Model/Price from the list:</strong></p>';
  $output .= '<select name="variations-list" id="variations-list">';

  foreach ($vars as $var){
    $selected = ($var['nid'] == $nid) ? 'selected="selected" ' : '';

    $output .= '<option value="' . $var['nid'] . '" id="' . $var['sku'] . '"' . $selected . '>' . $var['title'] . ' - ' . $var['price'] . '</option>';
  }

  $output .= '</ul>
  </select>';

  return $output;
}

function acquia_prosper_links($links, $attributes = array('class' => 'links')) {
    global $language;
    global $user;
  $output = '';

  if (count($links) > 0) {
    $output = '<ul' . drupal_attributes($attributes) . '>';

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {

      if ($key != "menu-411" || ($key == "menu-411" && $user->uid >= 1)){
        $class = $key;
        // Add first, last and active classes to the list of links to help out themers.
        if ($i == 1) {
          $class .= ' first';
        }
        if ($i == $num_links) {
          $class .= ' last';
        }
        if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
             && (empty($link['language']) || $link['language']->language == $language->language)) {
          $class .= ' active';
        }
        $output .= '<li' . drupal_attributes(array('class' => $class)) . '>';

        if (isset($link['href'])) {
          // Pass in $link as $options, they share the same keys.
          $output .= l($link['title'], $link['href'], $link);
        }
        else if (!empty($link['title'])) {
          // Some links are actually not links, but we wrap these in <span> for adding title and class attributes
          if (empty($link['html'])) {
            $link['title'] = check_plain($link['title']);
          }
          $span_attributes = '';
          if (isset($link['attributes'])) {
            $span_attributes = drupal_attributes($link['attributes']);
          }
          $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
        }

        $i++;
        $output .= "</li>\n";
      } 

    }

    $output .= '</ul>';
  }

  return $output;
}