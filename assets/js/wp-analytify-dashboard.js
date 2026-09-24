/* eslint-disable camelcase */
/* eslint-disable vars-on-top */
/* eslint-disable no-mixed-spaces-and-tabs */
/* eslint-disable no-unused-vars */
/* eslint-disable no-console */
jQuery( document ).ready( function ( $ ) {

	// this object will keep track of all the xhr requests
	const xhrRequests = {};

	if ( $( '#analytify-dashboard-addon-hide' ).is( ':checked' ) ) {
		ajax_request( true );
	}

	$( '#analytify-dashboard-addon-hide' ).on( 'click', function ( event ) {
		if ( $( this ).is( ':checked' ) ) {
			ajax_request( true );
		}
	});

	$( '.analytify_form_date' ).on( 'submit', function ( event ) {
		event.preventDefault();

		// if ('inactive' === analytify_dashboard_widget.pro_active || 'false' === analytify_dashboard_widget.pro_updated) {
		// 	return false;
		// }
		// Clear the previous ajax requests before making any new requests.
		abortXHRRequest();
		ajax_request( true );
	});

	/**
	 * This function will abort all the xhr
	 * requests other then the request for
	 * current selected section.
	 */
	function abortXHRRequest () {
		for ( var key in xhrRequests ) {
			if ( xhrRequests.hasOwnProperty( key ) ) {
				xhrRequests[key].abort();
				delete xhrRequests[key];
			}
		}
	}

	/**
	 * Generates realtime box structure.
	 *
	 * @param {object|boolean} values Object of values, false will add a rand number.
	 *
	 * @returns {string}
	 */
	function realtime_box_structure ( values = false ) {
		let markup = ``;
		for ( const key in analytify_dashboard_widget.real_time_labels ) {
			const num = values ? values[key] ? values[key] : 0 : Math.floor( Math.random() * ( 100 - 10 + 1 ) ) + 10;
			markup += `<div class="analytify_${key} analytify_realtime_status_boxes">`;
			markup += `<div class="analytify_general_stats_value" id="pa-${key}">${num}</div>`;
			markup += `<div class="analytify_label">${analytify_dashboard_widget.real_time_labels[key]}</div>`;
			markup += `</div>`;
		}
		return markup;
	}

	/**
	 * Sends the ajax call and generate the view.
	 *
	 * @returns {void}
	 */
	function ajax_request ( clear_contents = true ) {
		document.getElementById( 'analytify_chart_visitor_devices' ).style.display = 'none';
		const stats_type = $( '#analytify_dashboard_stats_type' ).val();

		if ( 'inactive' === analytify_dashboard_widget.pro_active && 'real-time-statistics' === stats_type ) {
			event.stopPropagation();
			return false;
		}

		if ( 'false' === analytify_dashboard_widget.pro_active && 'real-time-statistics' === stats_type ) {
			event.stopPropagation();
			return false;
		}

		const data = {
			sd: $( '#analytify_date_start' ).val(),
			ed: $( '#analytify_date_end' ).val(),
			differ: $( '#analytify_widget_date_differ' ).val()
		};

		if ( 'real-time-statistics' === stats_type ) {
			data.type = 'counter';
		}

		const ajax_url = ( 'real-time-statistics' === stats_type ) ? analytify_dashboard_widget.pro_url + 'real-time' : analytify_dashboard_widget.url + stats_type;

		xhrRequests[stats_type] = jQuery.ajax({
			url: ajax_url,
			type: 'get',
			data: data,
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', analytify_dashboard_widget.nonce );
				if ( clear_contents ) {
					$( '#inner_analytify_dashboard' ).addClass( 'stats_loading' );
					$( '.analytify_widget_return_wrapper' ).html( '' );
					$( '#analytify_chart_visitor_devices' ).html( '' );
					$( '#analytify_chart_visitor_devices' ).removeAttr( '_echarts_instance_' );
				}
			},
			success: function ( response ) {

				$( '#inner_analytify_dashboard' ).removeClass( 'stats_loading' );

				let good_response = 'undefined' !== typeof response.success && response.success;
				let markup = '';

				if ( good_response ) {

					// Generate markup based on 'stats_type'.
					if ( 'general-statistics' === stats_type ) {
						const element = document.getElementById( 'analytify_chart_visitor_devices' );
						if ( element ) {

							// Empty the element
							element.innerHTML = '';

							// Reset width and height
							element.style.removeProperty( 'width' );
							element.style.removeProperty( 'height' );
						}

						function runCode ( $ ) {
							$( '#inner_analytify_dashboard .analytify_stats_loading' ).css(
							  'display',
							  'block'
							);

							const URL =
							  analytify_dashboard_widget.pro_url + 'compare-stats' + '/';
							const __start_date = $( '#analytify_date_start' ).val();
							const __end_date = $( '#analytify_date_end' ).val();
							$.ajax({
							  url: URL,
							  data: {
									sd: __start_date,
									ed: __end_date
							  },
							  beforeSend: function ( xhr ) {
									xhr.setRequestHeader(
								  'X-WP-Nonce',
								  analytify_dashboard_widget.nonce
									);
							  }
							})
							  .fail( function ( data ) {
									var _html =
								  '<table class="analytify_data_tables analytify_no_header_table"><tbody><tr><td class="analytify_td_error_msg"><div class="analytify-stats-error-msg"><div class="wpb-error-box"><span class="blk"><span class="line"></span><span class="dot"></span></span><span class="information-txt">Something Unexpected Occurred.</span></div></div></td></tr></tbody></table>';
									$( '.compare-stats-report' )
								  .html( _html )
								  .parent()
								  .removeClass( 'stats_loading' );
							  })
							  .done( function ( data ) {
									document.getElementById(
								  'analytify_chart_visitor_devices'
									).style.display = 'block';
									$( '#analytify_chart_visitor_devices' ).append( data.body );

									wp_analytify_paginated();
									try {
								  is_three_month = data.stats_data.is_three_month;

										// Initialize after dom ready.
										var years_graph_by_visitors = echarts.init(
											document.getElementById( 'analytify_years_graph_by_visitors' )
										);
										var months_graph_by_visitors = echarts.init(
											document.getElementById( 'analytify_months_graph_by_visitors' )
										);
										var years_graph_by_view = echarts.init(
											document.getElementById( 'analytify_years_graph_by_view' )
										);
										var months_graph_by_view = echarts.init(
											document.getElementById( 'analytify_months_graph_by_view' )
										);

										const comp_graph_type = 'undefined' !== typeof ( analytify_comp_chart_data ) && analytify_comp_chart_data.graph_type ? analytify_comp_chart_data.graph_type : 'line';

										var years_graph_by_visitors_option = {
											options: {
												title: {
													display: false,
													text: 'Overall Activity'
												}
											},
											tooltip: {
												position: function ( p ) {
													if (
														200 >=
											$( '#analytify_years_graph_by_visitors' ).width() - p[0]
													) {
														return [ p[0] - 170, p[1] ];
													}
												},
												formatter: function ( params, ticket, callback ) {
													var year_name = '';
													var seriesName = params.seriesName + '<br />';

													if ( '0' == params.seriesIndex ) {
														if ( '1' == is_three_month ) {
															var s_date = moment( params.name, 'D-MMM-YYYY', true ).format(
																	'MMM DD'
																),
																year_name = moment( s_date, 'MMM DD', true )
																	.add( -1, 'years' )
																	.format( 'D-MMM-YYYY' );
														} else {
															var s_date = moment( params.name, 'MMM-YYYY', true ).format(
																	'MMM YYYY'
																),
																year_name = moment( s_date, 'MMM YYYY', true )
																	.add( -1, 'years' )
																	.format( 'MMM-YYYY' );
														}
													} else {
														year_name = params.name;
													}
													return seriesName + year_name + ' : ' + params.value;
												},
												show: true
											},
											color: [
												data.stats_data.graph_colors.visitors_last_year,
												data.stats_data.graph_colors.visitors_this_year
											],
											legend: {
												data: [
													data.stats_data.visitors_last_year_legend,
													data.stats_data.visitors_this_year_legend
												],
												orient: 'horizontal'
											},
											toolbox: {
												show: true,
												color: [ '#444444', '#444444', '#444444', '#444444' ],
												feature: {
													magicType: {
														show: true,
														type: 'bar' === comp_graph_type ? [ 'bar', 'line' ] : [ 'line', 'bar' ],
														title: {
															line: 'line',
															bar: 'bar'
														}
													},
													restore: { show: true, title: 'Restore' },
													saveAsImage: { show: true, title: 'Save As Image' }
												}
											},
											xAxis: [
												{
													type: 'category',
													boundaryGap: false,
													data: data.stats_data.month_data
												}
											],
											yAxis: [
												{
													type: 'value'
												}
											],
											series: [
												{
													name: data.stats_data.visitors_last_year_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.previous_year_users_data
												},
												{
													name: data.stats_data.visitors_this_year_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.this_year_users_data
												}
											]
										};

										var months_graph_by_visitors_option = {
											tooltip: {
												position: function ( p ) {
													if (
														200 >=
											$( '#analytify_months_graph_by_visitors' ).width() - p[0]
													) {
														return [ p[0] - 170, p[1] ];
													}
												},
												formatter: function ( params, ticket, callback ) {
													var month_name = '';
													if ( '0' == params.seriesIndex && 1 != data.stats_data.this_month_users_data.length ) {
														var s_date = moment( params.name, 'D-MMM', true ).format(
																'MMM DD'
															),
															month_name = moment( s_date, 'MMM DD', true )
																.add( -1, 'months' )
																.format( 'D-MMM' );
													} else {
														month_name = params.name;
													}
													return (
														params.seriesName +
											'<br />' +
											month_name +
											' : ' +
											params.value
													);
												},
												show: true
											},
											color: [
												data.stats_data.graph_colors.visitors_last_month,
												data.stats_data.graph_colors.visitors_this_month
											],
											legend: {
												data: [
													data.stats_data.visitors_last_month_legend,
													data.stats_data.visitors_this_month_legend
												],
												orient: 'horizontal'
											},
											toolbox: {
												show: true,
												color: [ '#444444', '#444444', '#444444', '#444444' ],
												feature: {
													magicType: {
														show: true,
														type: 'bar' === comp_graph_type ? [ 'bar', 'line' ] : [ 'line', 'bar' ],
														title: {
															line: 'line',
															bar: 'bar'
														}
													},
													restore: { show: true, title: 'Restore' },
													saveAsImage: { show: true, title: 'Save As Image' }
												}
											},
											xAxis: [
												{
													type: 'category',
													boundaryGap: false,
													data: data.stats_data.date_data
												}
											],
											yAxis: [
												{
													type: 'value'
												}
											],
											series: [
												{
													name: data.stats_data.visitors_last_month_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.previous_month_users_data
												},
												{
													name: data.stats_data.visitors_this_month_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.this_month_users_data
												}
											]
										};

										var years_graph_by_view_option = {
											tooltip: {
												position: function ( p ) {
													if ( 200 >= $( '#analytify_years_graph_by_view' ).width() - p[0]) {
														return [ p[0] - 170, p[1] ];
													}
												},
												formatter: function ( params, ticket, callback ) {
													var year_name = '';
													var seriesName = params.seriesName + '<br />';

													// if ( is_three_month == '1' ) {
													//     seriesName = 'Views <br />';
													// }
													if ( '0' == params.seriesIndex ) {
														if ( '1' == is_three_month ) {
															var s_date = moment( params.name, 'D-MMM-YYYY', true ).format(
																	'MMM DD'
																),
																year_name = moment( s_date, 'MMM DD', true )
																	.add( -1, 'years' )
																	.format( 'D-MMM-YYYY' );
														} else {
															var s_date = moment( params.name, 'MMM-YYYY', true ).format(
																	'MMM YYYY'
																),
																year_name = moment( s_date, 'MMM YYYY', true )
																	.add( -1, 'years' )
																	.format( 'MMM-YYYY' );
														}
													} else {
														year_name = params.name;
													}
													return seriesName + year_name + ' : ' + params.value;
												},
												show: true
											},
											color: [
												data.stats_data.graph_colors.views_last_year,
												data.stats_data.graph_colors.views_this_year
											],
											legend: {
												data: [
													data.stats_data.views_last_year_legend,
													data.stats_data.views_this_year_legend
												],
												orient: 'horizontal'
											},
											toolbox: {
												show: true,
												color: [ '#444444', '#444444', '#444444', '#444444' ],
												feature: {
													magicType: {
														show: true,
														type: 'bar' === comp_graph_type ? [ 'bar', 'line' ] : [ 'line', 'bar' ],
														title: {
															line: 'Line',
															bar: 'Bar'
														}
													},
													restore: { show: true, title: 'Restore' },
													saveAsImage: { show: true, title: 'Save As Image' }
												}
											},
											xAxis: [
												{
													type: 'category',
													boundaryGap: false,
													data: data.stats_data.month_data
												}
											],
											yAxis: [
												{
													type: 'value'
												}
											],
											series: [
												{
													name: data.stats_data.views_last_year_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.previous_year_views_data
												},
												{
													name: data.stats_data.views_this_year_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.this_year_views_data
												}
											]
										};

										var months_graph_by_view_option = {
											tooltip: {
												position: function ( p ) {
													if (
														200 >=
											$( '#analytify_months_graph_by_visitors' ).width() - p[0]
													) {
														return [ p[0] - 170, p[1] ];
													}
												},
												formatter: function ( params, ticket, callback ) {
													var month_name = '';
													if ( '0' == params.seriesIndex && 1 != data.stats_data.this_month_views_data ) {
														var s_date = moment( params.name, 'D-MMM', true ).format(
																'MMM DD'
															),
															month_name = moment( s_date, 'MMM DD', true )
																.add( -1, 'months' )
																.format( 'D-MMM' );
													} else {
														month_name = params.name;
													}
													return (
														params.seriesName +
											'<br />' +
											month_name +
											' : ' +
											params.value
													);
												},
												show: true
											},
											color: [
												data.stats_data.graph_colors.views_last_month,
												data.stats_data.graph_colors.views_this_month
											],
											legend: {
												data: [
													data.stats_data.views_last_month_legend,
													data.stats_data.views_this_month_legend
												],
												orient: 'horizontal'
											},
											toolbox: {
												show: true,
												color: [ '#444444', '#444444', '#444444', '#444444' ],
												feature: {
													magicType: {
														show: true,
														type: 'bar' === comp_graph_type ? [ 'bar', 'line' ] : [ 'line', 'bar' ],
														title: {
															line: 'Line',
															bar: 'Bar'
														}
													},
													restore: { show: true, title: 'Restore' },
													saveAsImage: { show: true, title: 'Save As Image' }
												}
											},
											xAxis: [
												{
													type: 'category',
													boundaryGap: false,
													data: data.stats_data.date_data
												}
											],
											yAxis: [
												{
													type: 'value'
												}
											],
											series: [
												{
													name: data.stats_data.views_last_month_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.previous_month_views_data
												},
												{
													name: data.stats_data.views_this_month_legend,
													type: comp_graph_type,
													smooth: true,
													itemStyle: {
														normal: {
															areaStyle: {
																type: 'default'
															}
														}
													},
													data: data.stats_data.this_month_views_data
												}
											]
										};

										// Load data into the ECharts instance.
										years_graph_by_visitors.setOption( years_graph_by_visitors_option );
										months_graph_by_visitors.setOption( months_graph_by_visitors_option );
										years_graph_by_view.setOption( years_graph_by_view_option );
										months_graph_by_view.setOption( months_graph_by_view_option );

										window.onresize = function () {
											try {
												years_graph_by_visitors.resize();
												months_graph_by_visitors.resize();
												years_graph_by_view.resize();
												months_graph_by_view.resize();
											} catch ( err ) {
												console.log( err );
											}
										};
									} catch ( err ) {
								  console.log( err );
									}
							  });
						  }
						if ( 'active' === analytify_dashboard_widget.pro_active && analytify_dashboard_widget.graph ) {
							jQuery( document ).ready( function ( $ ) {
								runCode( $ );

								document.addEventListener(
									'analytify_form_date_submitted',
									function ( e ) {
										e.preventDefault();
										if ( analytify_stats_pro.load_via_ajax ) {
											runCode( $ );
										}
									}
								);
							});
						}
						let boxes_markup = '';

						// Generate boxes.
						for ( const box_key in response.boxes ) {

							const box = response.boxes[box_key];

							boxes_markup += `<div class="analytify_general_status_boxes">
								<div class="title-wrapper">
									<div class="title-inner-wrapper">
										<h4>${box.title}</h4>
										${box.info ? `<div class="info-box"><span class="info-icon">?</span><p>${box.info}</p></div>` : ``}
									</div>
								</div>
								<div class="analytify_general_stats_value">${box.prepend ? box.prepend : ''}${box.number}${box.append ? box.append : ''}</div>
							</div>`;
						}

						if ( '' !== boxes_markup ) {
							markup += `<div class="analytify_status_header"><h3>${response.title}</h3>
							<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a>
							</div>
							<div class="analytify_status_body">${boxes_markup}</div>
							<div class="analytify_status_footer"><span class="analytify_info_stats">${response.bottom_info}</span></div>`;
						}

					} else if ( 'top-pages-by-views' === stats_type || 'top-posts-by-views' === stats_type || 'top-countries' === stats_type || 'top-cities' === stats_type || 'keywords' === stats_type || 'social-media' === stats_type || 'top-reffers' === stats_type ) {
						let table_rows = '';
						let table_row_num = 1;

						// Generate table rows (excluding THs).
						for ( const row_id in response.stats.data ) {
							table_rows += `<tr>
								<td class="analytify_txt_center">${table_row_num}</td>
								<td>${response.stats.data[row_id][0]}</td>
								<td class="analytify_txt_center">${response.stats.data[row_id][1]}</td>
							</tr>`;
							table_row_num++;
						}

						if ( '' !== table_rows && 'top-cities' === stats_type ) {

							const citiesPerPage = analytify_dashboard_widget.top_cities_per_page !== undefined ? analytify_dashboard_widget.top_cities_per_page : false;

							markup += `<div class="analytify_status_header"><h3>${response.title}</h3>
							<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a></div>
							<div class="analytify_status_body">
								<table class="analytify_data_tables wp_analytify_paginated" ${citiesPerPage && 'data-product-per-page=' + citiesPerPage }>
									<thead>
										<tr>
											<th class="analytify_num_row">#</th>
											<th class="analytify_txt_left">${response.stats.head[0]}</th>
											<th class="analytify_value_row">${response.stats.head[1]}</th>
										</tr>
									</thead>
									<tbody>${table_rows}</tbody>
								</table>
							</div>`;

							if ( response.bottom_info ) {
								markup += `<div class="analytify_status_footer"><div class="wp_analytify_pagination"></div><span class="analytify_info_stats">${response.bottom_info}</span></div>`;
							}

						} else if ( '' !== table_rows && 'top-pages-by-views' === stats_type ) {

							const pages_by_views = analytify_dashboard_widget.top_pages_by_views_filter !== undefined ? analytify_dashboard_widget.top_pages_by_views_filter : false;

							markup += `<div class="analytify_status_header"><h3>${response.title}</h3>
							<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a></div>
							<div class="analytify_status_body">
								<table class="analytify_data_tables wp_analytify_paginated" ${pages_by_views && 'data-product-per-page=' + pages_by_views }>
									<thead>
										<tr>
											<th class="analytify_num_row">#</th>
											<th class="analytify_txt_left">${response.stats.head[0]}</th>
											<th class="analytify_value_row">${response.stats.head[1]}</th>
										</tr>
									</thead>
									<tbody>${table_rows}</tbody>
								</table>
							</div>`;

							if ( response.bottom_info ) {
								markup += `<div class="analytify_status_footer"><div class="wp_analytify_pagination"></div><span class="analytify_info_stats">${response.bottom_info}</span></div>`;
							}

						} else if ( '' !== table_rows && 'top-posts-by-views' === stats_type ) {

							const posts_by_views = analytify_dashboard_widget.top_posts_by_views_filter !== undefined ? analytify_dashboard_widget.top_posts_by_views_filter : false;

							markup += `<div class="analytify_status_header"><h3>${response.title}</h3>
							<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a></div>
							<div class="analytify_status_body">
								<table class="analytify_data_tables wp_analytify_paginated" ${posts_by_views && 'data-product-per-page=' + posts_by_views }>
									<thead>
										<tr>
											<th class="analytify_num_row">#</th>
											<th class="analytify_txt_left">${response.stats.head[0]}</th>
											<th class="analytify_value_row">${response.stats.head[1]}</th>
										</tr>
									</thead>
									<tbody>${table_rows}</tbody>
								</table>
							</div>`;

							if ( response.bottom_info ) {
								markup += `<div class="analytify_status_footer"><div class="wp_analytify_pagination"></div><span class="analytify_info_stats">${response.bottom_info}</span></div>`;
							}

						} else if ( '' !== table_rows && 'top-countries' === stats_type ) {

							const countriesPerPage = analytify_dashboard_widget.top_countries_filter !== undefined ? analytify_dashboard_widget.top_countries_filter : false;

							markup += `<div class="analytify_status_header"><h3>${response.title}</h3>
							<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a>
							</div>
							<div class="analytify_status_body">
								<table class="analytify_data_tables wp_analytify_paginated" ${countriesPerPage && 'data-product-per-page=' + countriesPerPage }>
									<thead>
										<tr>
											<th class="analytify_num_row">#</th>
											<th class="analytify_txt_left">${response.stats.head[0]}</th>
											<th class="analytify_value_row">${response.stats.head[1]}</th>
										</tr>
									</thead>
									<tbody>${table_rows}</tbody>
								</table>
							</div>`;

							if ( response.bottom_info ) {
								markup += `<div class="analytify_status_footer"><div class="wp_analytify_pagination"></div><span class="analytify_info_stats">${response.bottom_info}</span></div>`;
							}

						} else if ( '' !== table_rows && 'keywords' === stats_type ) {

							const keywordsPerPage = analytify_dashboard_widget.top_keywords_filter !== undefined ? analytify_dashboard_widget.top_keywords_filter : false;

							markup += `<div class="analytify_status_header"><h3>${response.title}</h3>
							<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a></div>
							<div class="analytify_status_body">
								<table class="analytify_data_tables wp_analytify_paginated" ${keywordsPerPage && 'data-product-per-page=' + keywordsPerPage }>
									<thead>
										<tr>
											<th class="analytify_num_row">#</th>
											<th class="analytify_txt_left">${response.stats.head[0]}</th>
											<th class="analytify_value_row">${response.stats.head[1]}</th>
										</tr>
									</thead>
									<tbody>${table_rows}</tbody>
								</table>
							</div>`;

							if ( response.bottom_info ) {
								markup += `<div class="analytify_status_footer"><div class="wp_analytify_pagination"></div><span class="analytify_info_stats">${response.bottom_info}</span></div>`;
							}


						} else if ( '' !== table_rows && 'top-reffers' === stats_type ) {

							const reffersPerPage = analytify_dashboard_widget.top_refferals_filter !== undefined ? analytify_dashboard_widget.top_refferals_filter : false;

							markup += `<div class="analytify_status_header"><h3>${response.title}</h3>
							<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a></div>
							<div class="analytify_status_body">
								<table class="analytify_data_tables wp_analytify_paginated" ${reffersPerPage && 'data-product-per-page=' + reffersPerPage }>
									<thead>
										<tr>
											<th class="analytify_num_row">#</th>
											<th class="analytify_txt_left">${response.stats.head[0]}</th>
											<th class="analytify_value_row">${response.stats.head[1]}</th>
										</tr>
									</thead>
									<tbody>${table_rows}</tbody>
								</table>
							</div>`;

							if ( response.bottom_info ) {
								markup += `<div class="analytify_status_footer"><div class="wp_analytify_pagination"></div><span class="analytify_info_stats">${response.bottom_info}</span></div>`;
							}

						} else {
							markup = `<div class="analytify-stats-error-msg wpanalytify">
								<div class="wpb-error-box">
									<span class="blk"><span class="line"></span><span class="dot"></span></span>
									<span class="information-txt">${analytify_dashboard_widget.empty_message}</span>
								</div>
							</div>`;
						}


					} else if ( 'real-time-statistics' === stats_type ) {

						markup = `<div class="analytify_status_header"><h3>${response.title}</h3>
						<a href="/wp-admin/admin.php?page=analytify-dashboard&show=detail-realtime" title="View Dashboard"><span class="dashicons dashicons-chart-bar"></span></a></div>`;
						markup += `<div class="analytify_status_body"><div class="analytify_general_status_boxes_wraper analytify_real_time_stats_widget">${realtime_box_structure( response.counter )}</div></div>`;
					} else if ( 'visitors-devices' === stats_type ) {
						document.getElementById(
						  'analytify_chart_visitor_devices'
						).style.display = 'block';
						let total_devices = 0;
						const device_visitors_box = response.stats.data.visitor_devices;
						const chart_colors = response.stats.data.visitor_devices.colors;

						// Calculate total devices
						total_devices =
						  parseInt( device_visitors_box.stats.mobile.number ) +
						  parseInt( device_visitors_box.stats.tablet.number ) +
						  parseInt( device_visitors_box.stats.desktop.number );

						if ( $( '#analytify_chart_visitor_devices' ).length ) {
							  const setting_title = 'Devices of Visitors';
							  const setting_stats = device_visitors_box.stats;
							  const setting_colors = chart_colors;

							  if ( 0 < total_devices ) {
								const container = document.getElementById(
								  'analytify_chart_visitor_devices'
								);

								if ( ! container ) {
								  console.error( 'Chart container element not found.' );
								  return;
								}

								// Set dimensions dynamically
								container.style.width = '100%'; // Set width relative to parent or viewport
								container.style.height = '300px'; // Set height in pixels

								const user_device_graph_options = {
									color: setting_colors,
									legend: {
										x: 'center',
										y: 'bottom',
										bottom: '5%',
										textStyle: { fontSize: 14, fontWeight: '500' },
										formatter: function ( name ) {
											let value = setting_stats[name.toLowerCase()].number;
											if ( 1000 <= value ) {
												value = ( value / 1000 ).toFixed( 1 ) + 'k';
											}
											return `${name}: ${value}`;
										},
										data: [ setting_stats.mobile.label, setting_stats.tablet.label, setting_stats.desktop.label ]
									},
									series: [
										{
											name: setting_title,
											type: 'pie',
											smooth: true,
											center: [ '50%', '43%' ], // Center the chart
											label: {
												show: false
											},
											labelLine: {
												show: false
											},
											data: [
												{ name: setting_stats.mobile.label, value: setting_stats.mobile.number },
												{ name: setting_stats.tablet.label, value: setting_stats.tablet.number },
												{ name: setting_stats.desktop.label, value: setting_stats.desktop.number }
											]
										}
									]
								};

								const user_device_graph = echarts.init( document.getElementById( 'analytify_chart_visitor_devices' ) );
								user_device_graph.setOption( user_device_graph_options );

								window.onresize = function () {
									try {
										user_device_graph.resize();
									} catch ( err ) {
										console.log( err );
									}
								};
								if ( ! $( '#analytify_chart_visitor_devices .analytify_chart_container' ).length ) {
									const linkMarkup = `<div class="analytify_chart_container">
										<div class="analytify_status_header">
											<h3>Visitors Devices</h3>
											<a href="/wp-admin/admin.php?page=analytify-dashboard" title="View Dashboard">
												<span class="dashicons dashicons-chart-bar"></span>
											</a>
										</div>
									</div>`;
									$( '#analytify_chart_visitor_devices' ).css( 'position', 'relative' ).prepend( linkMarkup );
								}
							  } else {

								$( '#analytify_chart_visitor_devices' ).html(
								  `<div class="analytify-stats-error-msg wpanalytify">
										  <div class="wpb-error-box">
											  <span class="blk"><span class="line"></span><span class="dot"></span></span>
											  <span class="information-txt">${analytify_dashboard_widget.empty_message}</span>
										  </div>
									  </div>`
								);
								markup = `<div class="analytify-stats-error-msg wpanalytify">
													  <div class="wpb-error-box">
														  <span class="blk"><span class="line"></span><span class="dot"></span></span>
														  <span class="information-txt">${analytify_dashboard_widget.empty_message}</span>
													  </div>
												  </div>`;
							  }
						}
					  }
				} else if ( response.message ) {
					markup = `<div class="analytify-stats-error-msg wpanalytify">
						<div class="wpb-error-box">
							<span class="blk"><span class="line"></span><span class="dot"></span></span>
							<span class="information-txt">${response.message}</span>
						</div>
					</div>`;
				}

				$( '.analytify_widget_return_wrapper' ).html( markup );

				// Call pagination from the core.
				if ( good_response && response.pagination ) {
					wp_analytify_paginated();
				}

				// Only enable if wp-analytify-pro plugin is installed and function is available
				if ( 'undefined' !== typeof wpAnalytifyPro && 'function' === typeof enableAnalytifyTableSorting ) {
					enableAnalytifyTableSorting();
				}

				$( window ).trigger( 'resize' );

			}
		});
	}

	if ( 'inactive' === analytify_dashboard_widget.pro_active || 'false' === analytify_dashboard_widget.pro_updated ) {
		$( document ).on( 'change', '#analytify_dashboard_stats_type', function ( e ) {
			if ( 'real-time-statistics' === $( this ).val() ) {
				$( '#analytify-dashboard-addon .analytify_widget_return_wrapper' ).hide();
				let markup = '';

				markup += `<div class="analytify-dashboard-promo">
					${realtime_box_structure( false )}
					${analytify_dashboard_widget.real_time_pro_message}
				</div>`;

				$( markup ).insertAfter( '#analytify-dashboard-addon .analytify_widget_return_wrapper' );

			} else {
				$( '#analytify-dashboard-addon .analytify_widget_return_wrapper' ).show();
				$( '.analytify-dashboard-promo' ).remove();
			}
			$( window ).trigger( 'resize' );
		});
	}

	if ( 'active' === analytify_dashboard_widget.pro_active && 'true' === analytify_dashboard_widget.pro_updated ) {
		setInterval( () => {
			if (
				'real-time-statistics' === $( '#analytify_dashboard_stats_type' ).val()				&&
				$( '#analytify-dashboard-addon-hide' ).is( ':checked' )
			) {

				// Clear the previous ajax requests before making any new requests.
				abortXHRRequest();
				ajax_request( false );
			}
		}, 30000 );
	}

});

