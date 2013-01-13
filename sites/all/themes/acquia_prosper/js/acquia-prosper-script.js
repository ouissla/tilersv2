// $Id: acquia-prosper-script.js,v 1.3 2010/09/17 21:36:06 eternalistic Exp $

Drupal.behaviors.acquia_prosperRoundedCorner = function (context) {
  $(".prosper-rounded-title h2.block-title").corner("top 5px"); 
  $(".prosper-shoppingcart h2.block-title").corner("top 5px"); 
  $(".prosper-menu-list h2.block-title").corner("top 5px"); 
  $(".prosper-grayborder-darkbackground .inner").corner("7px"); 
};


$(document).ready(function(){

  
    /*$('#accordion-1').dcAccordion({
        eventType: 'click',
        autoClose: true,
        disableLink: true,
        speed: 'slow',
        showCount: false,
        autoExpand: false,
        classExpand : 'dcjq-current-parent'
    });*/


    display_coupon();
    change_picture();
    switch_tab();
    
    $('.fullview').find('#edit-attributes-1-1').change(function(){
      window.location = '/optionredirect/' + $(this).val();
    });
    
    if ($.isFunction(colorbox)){
    //$('a.product-img').colorbox({rel:'product-img'});
    }

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

    $('#custom-mega a.nofollow').click(function(e){
      e.preventDefault();
    });
    $('#custom-mega li.lvl1').hoverIntent(function(){
      var megawrapper = $(this).find('.megawrapper');
      $('.megawrapper').hide();
      megawrapper.slideDown();
    }, function(){
      var megawrapper = $(this).find('.megawrapper');
      megawrapper.hide();
    });

    $('.menu-block-2 > ul > li.expanded > a').click(function(e){
        e.preventDefault();
        var submenu = $(this).next('ul');
        if (submenu.is(':visible')){
          submenu.slideUp();
        } else {        
          submenu.slideDown();
        }
    });

    $('#cart-block-title').hoverIntent(function(){
      $(this).find('#subcontent').slideDown();
    }, function(){
      $(this).find('#subcontent').slideUp();
    });

    if ($('.availability-info-on').length > 0){
      $('.availability-info-on').qtip({
         content: $('.availability-info-on').parent().parent().find('.stock-info-inner').html(),
         show: 'mouseover',
         hide: 'mouseout',
         position: {
            corner: {
               target: 'topLeft',
               tooltip: 'bottomLeft'
            }
         }
      });
    }
    if($('.availability-info-off').length > 0){
      $('.availability-info-off').qtip({
         content: $('.availability-info-off').parent().parent().find('.stock-info-inner').html(),
         show: 'mouseover',
         hide: 'mouseout',
         position: {
            corner: {
               target: 'topLeft',
               tooltip: 'bottomLeft'
            }
         }
      });
    }

    //GROUT CALCULATOR
    if ($('#grout-calculator-form').length > 0){
      $(this).find('edit-calculate').hide();

      var tile_length = $('#edit-tile-length');
      var tile_width = $('#edit-tile-width');
      var grout_length = $('#edit-grout-length');
      var grout_width = $('#edit-grout-width');
      var tile_length = $('#edit-tile-length');
      var density = $('#edit-density');
      var surface = $('#edit-surface');
      var grout_total = $('#edit-grout-total');

      grout_total.val(calculate_grout(tile_length.val(), tile_width.val(), grout_length.val(), grout_width.val(), density.val(), surface.val()));

      $('.calculate-item').change(function(){
        grout_total.val(calculate_grout(tile_length.val(), tile_width.val(), grout_length.val(), grout_width.val(), density.val(), surface.val()));
      });
    }
    
    /*$('.price-group').each(function(){
      var w1 = $(this).find('.product-info').outerWidth();
      var w2 = $(this).find('.from').outerWidth();
      var w3 = $(this).find('.price-on-request').outerWidth();
      $(this).width(w1 + w2 + w3 + 5);
    });*/

             /*
              $('.menu-block-2 ul li a').click(function(e){
                var sub = $(this).parent().find('ul:first');
                if (sub.length > 0){
                  e.preventDefault();
                }
              });

              $('.menu-block-2 ul li a').hoverIntent(function(){
                var sub = $(this).parent().find('ul:first');
                if (sub.length > 0){
                  sub.find('li').slideDown();
                }
              },
              function(){
                $(this).find('ul:first').slideUp();
              }); */

    //global variable
    var maxHeight = 0;

    //the function
    $(".grey-box .title").each(function(){
      if ($(this).height() > maxHeight) {
        maxHeight = $(this).height();
      }
    });
    $(".grey-box .title").height(maxHeight);


    // Variaition list
    $('#variations-list').change(function(){
      window.location = '/node/' + $(this).val();
    });

    // Reorder items in cart review
    if ($('fieldset#cart-pane').length > 0){
      var table = $('fieldset#cart-pane').find('tbody');

      var output = '';
      var offstock = '';
      var subtotal = '';
      table.find('tr').each(function(){
        if ($(this).find('td.price').length > 0 && $(this).find('td.price').find('span').html() != "$0.00"){
          output += '<tr>' + $(this).html() + '</tr>';
        } else if ($(this).find('td.subtotal').length > 0){
          subtotal += '<tr>' + $(this).html();
        } else {
          offstock += '<tr class="outstock">' + $(this).html() + '</tr>';
        }
      });
      table.html(output + offstock + subtotal);

      var oddeven = 'odd';
      table.find('tr').each(function(){
        $(this).addClass(oddeven);
        if (oddeven == 'odd'){
          oddeven = 'even';
        } else {
          oddeven = 'odd';
        }
      });
    }

    // Reorder items in order review page
    if ($('.order-review-table').length > 0){
      var table = $('table.cart-review');
      var output = '';
      var offstock = '';
      table.find('tr').each(function(){
        if ($(this).find('td.price').length > 0 && $(this).find('td.price').find('span').html() != "$0.00"){
          output += '<tr>' + $(this).html() + '</tr>';
        } else {
          offstock += '<tr class="outstock">' + $(this).html() + '</tr>';
        }
      });
      table.html(output + offstock);
      setOddEvenClasses(table);
    }
    
});

var calculate_grout = function(tl, tw, gl, gw, d, s){
  var a = (((1*tl) + (1*tw)) / (tl * tw)) * gl * gw * d;
  var total = a * s;
  return Math.round(total*100)/100;
}

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

var setOddEvenClasses = function(table){
  var oddeven = 'odd';
  table.find('tr').each(function(){
    $(this).addClass(oddeven);
    if (oddeven == 'odd'){
      oddeven = 'even';
    } else {
      oddeven = 'odd';
    }
  });
}