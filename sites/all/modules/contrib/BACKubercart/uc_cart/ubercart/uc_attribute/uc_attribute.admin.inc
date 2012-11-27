<?php

/**
 * @file
 * Attribute administration menu items.
 */

/**
 * Displays a paged list and overview of existing product attributes.
 */
function uc_attribute_admin() {
  $header = array(
    array('data' => t('Name'), 'field' => 'name', 'sort' => 'asc'),
    array('data' => t('Label'), 'field' => 'label'),
    t('Required'),
    array('data' => t('List position'), 'field' => 'ordering'),
    t('Number of options'),
    t('Display type'),
    t('Operations'),
  );

  $display_types = _uc_attribute_display_types();

  $rows = array();

  $result = pager_query("SELECT aid, name, label, required, ordering, display FROM {uc_attributes}". tablesort_sql($header), 30, 0);
  while ($attr = db_fetch_object($result)) {
    $attr->options = db_result(db_query('SELECT COUNT(*) FROM {uc_attribute_options} WHERE aid = %d', $attr->aid));
    if (empty($attr->label)) {
      $attr->label = $attr->name;
    }
    $ops = array(
      l(t('edit'), 'admin/store/attributes/'. $attr->aid .'/edit'),
      l(t('options'), 'admin/store/attributes/'. $attr->aid .'/options'),
      l(t('delete'), 'admin/store/attributes/'. $attr->aid .'/delete'),
    );
    $rows[] = array(
      check_plain($attr->name),
      check_plain($attr->label),
      $attr->required == 1 ? t('Yes') : t('No'),
      array('data' => $attr->ordering, 'align' => 'center'),
      array('data' => $attr->options, 'align' => 'center'),
      $display_types[$attr->display],
      implode(' ', $ops),
    );
  }

  if (count($rows) == 0) {
    $rows[] = array(
      array('data' => t('No product attributes have been added yet.'), 'colspan' => '6')
    );
  }

  $output = theme('table', $header, $rows) . theme('pager', NULL, 30)
          . l(t('Add an attribute'), 'admin/store/attributes/add');

  return $output;
}

/**
 * Form builder for product attributes.
 *
 * @see uc_attribute_form_submit()
 * @ingroup forms
 */
function uc_attribute_form($form_state, $attribute = NULL) {
  // If an attribute specified, add its ID as a hidden value.

  if (!empty($attribute)) {
    $form['aid'] = array('#type' => 'hidden', '#value' => $attribute->aid);
    drupal_set_title(t('Edit attribute: %name', array('%name' => $attribute->name)));
  }

  if (isset($attribute->name)) {
    if (empty($attribute->label)) {
      $attribute->label = $attribute->name;
    }
    $name = $attribute->name;
    $label = $attribute->label;
  }
  else {
    $name = $label = '';
  }
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Name'),
    '#description' => t('The name of the attribute used in administrative forms'),
    '#default_value' => $name,
    '#required' => TRUE,
  );
  $form['label'] = array(
    '#type' => 'textfield',
    '#title' => t('Label'),
    '#description' => t("Enter a label that customers will see instead of the attribute name. Use &lt;none&gt; if you don't want a title to appear at all."),
    '#default_value' => $label,
  );
  $form['description'] = array(
    '#type' => 'textfield',
    '#title' => t('Help text'),
    '#description' => t('<b>Optional.</b> Enter the help text that will display beneath the attribute on product add to cart forms.'),
    '#default_value' => isset($attribute->description) ? $attribute->description : '',
    '#maxlength' => 255,
  );
  $form['required'] = array(
    '#type' => 'checkbox',
    '#title' => t('Make this attribute required, forcing the customer to choose an option.'),
    '#description' => t('Selecting this for an attribute will disregard any default option you specify.<br />May be overridden at the product level.'),
    '#default_value' => isset($attribute->required) ? $attribute->required : 0,
  );
  $form['display'] = array(
    '#type' => 'select',
    '#title' => t('Display type'),
    '#description' => t('This specifies how the options for this attribute will be presented.<br />May be overridden at the product level.'),
    '#options' => _uc_attribute_display_types(),
    '#default_value' => isset($attribute->display) ? $attribute->display : 1,
  );
  $form['ordering'] = array(
    '#type' => 'weight',
    '#delta' => 25,
    '#title' => t('List position'),
    '#description' => t('Multiple attributes on an add to cart form are sorted by this value and then by their name.<br />May be overridden at the product level.'),
    '#default_value' => isset($attribute->ordering) ? $attribute->ordering : 0,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#suffix' => l(t('Cancel'), 'admin/store/attributes'),
  );

  return $form;
}

