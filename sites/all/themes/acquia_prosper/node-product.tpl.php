<?php
// $Id: node-product.tpl.php,v 1.5 2010/09/17 21:36:06 eternalistic Exp $
?>
<?php if ($teaser){ ?>
	<?php if ($node->promo) { ?>
		<div class="featured-product">
            <div class="image">
                <a href="/<?php echo drupal_get_path_alias('node/' . $node->nid); ?>" title="<?php echo $node->title; ?>">
                <?php print theme('imagecache', 'product_featured', $node->field_product_images[0]['filepath'], $node->field_product_images[0]['data']['alt'], $node->field_product_images[0]['data']['title']); ?>
                </a>
            </div>
            <div class="info">
                  <h2><?php echo $node->title; ?></h2>
                  <div class="price-group">
	                <?php if ($node->field_product_price_on_demand[0]['value'] == 0){
						print $fusion_uc_display_price;
				    } else {
						echo '<div class="price-on-request"><p>Price on request</p></div>';
				    } ?>
				    <?php if ($has_options) { echo '<span class="from">from </span>'; } ?>
				  </div>

				  <div class="teaser-buttons">
				  	<a href="/<?php echo drupal_get_path_alias('node/' . $node->nid); ?>" class="teaser-full-details">Full details</a>
				  	<?php if ($has_options) { ?>
					  	<a href="/<?php echo drupal_get_path_alias('node/' . $node->nid); ?>" title="Select Model" class="teaser-add-to-cart">
						    Select Model
						</a>
						<?php } else { ?>
					  	<a href="/addtocart/<?php echo $node->nid; ?>" title="<?php echo $node->title; ?>" class="teaser-add-to-cart">
						    Add to cart
						</a>
						<?php } ?>
				  </div>

            </div>
        </div>
	
	<?php } else { ?>

	    <div id="node-<?php print $node->nid; ?>" class="node-teaser node clear-block product_in_list <?php print $node_classes; ?>">
				<?php print theme('grid_block', $tabs, 'content-tabs'); ?>
	          <div class="images">
		      
	              <a href="<?php print $node_url ?>" title="<?php print $title ?>">
		      <?php $image_info = image_get_info($node->field_product_images[0]['filepath']); ?>
		      <?php $image_style = ($image_info['width'] > $image_info['height']) ? 'product_list' : 'product_list_h';
			print theme('imagecache', $image_style, $node->field_product_images[0]['filepath'], $node->field_product_images[0]['data']['alt'], $node->field_product_images[0]['data']['title']);
		       ?>
	              </a>
	          </div><!-- /images -->

	          <div class="content grey-box-wrapper">

	            <?php $product_details = $fusion_uc_weight || $fusion_uc_dimensions || $fusion_uc_list_price || $fusion_uc_sell_price || $fusion_uc_model || $fusion_uc_cost; ?>
	            <div class="product-details grey-box clear<?php if (!$product_details): ?> field-group-empty<?php endif; ?>">
	                <h2 class="title">
			    <a href="<?php print $node_url ?>" title="<?php print $title ?>">
				<?php if ($node->field_product_group_title[0]['value'] == ''){ 
				    print $title ;
				} else {
				    print $node->field_product_group_title[0]['value'];
				} ?>
			    </a>
			</h2>
			
	                <div class="price-group">
	                    <?php if ($node->field_product_price_on_demand[0]['value'] == 0){
				print $fusion_uc_display_price;
			    } else {
				echo '<div class="price-on-request"><p>Price on request</p></div>';
			    } ?>
			    <?php if ($has_options) { echo '<span class="from">from </span>'; } ?>
	                    <?php //print $fusion_uc_add_to_cart; ?>
	                </div>

	                <div class="teaser-buttons">
					  	<a href="/<?php echo drupal_get_path_alias('node/' . $node->nid); ?>" class="teaser-full-details">Full details</a>
					  	<?php if ($has_options) { ?>
					  	<a href="/<?php echo drupal_get_path_alias('node/' . $node->nid); ?>" title="Select Model" class="teaser-add-to-cart">
						    Select Model
						</a>
						<?php } else { ?>
					  	<a href="/addtocart/<?php echo $node->nid; ?>" title="<?php echo $node->title; ?>" class="teaser-add-to-cart">
						    Add to cart
						</a>
						<?php } ?>
					  </div>

	            </div><!-- /product-details -->

	          </div><!-- /content -->

	    </div>
<?php } 
} else {?>
	<?php print theme('grid_block', $tabs, 'content-tabs'); ?>
    <div id="node-<?php print $node->nid; ?>" class="fullview node clear-block <?php print $node_classes; ?>">
      <div class="inner">
        <?php if ($page == 0): ?>
        <h2 class="title"><a href="<?php print $node_url ?>" title="<?php print $title ?>">
	    <?php print $title  ?>
	</a></h2>
        <?php endif; ?>
	
	<div id="main-infos">
	    <div class="images">
		<?php if ($option_images){
		    print $option_images;
		} else {
		    print acquia_prosper_image_gallery($node);
		} ?>
	    </div><!-- /images -->

	    <div class="prices">
	    
                <?php print $fusion_uc_add_to_cart; ?>
		
                <div class="stock-status">
                <?php if ($node->stock > 0){ ?>
					<div class="stock-info" class="instock">
						<div class="stock-info-inner">
							<h2>Immediate Despatch</h2>
							We currently hold stock of this item in our warehouse. Purchase this item before 11am and it will be shipped today</div></div>
							<div class="on-stock">Immediate Despatch
							<a href="#" class="availability-info-on"><img src="/sites/all/themes/acquia_prosper/images/question-mark-icon.jpg" /></a>
		    	<?php } else { ?>
					<div class="stock-info" class="offstock"><div class="stock-info-inner"><h2>Available - Possible Delay</h2>We are waiting on delivery of this item from the manufacturer. Add this item to your cart and we will contact you within 24 hours to advise delivery date. You will not be charged for this item</div></div>
                        <div class="off-stock">Available - Possible Delay
                        <a href="#" class="availability-info-off"><img src="/sites/all/themes/acquia_prosper/images/question-mark-icon.jpg" /></a>			
                <?php } ?>
		    		</div>
				</div>
		<br />
		
		<?php if (get_brand($node)) { ?>
		    <p><strong class="14">Brand:</strong> <?php echo get_brand($node); ?></p>
		<?php } ?>
		
		<?php if ($short_description != '') { ?>
		    <div class="short-description"><strong class="14">Details:</strong><br />
		    <?php echo $short_description; ?></div>
		<?php } ?>
		
	    </div>
	    
	
	</div>
	

          <div class="content" style="float: left;">
            <div id="content-body">
		
		
		<?php // GET TEHCNICAL DETAILS
		if ($is_variation){
		    if ($option_tech){ 
			$technical_details = '<p>' . l("Download the technical details in PDF", $option_tech) . '</p>';
		    }
        } elseif (!empty($node->field_pdf)){
            $technical_details = '<p>' . l("Download the technical details in PDF", $node->field_pdf[0]["filepath"]) . '</p>';
        } ?>
		
		<?php // GET MANUALS
		if ($is_variation){
		    if ($option_manuals && $option_manuals != ''){
			$manuals = $option_manuals;
		    }
    	        } else {
		    $manual_output = '';
		    foreach ($node->field_product_manuals as $manual) { 
		    	if (!empty($manual['data']) && $manual['data']['description'] != ''){
		    		$manual_link = $manual['data']['description'];
		    	} else {
		    		$manual_link = $manual["filename"];
		    	}
				$manual_output .= '<p>' . l($manual_link, $manual["filepath"], array('attributes' => array('class' => 'file-download'))) . '</p>';
		    }
		    $manuals = $manual_output;
		} ?>
		
		<?php // GET VIDEOS
		if ($is_variation){
		    if ($option_videos){
			$videos = $option_videos;
		    }
	        } else {
		    $videos_output = '';
		    foreach ($node->field_product_videos as $video) { 
                        $videos_output .= '<iframe width="460" height="254" src="http://www.youtube.com/embed/' . $video['value'] . '?rel=0" frameborder="0" allowfullscreen></iframe>
                        <br />';
			$videos = $videos_output;
                    }
		}?>
		
		<?php $delivery = '';
		if($node->field_product_delivery[0]['value'] != ''){
		    $delivery .= $node->field_product_delivery[0]['value'];
		} ?>
		
		<?php ?>
                 <ul class="product-tabs">
                    <li><a href="#" id="description" class="current">Full Details</a></li>
		    <?php if ((isset($technical_details) && $technical_details != '') || (isset($manuals) && $manuals != '')) { ?>
                    <li><a href="#" id="details">Technical details & Manuals</a></li>
		    <?php } ?>
		    <?php if (isset($videos) && $videos != '') { ?>
                    <li><a href="#" id="videos">Watch a Video</a></li>
		    <?php } ?>
		    <?php if ($node->field_product_delivery_tab[0]['value'] != '') { ?>
                    <li><a href="#" id="delivery">Delivery & Payments</a></li>
		    <?php } ?>
                </ul>
                 
                <!-- tab "panes" -->
                <div class="product-panes">
                    <div class="pane_description" id="panetion">
					<?php if ($option_body) {
					    print $option_body;
					} else {
					    echo $node->field_product_description[0]['value'];
					} ?>
				    </div>
		    
		    		<?php if ((isset($technical_details) && $technical_details != '') || (isset($manuals) && $manuals != '')) { ?>
                    <div class="pane_details" style="display: none">
						<?php print $technical_details; ?>
                        <?php if ((isset($manuals) && $manuals != '') && (isset($technical_details) && $technical_details != '')) {
                            print '<hr />';
                        } ?>
                        <?php if (isset($manuals) && $manuals != '') { ?>
                            <h3>Manuals</h3>
			    			<?php print $manuals; ?>
                        <?php } ?>
                        
                    </div>
		    		<?php } ?>
		    
				    <?php if ((isset($videos) && $videos != '')) { ?>
		                <div class="pane_videos" style="display: none">
		    	            <h3>Videos</h3>
					    	<?php print $videos; ?>
		                </div>
				    <?php } ?>
		    
				    <?php if ($node->field_product_delivery_tab[0]['value'] != ''){ ?>
				    <div class="pane_delivery" style="display: none">
						<?php switch ($node->field_product_delivery_tab[0]['value']) {
						    case 'small-items-delivery-details':
								$block = module_invoke('block', 'block', 'view', 5);
								print $block['content'];
								break;
						    case 'large-and-oversized-goods-details':
								$block = module_invoke('block', 'block', 'view', 6);
								print $block['content'];
								break;
						    case 'store-pickup-only-details':
								$block = module_invoke('block', 'block', 'view', 7);
								print $block['content'];
								break;
						} ?>
				    </div>
				    <?php } ?>
		    
                </div>
              <?php //print $fusion_uc_body; ?>
            </div> <?php  ?>
	    
	    <?php /* ?>
	    <div id="icon-dock">
		<!-- <div class="mac-dock">
		    <img src="/sites/all/themes/acquia_prosper/images/icon_img.png" alt=""/>
		    <img src="/sites/all/themes/acquia_prosper/images/icon_video.png" alt=""/>
		    <img src="/sites/all/themes/acquia_prosper/images/icon_file.png" alt=""/>
		</div> -->
		<div>
		<?php if ($is_variation && $variation){
		    if (!empty($variation->field_product_images)){ ?>
		    <a href="#" class="product-img-trigger" title="Pictures"><img src="/sites/all/themes/acquia_prosper/images/icon_img.png" alt=""/></a>
		    <?php } ?>
		<?php } else {
		    if (!empty($node->field_product_images)){ ?>
		    <a href="#" class="product-img-trigger" title="Pictures"><img src="/sites/all/themes/acquia_prosper/images/icon_img.png" alt=""/></a>
		<?php }
		} ?>
		    <a href="?width=500&height=460&inline=true#product_videos" class="colorbox-inline" title="Videos"><img src="/sites/all/themes/acquia_prosper/images/icon_video.png" alt=""/></a>
		    <a href="?width=400&height=300&inline=true#technical_details" class="colorbox-inline" title="Technical Details / Manuals"><img src="/sites/all/themes/acquia_prosper/images/icon_file.png" alt=""/></a>
		    <a href="#" title="Print" id="print"><img src="/sites/all/themes/acquia_prosper/images/icon_printer.png" alt=""/></a>
		</div> 
	    </div>
	    
	    
	    
            <?php $product_details = $fusion_uc_weight || $fusion_uc_dimensions || $fusion_uc_list_price || $fusion_uc_sell_price || $fusion_uc_model || $fusion_uc_cost; ?>
            <div id="product-details" class="clear<?php if (!$product_details): ?> field-group-empty<?php endif; ?>">
              <div id="field-group">
                <?php print $fusion_uc_weight; ?>
                <?php print $fusion_uc_dimensions; ?>
                <?php print $fusion_uc_model; ?>
                <?php print $fusion_uc_list_price; ?>
                <?php print $fusion_uc_sell_price; ?>
                <?php print $fusion_uc_cost; ?>
                  <div class="stock-status">
                    <?php if ($stock > 0){ ?>
                        <p class="on-stock">ON STOCK</p>
                    <?php } else { ?>
                        <p class="off-stock">OFF STOCK</p>
                    <?php } ?>
                </div>
              </div>

              <div id="price-group">
                <?php print $fusion_uc_display_price; ?>
                <?php print $fusion_uc_add_to_cart; ?>
              </div>
            </div><!-- /product-details -->
            
            <?php */ ?>

            <?php print tilers_products_back_to_list($node); ?>
            
          </div><!-- /content -->
        </div><!-- /product-group -->
      </div><!-- /inner -->

      <?php //if ($node_bottom && !$teaser): ?>
      <div id="node-bottom" class="node-bottom row nested">
        <div id="node-bottom-inner" class="node-bottom-inner inner">
	    <?php 
	    $r = trp_get_products($node->field_related_products); ?>
            <?php if (!empty($r)){
		print theme('display_trp', $r);
	    } ?>
        </div><!-- /node-bottom-inner -->
      </div><!-- /node-bottom -->
      <?php //endif; ?>
    </div>
    
    <?php /* hidden content showing in the modal box */ ?>
    <?php if ((isset($technical_details) && $technical_details != '') || (isset($manuals) && $manuals != '')){ ?>
        <div class="hidden-content" id="technical_details">
	    <?php if (isset($technical_details) && $technical_details != '') { ?>
	    <h2>Technical details</h2>
	    <?php print $technical_details; ?>
	    <?php } ?>
                        
            <?php if ((isset($manuals) && $manuals != '') && (isset($technical_details) && $technical_details != '')) {
                print '<hr />';
            } ?>
	    
	    <?php if (isset($manuals) && $manuals != '') { ?>
            <h3>Manuals</h3>
	    <?php print $manuals; ?>
            <?php } ?>
        </div>
    <?php } ?>
		    
    <?php if (isset($videos) && $videos != '') { ?>
        <div class="hidden-content" id="product_videos">   
            <?php if (isset($videos) && $videos != '') { ?>
                <h3>Videos</h3>
		<?php print $videos; ?>
            <?php } ?>
        </div>
    <?php } ?>
    
<?php } ?>