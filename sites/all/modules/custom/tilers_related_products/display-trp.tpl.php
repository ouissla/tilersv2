<h2>You may also like :</h2>
<?php /* ?>
<div id="tilers_related_products">
    
    <?php foreach ($products as $product){  ?>
        <div>
            <?php if ($product->type == "variation"){
              $url = url(substr($_SERVER['REQUEST_URI'], 1), array('query' => array('variation' => $product->field_variation_oid[0]['value'])));
            } else {
              $url = '/' . drupal_get_path_alias('node/' . $product->nid);
            } ?>
            <a href="<?php echo $url; ?>">
                <?php print theme('imagecache', 'related_products', $product->field_product_images[0]['filepath'], $product->field_product_images[0]['data']['alt'], $product->field_product_images[0]['data']['title']); ?>
            </a>
            <div style="text-align: center;">
                <a href="<?php echo $url; ?>"><?php echo $product->title; ?></a>
            </div>
        </div>
    <?php } ?>
    
    
</div><?php */ ?>

<div id="tilers_related_products">
    
    <?php foreach ($products as $node){ ; ?>

    <?php print node_view($node, 'teaser'); ?>

    <?php } ?>
    
    
</div>