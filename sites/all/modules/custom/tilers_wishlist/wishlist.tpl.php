<?php if (isset($cookie) && $cookie == 1){
        print drupal_get_form('tilers_wishlist_display_form'); ?>
        
    <p>You can save your wishlist for up to 7 days in the database after which it will automatically be deleted.<br />
    When you return to meternitywear.com.au just login before the expiry date and your wishlist will be reinstated.<br />
    If you would like to email a copy to yourself and a friend just complete below.
    </p>
    
    <div id="wishlist_copy">
    <form method="post" action="<?php echo base_path(); ?>send-me-a-copy" id="send_me_a_copy">
        <input type="submit" value="Email me a copy" />
    </form>

    </div>
<?php } else { ?>
    <p>You don't have any wishlist</p>
<?php } ?>

