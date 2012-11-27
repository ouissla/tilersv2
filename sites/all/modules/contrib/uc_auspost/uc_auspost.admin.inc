<?php
// $Id$

/**
 * @file
 * Australia Post administration menu items.
 */

/**
 * Australia Post Online Tool settings.
 *
 * Configure which Aus Post services are quoted to customers.
 *
 * @ingroup forms
 * @see uc_admin_settings_validate()
 */

function uc_auspost_admin_settings(){
  $form = array();
  
  $form['uc_auspost_services'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Australia Post Services'),
    '#default_value' => variable_get('uc_auspost_services', _uc_auspost_services()),
    '#options' => _uc_auspost_services(),
    '#description' => t('Select the Australia Post services that are available to customers.'),
  );
  
  $form['uc_auspost_intl_services'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Australia Post International Services'),
    '#default_value' => variable_get('uc_auspost_intl_services', _uc_auspost_intl_services()),
    '#options' => _uc_auspost_intl_services(),
    '#description' => t('Select the Australia Post services that are available to customers.'),
  );
    
  $form['uc_auspost_display_graphics'] = array(
    '#type' => 'radios',
    '#title' => t('Display Service Logos During Checkout?'),
    '#default_value' => variable_get('uc_auspost_display_graphics',1),
    '#options' => array(
      t('No'),
      t('Yes')
    ),
    '#description' => t('Choice is between displaying text or logos for the various AusPost service types at checkout.')
  );
    
  $form['uc_auspost_display_time_estimate'] = array(
    '#type' => 'radios',
    '#title' => t('Display Delivery Time Estimate During Checkout?'),
    '#default_value' => variable_get('uc_auspost_display_time_estimate',1),
    '#options' => array(
      t('No'),
      t('Yes')
    ),
    '#description' => t('Display the DRC\'s postage time estimate')
  );
   
  $form['uc_auspost_all_in_one'] = array('#type' => 'radios',
    '#title' => t('Product packages'),
    '#default_value' => variable_get('uc_auspost_all_in_one', 1),
    '#options' => array(
      0 => t('Each in its own package'),
      1 => t('All in one'),
    ),
    '#description' => t('Indicate whether each product is quoted as shipping separately or all in one package.'),
  );
      
  $form['uc_auspost_display_disclaimer'] = array(
    '#type' => 'radios',
    '#title' => t('Display Disclaimer During Checkout?'),
    '#default_value' => variable_get('uc_auspost_display_disclaimer',1),
    '#options' => array(
      t('No'),
      t('Yes')
    ),
    '#description' => t('Display the Australia Post eDRC Disclaimer at checkout?') . '<br />' .t('Note that it is a condition of use of the eDRC that the disclaimer be displayed.')
  );
    
  $form['uc_auspost_send_dimensions'] = array(
    '#type' => 'radios',
    '#title' => t('Send Product Dimensions to the DRC?'),
    '#default_value' => variable_get('uc_auspost_send_dimensions',0),
    '#options' => array(
      t('No')
//      t('Yes')
    ),
    '#description' => t('Send the product dimensions as part of retrieving a quote?') . '<br />' .t('Note that the dimensions of a package have no bearing on the cost of shipping.')
  );

  $form['uc_auspost_shipment_time'] = array(
    '#type' => 'textfield',
    '#title' => t('Shipment Processing Time'),
    '#default_value' =>  variable_get('uc_auspost_shipment_time',  '1'),
    '#description' => t('Shipment processing time (in days). Allows for some leeway to be factored in.') . '<br />' .t('Note that weekends are automatically factored in.'),
  );  
 
  $form['uc_auspost_markup_type'] = array(
    '#type' => 'select',
    '#title' => t('Markup type'),
    '#default_value' => variable_get('uc_auspost_markup_type', 'percentage'),
    '#options' => array(
      'percentage' => t('Percentage (%)'),
      'multiplier' => t('Multiplier (x)'),
      'currency' => t('Addition (!currency)', array('!currency' => variable_get('uc_currency_sign', '$'))),
    ),
  );
  
  $form['uc_auspost_markup'] = array(
    '#type' => 'textfield',
    '#title' => t('Shipping rate markup'),
    '#default_value' => variable_get('uc_auspost_markup', '0'),
    '#description' => t('Markup shipping rate quote by dollar amount, percentage, or multiplier.'),
  ); 
  
  $form['uc_auspost_uri'] = array(
    '#type' => 'textfield',
    '#title' => t('eDRC URI'),
    '#default_value' =>  variable_get('uc_auspost_uri',  variable_get('uc_auspost_markup', 'http://drc.edeliver.com.au/ratecalc.asp')),
    '#description' => t('Location of the rate calculator. Shouldn\'t need to be changed'),
  );  
  
  return system_settings_form($form);
}
