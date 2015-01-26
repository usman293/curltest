<?php
/**
 * Template Name: Curl test request
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php 
					// $params = array(
					//    "event_phoneapp_api_request" => "123456789",
					//    "get_menus_data" => ""
					// );
					// $params = array(
					//    "event_phoneapp_api_request" => "123456789",
					//    "get_page_content_for_this_id" => 1842
					// );
					// $params = array(
					//    "event_phoneapp_api_request" => "123456789",
					//    "get_events_data" => ""
					// );

					// $params = array(
					//    "union_api_key" => "123456789",
					//    "content_type" => "menus"
					// );

					// $params = array(
					//    "union_api_key" => "123456789",
					//    "content_type" => "page" ,
					//    "content_id"   =>  1842
					// );

					$params = array(
					   "union_api_key" => "123456789",
					   "content_type" => "events"
					);

					$events_data = array();
					$events_data['events'] = array(
							array(
								"id" => 24,
								"title" => "test event",
								"description" => "Lorem ipsum doller imet id.Lorem ipsum doller imet id.Lorem ipsum doller imet id.",
								"event_start" => "2015-01-02 00:00:00",
								"event_end" => "2015-01-31 00:00:00",
								"all_day" => true,
								"location" => "ILWU Dispatch Hall 350 W. 5th Street San Pedro CA US",
								"url" => "http://phone-api.nb/events/test-event/",
								),
							array(
								"id" => 2898,
								"title" => "Grievance Committee Meeting",
								"description" => "Lorem ipsum doller imet id.Lorem ipsum doller imet id.Lorem ipsum doller imet id.",
								"event_start" => "2015-01-04 00:00:00",
								"event_end" => "2015-01-25 00:00:00",
								"all_day" => true,
								"location" => "johar town lahore street 233 g4 lahore pakistan PK",
								"url" => "http://phone-api.nb/events/grievance-committee-meeting-2/",
							)
						);
					// echo '<pre>';
					// print_r($events_data['events']);
					// echo '</pre>';
					//foreach ($events_data['events'] as $key => $value) {
						//echo $value['title'];
					//}
					$event = array(
								"id" => 24,
								"title" => "test event",
								"description" => "Lorem ipsum doller imet id.Lorem ipsum doller imet id.Lorem ipsum doller imet id.",
								"event_start" => "2015-01-02 00:00:00",
								"event_end" => "2015-01-31 00:00:00",
								"all_day" => true,
								"location" => "ILWU Dispatch Hall 350 W. 5th Street San Pedro CA US",
								"url" => "http://phone-api.nb/events/test-event/",
								);
					$objs = (object) $event;
					//$event_page_data = array();
					//$event_page_data['event_and_page_data'] = $events_data;
					//echo '<pre>';
				    //print_r($objs);
				    //echo '</pre>';
					
					$params = array(
					   "union_api_key" 					=> "123456789",
					   "data_from_app" 					=> "event_page",
					);					

					$url = 'http://phone-api.nb/';
					//$url = 'http://ilwu63.prometheuslabor.com';
					

					//$url = 'http://cwa1150.uconnect.dev/';
				    // $ch = curl_init(); 
 
				    // curl_setopt($ch,CURLOPT_URL,$url);
				    // curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
				    // curl_setopt($ch,CURLOPT_HEADER, false);
				    // curl_setopt($ch, CURLOPT_POST, count($params));
				    // curl_setopt($ch, CURLOPT_POSTFIELDS, $params);   
				 
				    // $output=curl_exec($ch);
				 
				    // curl_close($ch);
				    echo '<pre>';
				    //print_r($output);
				    //print_r(json_decode($output));
				    echo '</pre>';


				    $url = 'http://ilwu63.uconnect.dev:3000/events/';
			        $params = array(
			            "wp_key" => "123456789"
			        );                                       
			        $request_to = $url . '?' . http_build_query($params);  
			        
			        //$sc_response = self::curl_get_execution($request_to);

					$curl = curl_init();
			        curl_setopt_array($curl, array(
			            CURLOPT_RETURNTRANSFER => 1,
			            CURLOPT_URL => $request_to,
			        ));
			        $response = curl_exec($curl);
			        curl_close($curl);
			        
			        $output = json_decode($response);

			        echo '<pre>';
				    print_r($output);
				    
				    echo '</pre>';
			?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