/**
 * Form submission handler for uc_attribute_form().
 *
 * @see uc_attribute_form()
 */
function uc_attribute_form_submit($form, &$form_state) {
  if (!empty($form_state['values']['aid'])) {
    db_query("UPDATE {uc_attributes} SET name = '%s', label = '%s', ordering = %d, required = %d, display = %d, description = '%s' WHERE aid = %d", $form_state['values']['name'], $form_state['values']['label'], $form_state['values']['ordering'], $form_state['values']['required'], $form_state['values']['display'], $form_state['values']['description'], $form_state['values']['aid']);
  }
  else {
    db_query("INSERT INTO {uc_attributes} (name, label, ordering, required, display, description) VALUES ('%s', '%s', %d, %d, %d, '%s')", $form_state['values']['name'], $form_state['values']['label'], $form_state['values']['ordering'], $form_state['values']['required'], $form_state['values']['display'], $form_state['values']['description']);
    $form_state['values']['aid'] = db_last_insert_id('uc_attributes', 'aid');
  }

  $form_state['redirect'] = 'admin/store/attributes';
}

/**
 * Confirms the deletion of the given attribute.
 *
 * @see uc_attribute_delete_confirm_submit()
 */
function uc_attribute_delete_confirm($form_state, $attribute) {
  // If we got a bunk attribute, kick out an error message.
  if (empty($attribute)) {
    drupal_set_message(t('There is no attribute with that ID.'), 'error');
    drupal_goto('admin/store/attributes');
  }

  $form['aid'] = array('#type' => 'value', '#value' => $attribute->aid);
  $form['#redirect'] = 'admin/store/attributes';

  $count = db_result(db_query("SELECT COUNT(*) FROM {uc_product_attributes} WHERE aid = %d", $attribute->aid));

  $output = confirm_form($form, t('Are you sure you want to delete the attribute %name?', array('%name' => $attribute->name)),
              'admin/store/attributes', format_plural($count, 'There is @count product with this attribute.', 'There are @count products with this attribute.'),
              t('Delete'), t('Cancel'));

  return $output;
}

/**
 * Form submission handler for uc_attribute_delete_confirm().
 *
 * @see uc_attribute_delete_confirm()
 */
function uc_attribute_delete_confirm_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    db_query("DELETE FROM {uc_class_attribute_options} WHERE EXISTS (SELECT * FROM {uc_attribute_options} AS ao WHERE {uc_class_attribute_options}.oid = ao.oid AND ao.aid = %d)", $form_state['values']['aid']);
    db_query("DELETE FROM {uc_class_attributes} WHERE aid = %d", $form_state['values']['aid']);
    db_query("DELETE FROM {uc_product_options} WHERE EXISTS (SELECT * FROM {uc_attribute_options} AS ao WHERE {uc_product_options}.oid = ao.oid AND ao.aid = %d)", $form_state['values']['aid']);
    db_query("DELETE FROM {uc_product_adjustments} WHERE EXISTS (SELECT * FROM {uc_product_attributes} AS pa WHERE {uc_product_adjustments}.nid = pa.nid AND pa.aid = %d)", $form_state['values']['aid']);
    db_query("DELETE FROM {uc_product_attributes} WHERE aid = %d", $form_state['values']['aid']);
    db_query("DELETE FROM {uc_attribute_options} WHERE aid = %d", $form_state['values']['aid']);
    db_query("DELETE FROM {uc_attributes} WHERE aid = %d", $form_state['values']['aid']);
    drupal_set_message(t('Product attribute deleted.'));
  }
}

/**
 * Form builder for bulk product updates.
 *
 * @see uc_attribute_bulk_update_flush()
 * @see uc_attribute_bulk_update_flush_all()
 * @see theme_uc_attribute_bulk_update_form()
 * @ingroup forms
 */