// Hide duplicate 'No activity during this period.' messages, keep only the first
jQuery( document ).ready( function ( $ ) {
	var $msgs = $( '.analytify-stats-error-msg .information-txt:contains(\'No activity during this period.\')' );
	if ( 1 < $msgs.length ) {
		$msgs.parent().parent().slice( 1 ).hide();
	}
});

function analytify_hide_no_activity_stats () {
	var selected = jQuery( '#analytify_dashboard_stats_type' ).val();
	if ( 'visitors-devices' === selected ) {

		// Hide the entire general stats section if there is no data
		var $noActivity = jQuery( '.analytify_widget_return_wrapper .analytify-stats-error-msg .information-txt:contains("No activity during this period.")' );
		if ( $noActivity.length ) {
			jQuery( '.analytify_general_status.analytify_status_box_wraper.analytify_widget_return_wrapper' ).hide();
		}

		// Show the one in the visitors devices section (if hidden)
		jQuery( '#analytify_chart_visitor_devices .analytify-stats-error-msg' ).show();
	} else {

		// Show the general stats section
		jQuery( '.analytify_general_status.analytify_status_box_wraper.analytify_widget_return_wrapper' ).show();

		// Show all messages (reset)
		jQuery( '.analytify-stats-error-msg' ).show();
	}
}

// Run after AJAX loads new data
jQuery( document ).ajaxComplete( function () {
	analytify_hide_no_activity_stats();
});

// Also run when the dropdown changes
jQuery( document ).on( 'submit', '#analytify-dashboard-addon .analytify_form_date', function () {
	setTimeout( analytify_hide_no_activity_stats, 200 ); // slight delay for AJAX
});
