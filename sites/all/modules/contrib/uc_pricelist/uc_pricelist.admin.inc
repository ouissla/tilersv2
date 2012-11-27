<?php

/**
 * The price list overview page.
 */
function uc_pricelist_overview() {
  // Get all of the pricelists.
  $pricelists = uc_pricelist_get_pricelists();
  $pricelist_roles = variable_get('uc_pricelist_role', array());

  // Set the options array.
  $options = array(0 => 'None');
  foreach( $pricelists as $name => $pricelist ) {
    $options[$name] = $name;
  }

  // Get all of the roles.
  $roles = user_roles();
  foreach( $roles as $rid => $role ) {
    $form['pricelist_roles']['list_' . $rid] = array(
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => isset($pricelist_roles[$rid]) ? $pricelist_roles[$rid] : 0
    );
  }

  $form['pricelist_users'] = array(
    '#type' => 'markup',
    '#value' => '<p><strong>Note: You can override these settings on an individual basis by editing whatever user you would like to override, and provide a price list for that user.</strong></p>'
  );

  $form['pricelists'] = array(
    '#type' => 'fieldset',
    '#title' => t('Price Lists'),
    '#collapsible' => TRUE
  );

  if( count( $pricelists ) > 0 ) {
    $header = array(t('Price List'), array('data' => t('Operations'), 'colspan' => '3'));
    $rows = array();
    foreach( $pricelists as $name => $pricelist ) {
      $row = array();
      $row[] = check_plain($name);
      $row[] = array('data' => l(t('view'), 'admin/store/pricelists/manage/'. $name));
      $row[] = array('data' => l(t('edit'), 'admin/store/pricelists/manage/'. $name . '/edit'));
      $row[] = array('data' => l(t('delete'), 'admin/store/pricelists/manage/'. $name .'/delete'));
      $rows[] = $row;
    }
    $form['pricelists']['pricelist'] = array(
      '#type' => 'markup',
      '#value' => theme('table', $header, $rows)
    );
  }
  else {
    $form['pricelists']['pricelist'] = array(
      '#type' => 'markup',
      '#value' => t('No price lists.  To add a new price list, click on the <strong>Add Price List</strong> button.')
    );
  }

  $form['pricelist_combine'] = array(
    '#type' => 'textarea',
    '#title' => t('Combine Quantities'),
    '#description' => t('With this setting you can combine quantities of different types of SKU\'s so that quantity discounts will trigger from the combination of those items.  Enter the SKU\'s that you would like to combine separated by comma\'s, use multiple lines to distinguish different combinations.'),
    '#default_value' => variable_get('pricelist_combine', '')
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save Configuration'),
    '#weight' => 30,
  );

  // Theme the overview form.
  $form['#theme'] = 'pricelist_overview';

  $form['#submit'][] = 'uc_pricelist_overview_submit';

  return $form;
}

/**
 * We want to theme the overview form to make the user role selections
 * in table format.
 */
function theme_pricelist_overview( $form ) {
  $output = '';
  $header = array(t('User Role'), t('Price List'));
  $rows = array();
  $roles = user_roles();
  foreach( element_children($form['pricelist_roles']) as $key ) {
    $row = array();
    $rid = substr($key, 5);
    $row[] = $roles[$rid];
    $row[] = drupal_render($form['pricelist_roles'][$key]);
    $rows[] = $row;
  }
  $output .= theme('table', $header, $rows);
  $output .= drupal_render($form);
  return $output;
}

/**
 * Called when the overview page has been submitted.
 */

function uc_pricelist_overview_submit($form, &$form_state) {

  // Set the user roles
  $roles = user_roles();
  $pricelists = array();
  foreach($roles as $rid => $role ) {
    $pricelists[$rid] = $form_state['values']['list_' . $rid];
  }

  // Set the pricelists.
  variable_set( 'uc_pricelist_role', $pricelists );
  variable_set( 'pricelist_combine', $form_state['values']['pricelist_combine'] );
}

/**
 * A table view of a pricelist.
 */
function uc_pricelist_view( $pricelist ) {

  $header = array('SKU');
  $rows = array();
  $header_done = false;

  // Just create a table view of the price list.
  foreach( $pricelist['pricelist'] as $sku => $prices ) {
    $row = array();
    $row[] = $sku;
    $prevnum = 1;
    $index = 0;
    foreach( $prices as $num => $price ) {
      if( !$header_done && $index > 0) {
        $header[$index] = $prevnum . ' to ' . ($num - 1);
      }
      $row[] = $price;
      $prevnum = $num;
      $index++;
    }

    $rows[] = $row;
    if( !$header_done ) {
      $header[$index] = $prevnum . '+';
    }
    $header_done = true;
  }

  return theme('table', $header, $rows);
}

/**
 * The main pricelist form.
 */