function uc_attribute_bulk_update_form($form_state, $class) {
  drupal_set_title(check_plain($class->name));

  $form['node_type'] = array(
    '#type' => 'value',
    '#value' => $class->pcid,
  );

  // Select all products with current class.
  $result = pager_query("SELECT n.nid, n.title, n.status, nt.name FROM {node} n LEFT JOIN {node_type} nt ON nt.type = n.type WHERE nt.type = '%s'", 50, 0, NULL, $class->pcid);
  $nodes = array();
  while ($node = db_fetch_object($result)) {
    $nodes[$node->nid] = '';
    $form['title'][$node->nid] = array('#value' => l($node->title, 'node/' . $node->nid));
    $form['status'][$node->nid] =  array('#value' => ($node->status ? t('published') : t('not published')));
    $form['type'][$node->nid] =  array('#value' => check_plain($node->name));
  }

  $form['nodes'] = array('#type' => 'checkboxes', '#options' => $nodes);

  $form['flush'] = array(
    '#type' => 'submit',
    '#value' => t('Flush selected'),
    '#submit' => array('uc_attribute_bulk_update_flush'),
  );
  $form['flush_all'] = array(
    '#type' => 'submit',
    '#value' => t('Flush all'),
    '#submit' => array('uc_attribute_bulk_update_flush_all'),
  );

  $form['pager'] = array('#value' => theme('pager', NULL, 50, 0));

  return $form;
}

/**
 * Displays the bulk product update form.
 *
 * @see uc_attribute_bulk_update_form()
 * @ingroup themeable
 */
function theme_uc_attribute_bulk_update_form($form) {
  $output = '';

  $has_products = isset($form['title']) && is_array($form['title']);

  $header[] = theme('table_select_header_cell');
  $header[] = t('Title');
  $header[] = t('Status');
  $header[] = t('Type');

  if ($has_products) {
    foreach (element_children($form['title']) as $key) {
      $row = array();
      $row[] = drupal_render($form['nodes'][$key]);
      $row[] = drupal_render($form['title'][$key]);
      $row[] = drupal_render($form['status'][$key]);
      $row[] = drupal_render($form['type'][$key]);
      $rows[] = $row;
    }
  }
  else {
    $rows[] = array(array('data' => t('No products available.'), 'colspan' => '6'));
  }

  $output .= theme('table', $header, $rows);
  if ($form['pager']['#value']) {
    $output .= drupal_render($form['pager']);
  }

  $output .= drupal_render($form);

  $output .= '<p>' . t('Flush actions take place immediately and cannot be undone.') . '</p>';

  return $output;
}

/**
 * Form submission handler for uc_attribute_bulk_update_form().
 *
 * @see uc_attribute_bulk_update_form()
 */
function uc_attribute_bulk_update_flush($form, &$form_state) {
  $nodes = array_filter($form_state['values']['nodes']);
  foreach ($nodes as $nid) {
    $node = node_load($nid);
    uc_attribute_node_reset($node);
  }
  drupal_set_message(t("Bulk update completed successfully on selected products."));
}

/**
 * Form submission handler for uc_attribute_bulk_update_form().
 *
 * @see uc_attribute_bulk_update_form()
 */
function uc_attribute_bulk_update_flush_all($form, &$form_state) {
  $result = db_query("SELECT nid FROM {node} WHERE type = '%s'", $form_state['values']['node_type']);
  while ($item = db_fetch_object($result)) {
    $node = node_load($item->nid);
    uc_attribute_node_reset($node);
  }
  drupal_set_message(t("Bulk update completed successfully on all products in this class."));
}

/**
 * Changes the display of attribute option prices.
 *
 * @ingroup forms
 */
function uc_attribute_admin_settings() {
  $form = array();

  $form['uc_attribute_option_price_format'] = array(
    '#type' => 'radios',
    '#title' => t('Option price format'),
    '#default_value' => variable_get('uc_attribute_option_price_format', 'adjustment'),
    '#options' => array('none' => t('Do not display'),
      'adjustment' => t('Display price adjustment'),
      'total' => t('Display total price'),
    ),
    '#description' => t('Determines how price variations are displayed to the customer.  Prices may be displayed directly next to each attribute option in the attribute selection form either as a total price for the product with that option or as an adjustment (+ or -) showing how that option affects the product base price.  However, the total price will not be displayed if a product has more than one attribu