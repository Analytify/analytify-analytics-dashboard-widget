jQuery(document).ready(function($) {

  if(  $("#analytify-dashboard-addon-hide").is(':checked')){
    ajax_request();
  }

  $("#analytify-dashboard-addon-hide").on('click', function(event) {
    if($(this).is(':checked')){
      ajax_request();
    }
  });

  $(".analytify-widget-form").on('submit', function(event) {
    event.preventDefault();
    ajax_request();
  });


  function ajax_request(){

    var  s_date = $("#analytify_start").val();
         s_date = moment(s_date).format("YYYY-MM-DD");

    var en_date = $("#analytify_end").val();
        en_date =  moment(en_date).format("YYYY-MM-DD");

    var  stats_type  = $("#analytify_dashboard_stats_type").val();

    jQuery.ajax({
      url : ajaxurl,
      type : 'post',
      data : {
        action     : 'analytify_dashboard_addon',
        startDate  : s_date,
        endDate    : en_date,
        stats_type : stats_type
      },
      beforeSend : function(){
        $("#inner_analytify_dashboard").addClass('stats_loading');
      },
      success : function( response ) {

        $("#analytify_dashboard").next().remove();
        $(response).insertAfter("#analytify_dashboard");
        $("#inner_analytify_dashboard").removeClass('stats_loading');
        wp_analytify_paginated();
      }
    });
  }
});
