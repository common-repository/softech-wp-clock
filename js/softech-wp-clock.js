jQuery(function() 
{
  jQuery('#clock_style').change(function()
  {
  	var select = jQuery("#clock_style option:selected").val();
  	if(select == "Default"){
      jQuery("#text_align").removeClass("hide");
      jQuery("#zone_font_size").removeClass("hide");
      jQuery("#date_font_size").removeClass("hide");
    }
    if(select == "Analog Clock"){
      jQuery("#zone_font_size").addClass("hide");
      jQuery("#date_font_size").addClass("hide");
      jQuery("#text_align").removeClass("hide");
      jQuery("#padding").removeClass("hide");
    }
    else if(select == "Flip Clock"){
      jQuery("#text_align").addClass("hide");
      jQuery("#zone_font_size").addClass("hide");
      jQuery("#date_font_size").addClass("hide");
      jQuery("#padding").addClass("hide");
    }
    else if(select == "digital-design"){
      jQuery("#text_align").addClass("hide");
      jQuery("#zone_font_size").addClass("hide");
      jQuery("#time_font_size").addClass("hide");
      jQuery("#date_font_size").addClass("hide");
      jQuery("#padding").addClass("hide");
    }
  });
  var select = jQuery("#clock_style option:selected").val();
    if(select == "Flip Clock")
    {
      jQuery("#text_align").addClass("hide");
      jQuery("#zone_font_size").addClass("hide");
      jQuery("#date_font_size").addClass("hide");
      jQuery("#padding").addClass("hide");
    }
    if(select == "Analog Clock"){
      jQuery("#zone_font_size").addClass("hide");
      jQuery("#date_font_size").addClass("hide");
    }
});