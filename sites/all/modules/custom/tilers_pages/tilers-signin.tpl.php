<script type="text/javascript" src="<?php echo base_path(); ?>sites/all/themes/acquia_prosper/js/jquery-validation/jquery.validate.min.js"></script>

<script type="text/javascript">
 $(document).ready(function() {
	$("#user-register").validate();
    
    $("#edit-pass-pass2").rules("add", {
     required: true,
     minlength: 5,
     equalTo: "#edit-pass-pass1",
     messages: {
       required: "Required input",
       minlength: jQuery.format("Please, at least {0} characters are necessary"),
       equalTo: "Password are not matching"
     }
    });

    $("#edit-conf-mail").rules("add", {
     required: true,
     equalTo: "#edit-mail",
     messages: {
       required: "This field is requiered.",
       equalTo : "Email addresses don't match"
     }
    });

});
</script>

<div id="signin_page">
    <div id="signin_page_left">
      <div class="signin-inner-content">
        <fieldset>
            <legend>Existing Customer Login</legend>
            <?php $block = module_invoke('user', 'block', 'view', 0);
            print $block['content']; ?>
        </fieldset>
        <div id="signin_img">
            <img src="<?php echo base_path(); ?>sites/all/themes/cosmic/images/signup.jpg" />
        </div>
      </div>
    </div>
    <div id="signin_page_right">
      <div class="signin-inner-content">
        <?php print drupal_get_form('user_register'); ?>
      </div>
</div>
</div>