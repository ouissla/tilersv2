<?php
// $Id: node-product.tpl.php,v 1.5 2010/09/17 21:36:06 eternalistic Exp $
?>
<?php /*if ($teaser){ ?>
    <div id="node-<?php print $node->nid; ?>" class="node clear-block product_in_list <?php print $node_classes; ?>">

          <div class="images">
              <a href="<?php print $node_url ?>" title="<?php print $title ?>">
                <?php print theme('imagecache', 'product_list', $node->field_image_cache[0]['filepath'], $node->field_image_cache[0]['data']['alt'], $node->field_image_cache[0]['data']['title']); ?>
              </a>
          </div><!-- /images -->

          <div class="content">

            <?php $product_details = $fusion_uc_weight || $fusion_uc_dimensions || $fusion_uc_list_price || $fusion_uc_sell_price || $fusion_uc_model || $fusion_uc_cost; ?>
            <div class="product-details clear<?php if (!$product_details): ?> field-group-empty<?php endif; ?>">
                <h2 class="title"><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
                <!--<div class="stock-status">
                    On stock
                </div>-->
                <div id="price-group">
                    <?php print $fusion_uc_display_price; ?>
                    <?php //print $fusion_uc_add_to_cart; ?>
                </div>
            </div><!-- /product-details -->

          </div><!-- /content -->

    </div>
<?php } else { */?>
    <div id="node-<?php print $node->nid; ?>" class="node clear-block <?php print $node_classes; ?>">
        <div class="special_image">
            <a href="<?php echo $node->field_special_url[0]['value']; ?>">
                <?php print theme('imagecache', 'special', $node->field_special_image[0]['filepath'], $node->field_special_image[0]['data']['alt'], $node->field_special_image[0]['data']['title']); ?>
            </a>
        </div>

      <div class="special_description">
          <?php echo $node->content['body']['#value']; ?>
      </div>
    </div>
<?php //} ?>