function uc_pricelist_form( &$form_state, $pricelist = NULL ) {
  $form['pricelist_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Price List Name'),
    '#description' => t('Enter the name for this price list'),
    '#default_value' => $pricelist['name']
  );

  $form['pricelist_value'] = array(
    '#type' => 'textarea',
    '#title' => t('The Price List'),
    '#description' => t('Just copy and paste a price list, in CSV format, into this field.'),
    '#default_value' => $pricelist['pricelist']
  );

  $header = array('SKU', '1', '10', '20', '30', '40', '50', '60', '70', '80', '90', '100');
  $rows = array(
    array('SKU_A', '100', '90', '80', '70', '60', '50', '40', '30', '20', '10', '1'),
    array('SKU_B', '200', '190', '180', '170', '160', '150', '140', '130', '120', '110', '100'),
    array('SKU_C', '300', '290', '280', '270', '260', '250', '240', '230', '220', '210', '200'),
    array('SKU_D', '400', '390', '380', '370', '360', '350', '340', '330', '320', '310', '300'),
  );

  $help .= '<p>Let us suppose that you have 4 products ( SKU_A, SKU_B, SKU_C, and SKU_D ), and for each one of those products, you would like to have different price points depending on what quantity they purchased.  ';
  $help .= 'This can be accomplished by creating a table where the first row simply illustrates the quantity ranges where the discount starts for that column.  ';
  $help .= 'Each subsequent row is then used to provide the prices for each quantity checkpoint provided within the first row.</p>';
  $help .= '<p>An example table for the following would look like the following.</p>';
  $help .= theme('table', $header, $rows);
  $help .= '<p>Provided this example, if I were to purchase 55 SKU_A\'s, my charge per item would be reduced to $50, whereas I would have to pay $100 if I only bought 1.</p>';
  $help .= '<p>If you were to export this table as CSV format, you would then paste that table into the textarea above, which would look like the following.</p>';
  $help .= '<p><code>SKU,1,10,20,30,40,50,60,70,80,90,100<br/>';
  $help .= 'SKU_A,100,90,80,70,60,50,40,30,20,10,1<br/>';
  $help .= 'SKU_B,200,190,180,170,160,150,140,130,120,110,100<br/>';
  $help .= 'SKU_C,300,290,280,270,260,250,240,230,220,210,200<br/>';
  $help .= 'SKU_D,400,390,380,370,360,350,340,330,320,310,300<br/></code></p>';

  $form['help'] = array(
    '#type' => 'fieldset',
    '#title' => t('Example'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE
  );
  $form['help']['pricelist_help'] = array(
    '#type' => 'markup',
    '#value' => $help
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save Price List'),
    '#weight' => 30,
  );

  $form['delete'] = array(
    '#type' => 'submit',
    '#value' => t('Delete'),
    '#weight' => 40,
    '#submit' => array('uc_pricelist_delete_submit'),
  );

  $form['#validate'][] = 'uc_pricelist_validate';
  $form['#submit'][] = 'uc_pricelist_submit';

  return $form;
}


/**
 * Verify the syntax of the given name.
 *
 * Borrowed from the user.module.   :)
 */
function uc_pricelist_validate($form, &$form_state) {
  $name = $form_state['values']['pricelist_name'];
  if (!$name) {
    form_set_error('pricelist_name', t('You must enter a name.'));
  }
  if( arg(5) != 'edit' && uc_pricelist_name_exists( $name ) ) {
    form_set_error('pricelist_name', t('The name %name is already taken.', array('%name' => $name)));
  }
  if (strpos($name, ' ') !== FALSE ) {
    form_set_error('pricelist_name', t('The name cannot contain a space.'));
  }
  if( is_numeric( $name[0] ) ) {
    form_set_error('pricelist_name', t('The name cannot begin with a number.'));
  }
  if (preg_match('/[^a-z0-9_]/i', $name)) {
    form_set_error('pricelist_name', t('The name contains an illegal character.'));
  }
  if (drupal_strlen($name) > PRICELIST_NAME_MAX_LENGTH) {
    form_set_error('pricelist_name', t('The name %name is too long: it must be %max characters or less.', array('%name' => $name, '%max' => PRICELIST_NAME_MAX_LENGTH)));
  }
}

/**
 * Submit handler for the preset form.
 */
function uc_pricelist_submit($form, &$form_state) {
  variable_set('uc_pricelists_' . $form_state['values']['pricelist_name'], $form_state['values']['pricelist_value']);
  $form_state['redirect'] = 'admin/store/pricelists/manage/' . $form_state['values']['pricelist_name'];
}

/**
 * Button sumit function: handle the 'Delete' button on the preset form.
 */
function uc_pricelist_delete_submit($form, &$form_state) {
  $form_state['redirect'] = 'admin/store/pricelists/manage/' . $form_state['values']['pricelist_name'] . '/delete';
}

/**
 * Confirm a deletion.
 * 
 * @param <type> $form_state
 * @param <type> $pricelist
 * @return <type>
 */
function uc_pricelist_delete_confirm( &$form_state, $pricelist ) {
  $form['pricelist_name'] = array('#type' => 'value', '#value' => $pricelist['name']);
  $message = t('Are you sure you want to delete the price list?');
  $caption = '<p>' . t('This action cannot be undone.') . '</p>';
  return confirm_form($form, $message, 'admin/store/pricelists', $caption, t('Delete'));
}

/**
 * Execute preset deletion
 */
function uc_pricelist_delete_confirm_submit($form, &$form_state) {
  if( $form_state['values']['confirm'] ) {
    uc_pricelist_delete($form_state['values']['pricelist_name']);
  }

  $form_state['redirect'] = 'admin/store/pricelists';
}