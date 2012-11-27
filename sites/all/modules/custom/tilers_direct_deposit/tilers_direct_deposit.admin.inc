<?php
// $Id$

/**
 * @file
 * Payment pack administration menu items.
 *
 */

/**
 * Receive a Direct Deposit for an order.
 */
function tilers_direct_deposit_receive_directdeposit_form($form_state, $order) {
  $balance = uc_payment_balance($order);
  $form['balance'] = array('#value' => uc_currency_format($balance));
  $form['order_id'] = array(
    '#type' => 'hidden',
    '#value' => $order->order_id,
  );
  $form['directdeposit_exists'] = array(
    '#type' => 'directdepositbox',
    '#title' => t('Direct Deposit has already been received.'),
    '#attributes' => array('onclick' => 'receive_directdeposit_toggle(this.directdeposited);'),
  );
  $form['amount'] = array(
    '#type' => 'textfield',
    '#title' => t('Amount'),
    '#default_value' => uc_currency_format($balance, FALSE, FALSE),
    '#size' => 10,
    '#field_prefix' => variable_get('uc_sign_after_amount', FALSE) ? '' : variable_get('uc_currency_sign', '$'),
    '#field_suffix' => variable_get('uc_sign_after_amount', FALSE) ? variable_get('uc_currency_sign', '$') : '',
  );
  $form['comment'] = array(
    '#type' => 'textfield',
    '#title' => t('Reciept No. / Comment'),
    '#description' => t('Any notes about the Direct Deposit, like receipt number.'),
    '#size' => 64,
    '#maxlength' => 256,
  );
  $form['clear'] = array(
    '#type' => 'fieldset',
    '#title' => t('Expected clear date'),
    '#collapsible' => FALSE,
  );
  $form['clear']['clear_month'] = uc_select_month(NULL, format_date(time(), 'custom', 'n'));
  $form['clear']['clear_day'] = uc_select_day(NULL, format_date(time(), 'custom', 'j'));
  $form['clear']['clear_year'] = uc_select_year(NULL, format_date(time(), 'custom', 'Y'), format_date(time(), 'custom', 'Y'), format_date(time(), 'custom', 'Y') + 1);
  foreach (array('clear_month', 'clear_day', 'clear_year') as $key) {
    $form['clear'][$key]['#prefix'] = '<div style="float: left; margin-right: 1em;">';
    $form['clear'][$key]['#suffix'] = '</div>';
  }
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Receive Direct Deposit'),
  );

  return $form;
}

function theme_tilers_direct_deposit_receive_directdeposit_form($form) {
  uc_add_js(drupal_get_path('module', 'uc_payment') .'/uc_payment.js');

  $output = '<p>'. t('Use the form to enter the Direct Deposit into the payments system and set the expected clear date.') .'</p>';
  $output .= '<p><strong>'. t('Order balance:') .'</strong> '
           . drupal_render($form['balance']) .'</p>';

  $output .= drupal_render($form);

  return $output;
}

function tilers_direct_deposit_receive_directdeposit_form_validate($form, &$form_state) {
  if (!$form_state['values']['directdeposit_exists'] && !is_numeric($form_state['values']['amount'])) {
    form_set_error('amount', t('The amount must be a number.'));
  }
}

function tilers_direct_deposit_receive_directdeposit_form_submit($form, &$form_state) {
  global $user;

  uc_payment_enter($form_state['values']['order_id'], _payment_method_data('directdeposit', 'name'),
                   $form_state['values']['amount'], $user->uid, '', $form_state['values']['comment']);

  db_query("INSERT INTO {tilers_payment_directdeposit} (order_id, clear_date) "
          ."VALUES (%d, %d)", $form_state['values']['order_id'],
           mktime(12, 0, 0, $form_state['values']['clear_month'], $form_state['values']['clear_day'], $form_state['values']['clear_year']));

  drupal_set_message(t('directdeposit received, expected clear date of !date.',
                     array('!date' => $form_state['values']['clear_month'] .'/'
                                    . $form_state['values']['clear_day'] .'/'
                                    . $form_state['values']['clear_year'])));

  drupal_goto('admin/store/orders/'. $form_state['values']['order_id']);
}

