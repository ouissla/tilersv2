<?php
function tilers_discounts_views_data() {
  $data['tilers_discounts_order_codes']['table']['group'] = t('UC Discounts');

  $data['tilers_discounts_order_codes']['table']['join'] = array(
    'uc_orders' => array(
      'left_field' => 'order_id',
      'field' => 'order_id',
    ),
  );

  $data['tilers_discounts_order_codes']['codes'] = array(
    'title' => t('Codes Applied'),
    'help' => t('A comma separated listing of the coupon codes used on this order'),
    'field' => array(
      'handler' => 'views_handler_field',
    ),
  );

  return $data;
}
