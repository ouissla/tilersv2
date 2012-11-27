//$Id: tilers_discounts.js,v 1.5.2.11 2010/12/11 03:53:15 jrust Exp $

var tilers_discountsisUpdating = false;

//Handles onload calls for tilers_discounts.
function tilers_discountsOnLoad(e) {
  context = $('body');
  tilers_discountsProcessCodes(context, e);

  //Add click event listener to discounts pane button once
  $("input[id*=uc-discounts-button]:not(.tilers_discountsOnLoad-processed)", 
    context).addClass("tilers_discountsOnLoad-processed").click(function(e) {
      tilers_discountsProcessCodes(context, e);
      //Return false to prevent default actions and propogation
      return false;
    });
}

//Processes currently entered discounts
function tilers_discountsProcessCodes(context, e) {
  //If currently updating, wait
  if (tilers_discountsisUpdating) {
    setTimeout(function() { tilers_discountsProcessCodes(context); }, 200);
    return;
  }

  var parameterMap = {};
  parameterMap["uc-discounts-codes"] = $("textarea[id*=uc-discounts-codes]", context).val();

  //Show loading container
  var progress = new Drupal.progressBar("tilers_discountsProgress");
  progress.setProgress(-1, Drupal.settings.tilers_discounts.progress_msg);
  var messages_container = $(".uc-discounts-messages-container");
  messages_container.empty().append(progress.element);
  messages_container.addClass("solid-border");

  $.ajax({
    type: "POST",
    url: Drupal.settings.tilers_discounts.url,
    data: parameterMap,
    complete : function(xmlHttpRequest, textStatus) {
      //Hide loading container
      $(".uc-discounts-messages-container").removeClass("solid-border").empty();

      //If status is not 2XX
      if ( parseInt(xmlHttpRequest.status) != 0 && parseInt(xmlHttpRequest.status / 100) != 2) {
        alert(Drupal.settings.tilers_discounts.err_msg);
        return;
      }

      var responseText = xmlHttpRequest.responseText;
      var calculateDiscountResponse = null;
      try {
        responseText = xmlHttpRequest.responseText;
        calculateDiscountResponse = Drupal.parseJson(responseText);
      }
      catch (err) {
        alert(Drupal.settings.tilers_discounts.response_parse_err_msg + responseText);
        return;
      }

      try {
        tilers_discountsProcessCalculateDiscountResponse(calculateDiscountResponse, context);
        // Re-process shipping quotes since discount codes can affect shipping via conditional actions
        // Only re-process if they clicked the button so as to not override their shipping preference
        if (e.type == 'click') {
          $("input[id*=quote-button]").click();
        }
      }
      catch (err) {
        alert(Drupal.settings.tilers_discounts.err_msg);
        return;
      }
    }
  });
}

//Processes calculateDiscountResponse from drupal
function tilers_discountsProcessCalculateDiscountResponse(calculateDiscountResponse, context) {
  if (tilers_discountsisUpdating) {
    return;
  }
  tilers_discountsisUpdating = true;

  try {
    var i;

    if (calculateDiscountResponse == null) {
      alert(Drupal.settings.tilers_discounts.err_msg);
      return;
    }

    var line_items = null;
    var errors = null;
    var messages = null;

    try {
      line_items = calculateDiscountResponse[Drupal.settings.tilers_discounts.calculate_discount_response_line_items_key];
      errors = calculateDiscountResponse[Drupal.settings.tilers_discounts.calculate_discount_response_errors_key];
      messages = calculateDiscountResponse[Drupal.settings.tilers_discounts.calculate_discount_response_messages_key];
    }
    catch (err) { }

    //Process discount line items and update total (false to not display messages)
    tilers_discountsRenderLineItems(line_items, true);

    //Add errors and messages to messages container
    var discounts_messages_container = $(".uc-discounts-messages-container", context);
    discounts_messages_container.empty();

    if ( (errors != null) && (errors.length > 0) ) {
      discounts_messages_container.append(  $("<div class='uc-discounts-messages messages error'><" + "/div>").append( errors.join("<br/>") )  );
    }

    if ( (messages != null) && (messages.length > 0) ) {
      var message_list = $("<ul><" + "/ul>");
      for (var i = 0; i < messages.length; i++) {
        message_list.append( $("<li><" + "/li>").append(messages[i]) );
      }
      discounts_messages_container.append( $("<div class='uc-discounts-messages messages'><" + "/div>").append(message_list) );
    }

    tilers_discountsisUpdating = false;
  }
  catch (err) {
    tilers_discountsisUpdating = false;
    throw err;
  }
}

//Updates the discount line items list and updates totals
function tilers_discountsRenderLineItems(line_items, show_message) {
  if ((window.set_line_item == null) || (line_items == null)) {
    return;
  }

  //Remove total discount line item
  remove_line_item(Drupal.settings.tilers_discounts.line_item_key_name);

  var total_amount = 0;
  for (i = 0; i < line_items.length; i++) {
    var line_item = line_items[i];
    total_amount += parseFloat(line_item["amount"]);
  }

  //Add total discount line item
  if (line_items.length > 0) {
    set_line_item(Drupal.settings.tilers_discounts.line_item_key_name,
                  Drupal.settings.tilers_discounts.total_discount_text, total_amount,
                  parseFloat(Drupal.settings.tilers_discounts.line_item_weight) + 0.5,
                  1,
                  false);
  }

  // Update total
  if (window.render_line_items) {
    render_line_items();
  }

  //If there is tax in the system, recalculate tax
  if (window.getTax) {
    getTax();
  }
}
