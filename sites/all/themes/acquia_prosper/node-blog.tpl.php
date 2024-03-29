<?php
// $Id: node.tpl.php,v 1.4 2010/09/17 21:36:06 eternalistic Exp $
?>

<div id="node-<?php print $node->nid; ?>" class="node <?php print $node_classes; ?> blog-article-container">
  <div class="inner">
      <?php $u = user_load($node->uid); ?>
      <p class="submition-info"><span><?php echo date('F d, Y', $node->created); ?></span><span>By <?php echo $u->name; ?></span></p>


      <h2 class="title"><?php print $title ?></h2>
    
  
    <?php if ($node_top && !$teaser): ?>
    <div id="node-top" class="node-top row nested">
      <div id="node-top-inner" class="node-top-inner inner">
        <?php print $node_top; ?>
      </div><!-- /node-top-inner -->
    </div><!-- /node-top -->
    <?php endif; ?>

    <div class="content clearfix">
      <?php print $picture ?>
      <?php print $content ?>
    </div>
  
    <?php if ($terms): ?>
    <div class="terms">
      <?php print $terms; ?>
    </div>
    <?php endif;?>
    
    <?php if ($links): ?>
    <div class="links">
      <?php print $links; ?>
    </div>
    <?php endif; ?>
  </div><!-- /inner -->

  <?php if ($node_bottom && !$teaser): ?>
  <div id="node-bottom" class="node-bottom row nested">
    <div id="node-bottom-inner" class="node-bottom-inner inner">
      <?php print $node_bottom; ?>
    </div><!-- /node-bottom-inner -->
  </div><!-- /node-bottom -->
  <?php endif; ?>
</div><!-- /node-<?php print $node->nid; ?> -->
