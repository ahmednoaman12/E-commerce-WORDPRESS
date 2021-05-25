jQuery(function() {

	// Set the default dates
	var startDate = Date.create().addDays(-6).format('{yyyy}-{MM}-{dd}'),	// 7 days ago
	endDate = Date.create().format('{yyyy}-{MM}-{dd}'); 				// today

	// Load chart
	ajaxLoadChart(startDate,endDate);
	
	jQuery(".eh_date_range_picker").css("display", "none");
	
	jQuery('.eh_inptgrp').html('<select name="eh_dropdown" id="eh_dropdown"><option value="'+ Date.create().addDays(-6).format('{yyyy}-{MM}-{dd}')+'"> Last 7 days</option><option value="today">Today</option><option value="'+ Date.create().addDays(-29).format('{yyyy}-{MM}-{dd}')+'">Last 30 days</option><option value="custom_range">Custom range</option></select>');
	
	jQuery('#eh_dropdown').change(function(){
		var start = jQuery(this).val();
		if(start!='custom_range')
		{
			jQuery(".eh_date_range_picker").css("display", "none");
			var end = 'today';
			ajaxLoadChart(start, end);
		}else
		{
			jQuery(".eh_date_range_picker").css("display", "block");
			jQuery('input[name="eh_date_picker"]').daterangepicker({
				opens: 'right'
			}, function(start, end, label) {
				startdate = start.format('YYYY-MM-DD');
				enddate = end.format('YYYY-MM-DD');
				ajaxLoadChart(startdate, enddate);
			});
			
		}
	});
	
	// The tooltip shown over the chart
	var tt = jQuery('<div class="ex-tooltip">').appendTo('body'),
		topOffset = -32;

	var data = {
		"xScale" : "time",
		"yScale" : "linear",
		"main" : [{
			className : ".Stripe",
			"data" : []
		}]
	};

	var opts = {
		paddingLeft : 30,
		paddingTop : 30,
		paddingRight : 0,
        paddingBottom : 30,
		axisPaddingLeft : 25,
		tickHintX: 9, // How many ticks to show horizontally

		dataFormatX : function(x) {
			
			// This turns converts the timestamps coming from
			// ajax.php into a proper JavaScript Date object
			
			return Date.create(x);
		},

		tickFormatX : function(x) {
			
			// Provide formatting for the x-axis tick labels.
			// This uses sugar's format method of the date object. 

			return x.format('{dd} {Mon} {yy}');
		},
		
		"mouseover": function (d, i) {
			var pos = jQuery(this).offset();
			
			tt.text(d.x.format('{Month} {ord}') + ': ' + d.y).css({
				
				top: topOffset + pos.top,
				left: pos.left
				
			}).show();
		},
		
		"mouseout": function (x) {
			tt.hide();
		}
	};

	// Create a new xChart instance, passing the type
	// of chart a data set and the options object
	
	var chart = new xChart('line-dotted', data, '#chart' , opts);
	
	// Function for loading data via AJAX and showing it on the chart
	function ajaxLoadChart(startDate,endDate) {
		
		var page = jQuery("#eh-hidden-graph-hidden-field").val();
		if(page){
			var button = page;
		}else{
			var button = 'Captured';
		}

		// If no data is passed (the chart was cleared)
		
		if(!startDate || !endDate){
			chart.setData({
				"xScale" : "time",
				"yScale" : "linear",
				"main" : [{
					className : ".Stripe",
					data : []
				}]
			});
			
			return;
		}
                jQuery("#analytics .loader").css("display", "block");
		// Otherwise, issue an AJAX request
                jQuery.ajax({
                        type: 'post',
                        url: ajaxurl,
                        data:{
							_wpnonce: jQuery('#_ajax_eh_spg_nonce').val(),
                            action  :   'eh_spg_analytics',
							start   :   startDate,
							end     :	endDate,

                        },
                        success: function(data) {
                           //	console.log(data);
							var total1 = 0;
							var total2 = 0;
							var total3 = 0;
							jQuery.each(JSON.parse(data), function(key,value) { 
								jQuery.each(value, function(key1,value1){
									if(value1.value){
										total1 += value1.value;
									}
									if(value1.value2){
										total2 += value1.value2;
									}
									if(value1.value3){
										total3 += value1.value3;
									}

								});
								
							}); 
							
							$currency = jQuery("#eh_currency_field").val();
							
							jQuery("#eh_capture_total").text( total1.toFixed(2)+' '+$currency );
							jQuery("#eh_uncapture_total").text( total2.toFixed(2)+' '+$currency );
							jQuery("#eh_refunded_total").text( total3.toFixed(2)+' '+$currency );

                            jQuery("#analytics .loader").css("display", "none");
                            var set = [];
                            if(button === 'Captured'){ 
								jQuery.each(JSON.parse(data), function(key,value) { 
									jQuery.each(value, function(key1,value1){
										
											if(value1.value !== undefined){
												set.push({
													x : value1.label,
													y : value1.value,
											});
											}
									});
								});
							}
							else if(button === 'Uncaptured'){

								jQuery.each(JSON.parse(data), function(key,value) { 
									jQuery.each(value, function(key1,value1){
										
											if(value1.value2 !== undefined){
												set.push({
													x : value1.label2,
													y : value1.value2,
											});
											}
									});
								});

							}else if(button === 'succeeded'){

								jQuery.each(JSON.parse(data), function(key,value) { 
									jQuery.each(value, function(key1,value1){
										
											if(value1.value3 !== undefined){
												set.push({
													x : value1.label3,
													y : value1.value3,
											});
											}
									});
								});

							}
                            chart.setData({
                                    "xScale" : "time",
                                    "yScale" : "linear",
                                    "main" : [{
                                            className : ".Stripe",
                                            data : set
                                    }]
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
	}

	jQuery(".eh_graph_button").on('click',function() 
    {   
		var title = jQuery(this).data('title');
		var field = jQuery(this).data('field');

		jQuery("#eh-graph-title").html( title );
		
		jQuery("#eh-hidden-graph-hidden-field").remove();
						
		jQuery(".eh-graph-hidden-field").append( '<input type="hidden" id="eh-hidden-graph-hidden-field" name="eh-hidden-graph-hidden-field" value="'+field+'"/>' );
		
		var startDate = jQuery('#eh_dropdown').val();
		if(startDate === 'custom_range'){
			date = jQuery('input[name="eh_date_picker"]').val().split('-');
			startDate = date[0];
			endDate =  date[1];

		}else{
			var endDate = 'today';
		}
		
		ajaxLoadChart(startDate,endDate);
	});

});
