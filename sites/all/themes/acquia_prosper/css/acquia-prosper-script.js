// $Id: acquia-prosper-script.js,v 1.3 2010/09/17 21:36:06 eternalistic Exp $

Drupal.behaviors.acquia_prosperRoundedCorner = function (context) {
  $(".prosper-rounded-title h2.block-title").corner("top 5px"); 
  $(".prosper-shoppingcart h2.block-title").corner("top 5px"); 
  $(".prosper-menu-list h2.block-title").corner("top 5px"); 
  $(".prosper-grayborder-darkbackground .inner").corner("7px"); 
};


$(document).ready(function(){
    $('#accordion-1').dcAccordion({
        eventType: 'click',
        autoClose: true,
        disableLink: true,
        speed: 'slow',
        showCount: false,
        autoExpand: false,
        classExpand : 'dcjq-current-parent'
    });

    /*if ($('#mycarousel').length > 0){
        $('#mycarousel').find('li').show();
        $('#loader').hide();
        $('#mycarousel').jcarousel({
            scroll: 1,
            auto: 4,
            wrap: "circular",
            animation: "slow",
            buttonPrevHTML: null,
            buttonNextHTML: null
        });
    }*/

    display_coupon();
    change_picture();
    switch_tab();
    
    $('.fullview').find('#edit-attributes-1-1').change(function(){
      window.location = '/optionredirect/' + $(this).val();
    });
    
    $('a.product-img').colorbox({rel:'product-img'});
    
    $('#print').click(function(){
      window.print();
    });
    
    $('.product-img-trigger').click(function(){
      $('#product-main-img').trigger('click');
    });
    
    $('#user-register legend:first-child').html('Create an account');
    
    $('.product_in_list img').each(function(){
      if ($(this).height() > 120){
        $(this).height(120).width('auto');
      }
    });
    
    $('#mega-menu-1').dcMegaMenu({
      rowItems: '5',
      speed: 3,
      effect: 'slide'
    });
    
    $('.dc-mega').click(function(elem){
      elem.preventDefault();
    });
    $('.mega-hdr-a').click(function(elem){
      elem.preventDefault();
    });
    
    $('.price-group').each(function(){
      var w1 = $(this).find('.product-info').outerWidth();
      var w2 = $(this).find('.from').outerWidth();
      var w3 = $(this).find('.price-on-request').outerWidth();
      $(this).width(w1 + w2 + w3);
    });
    
    // The slider being synced must be initialized first
    $('#carousel').flexslider({
      animation: "slide",
      controlNav: false,
      animationLoop: false,
      slideshow: false,
      itemWidth: 210,
      itemMargin: 5,
      asNavFor: '#slider'
    });
     
    $('#slider').flexslider({
      animation: "slide",
      controlNav: false,
      animationLoop: false,
      slideshow: false,
      sync: "#carousel"
    });
    
});

var display_coupon = function(){
    var block = $('#uc-coupon-block-form');
    var but = $('#cart-form-buttons');
    but.before(block);
}

var change_picture = function(){

    $('.mini_picture').click(function(){
        var current_image = $('#main_picture');
        var mini_url = $(this).attr('src');

        //the main picture fades out and we display a loading gif
        //current_image.fadeout();

        var new_src = mini_url.replace('gallery', 'product_full');
        current_image.attr('src', new_src);
    });

}

var switch_tab = function(){

    $('ul.product-tabs').find('a').click(function(){
        $('ul.product-tabs').find('a').removeClass('current');
        $(this).addClass('current');

        var current_id = $(this).attr('id');

        var pane = $('div.product-panes').find('div.pane_' + current_id);
        pane.show();
        pane.siblings().hide();
        return false;
    });
}
