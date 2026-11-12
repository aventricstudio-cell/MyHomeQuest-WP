<?php

/*
 *  create deal hubspot
 *
 *
 *
 *
 */

 function wpestate_create_tickets_hubspot($arguments, $hubspot_api_key, $for, $who)
 {
     if (!wpestate_hubspot_enabled()) {
         return;
     }


     $message = $arguments['details']. "\n\n".esc_html__('Message sent from ', 'wpestate-crm').$arguments['page_source'];
     $post_arguments=array('properties'=>array(
     "hs_pipeline"          => '0',
     "hs_pipeline_stage"    => '1',
     "hs_ticket_priority"   =>  'HIGH',
     "hubspot_owner_id"     =>  '',
     "subject"              =>  $arguments['title'],
     "content"              =>   $message


   ));

     $curl = curl_init();


     curl_setopt_array($curl, array(
     CURLOPT_URL => "https://api.hubapi.com/crm/v3/objects/tickets",
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_ENCODING => "",
     CURLOPT_MAXREDIRS => 10,
     CURLOPT_TIMEOUT => 30,
     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
     CURLOPT_CUSTOMREQUEST => "POST",
     CURLOPT_POSTFIELDS => json_encode($post_arguments),
     CURLOPT_HTTPHEADER => array(
       "accept: application/json",
       "content-type: application/json",
       "Authorization: Bearer " . $hubspot_api_key,
     ),
  ));



     $response = curl_exec($curl);
     $err = curl_error($curl);

     curl_close($curl);

     if ($err) {
         // echo "cURL Error #:" . $err;
     } else {
         $response=json_decode($response);
       
         if (isset($response->id)) {
             return $response->id;
         } else {
             return 0;
         }
     }
 }



/*
 *  create deal hubspot
 *
 *
 *
 *
 */

 function wpestate_create_deal_hubspot($arguments, $hubspot_api_key, $crm_stage, $for, $who)
 {
     if (!wpestate_hubspot_enabled()) {
         return;
     }
     $date  = new DateTime();
     $price = get_post_meta($arguments['propid'], 'property_price', true);
     $post_arguments=array('properties'=>array(
     "amount"             => floatval($price),
      "closedate"          => ($date->getTimestamp()*1000),
     "dealname"           => 'Request from '.$arguments['contact_name'],
     "hubspot_owner_id"   => '',
     "dealstage"          => $crm_stage,
     "pipeline"           => "default"

   ));

     $curl = curl_init();
     curl_setopt_array($curl, array(
     CURLOPT_URL => "https://api.hubapi.com/crm/v3/objects/deals",
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_ENCODING => "",
     CURLOPT_MAXREDIRS => 10,
     CURLOPT_TIMEOUT => 30,
     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
     CURLOPT_CUSTOMREQUEST => "POST",
     CURLOPT_POSTFIELDS => json_encode($post_arguments),
     CURLOPT_HTTPHEADER => array(
       "accept: application/json",
       "content-type: application/json",
        "Authorization: Bearer " . $hubspot_api_key,
     ),
   ));

     $response = curl_exec($curl);
     $err = curl_error($curl);

     curl_close($curl);

     if ($err) {
         // echo "cURL Error #:" . $err;
     } else {
         $response=json_decode($response);
       
         if (isset($response->id)) {
             return $response->id;
         } else {
             return 0;
         }
         print '--------------';
     }
 }




/*
 *  create contact hubspot
 *
 *
 *
 *
 */

function wpestate_create_contact_hubspot($arguments, $lead_author_id, $who)
{
    if (!wpestate_hubspot_enabled()) {
        return;
    }

    $hubspot_api_key_array=wpestate_crm_return_api_hubspot($lead_author_id);
    if ($hubspot_api_key_array['key']=='') {
        return;
    }


    $names= wpestate_crm_split_name($arguments['contact_name']);
    $post_arguments=array('properties'=>array(
    "company"   => "",
    "email"     => $arguments['contact_email'],
    "firstname" => $names[0],
    "lastname"  => $names[1],
    "phone"     => $arguments['contact_mobile'],

  ));


    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.hubapi.com/crm/v3/objects/contacts",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($post_arguments),
    CURLOPT_HTTPHEADER => array(
      "accept: application/json",
      "content-type: application/json",
    "Authorization: Bearer " . $hubspot_api_key_array['key'],
    ),
  ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $response=json_decode($response);
      
        wpestate_crm_handle_response($arguments, $response, $hubspot_api_key_array['key'], $hubspot_api_key_array['stage']);
    }
}

/*
 *  return hubsport api key
 *
 *
 *
 *
 */
function wpestate_crm_handle_response($arguments, $response, $hubspot_api_key, $crm_stage)
{
    if (isset($response->status) && $response->status=='error') {
        if (strpos($response->message, 'Contact already exists. Existing ID:') !== false) {
            $hubspot_id =intval(trim(str_replace('Contact already exists. Existing ID:', '', $response->message)));
        }
    } else {
        if (isset($response->id)) {
            $hubspot_id =$response->id;
        }
    }

    $who='';
    $for='';

    $deal_id  =    wpestate_create_deal_hubspot($arguments, $hubspot_api_key, $crm_stage, $hubspot_id, $who);
    $ticket_id =   wpestate_create_tickets_hubspot($arguments, $hubspot_api_key, $for, $who);

    wpestate_hubspot_crm_associate('contact', 'deal', $hubspot_id, $deal_id, $hubspot_api_key);
    wpestate_hubspot_crm_associate('ticket', 'deal', $ticket_id, $deal_id, $hubspot_api_key);
    wpestate_hubspot_crm_associate('contact', 'ticket', $hubspot_id, $ticket_id, $hubspot_api_key);
}


/*
 *  return hubsport api key
 *
 *
 *
 *
 */
function wpestate_hubspot_crm_associate($fromtype, $totype, $from_id, $to_id, $hubspot_api_key)
{
    $curl = curl_init();
    $typex=$fromtype."_to_".$totype;


    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.hubapi.com/crm/v3/associations/".$fromtype."/".$totype."/batch/create/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"inputs\":[{\"from\":{\"id\":\"".$from_id."\"},\"to\":{\"id\":\"".$to_id."\"},\"type\":\"".$typex."\"}]}",
    CURLOPT_HTTPHEADER => array(
      "accept: application/json",
      "content-type: application/json",
    "Authorization: Bearer " . $hubspot_api_key,
    ),
  ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
       
    }
}


 /*
  *  return hubsport api key
  *
  *
  *
  *
  */


function wpestate_crm_return_api_hubspot($lead_id='')
{
    $lead_id_has_post_id = get_the_author_meta('user_agent_id', $lead_id);
    $post_type = get_post_type($lead_id_has_post_id) ;

    if ($post_type=='estate_agency' || $post_type=='estate_developer') {
        $admin_hub_api_array['key'] =     get_post_meta($lead_id_has_post_id, 'hubspot_api', true) ;
        $admin_hub_api_array['stage'] =   'appointmentscheduled';
        return $admin_hub_api_array;
    }

    // check to see if agent is independen or belongs to agency
    if ($post_type=='estate_agent') {
        $author_sc = intval(get_post_field('post_author', $lead_id_has_post_id));
        // single agent
        if ($author_sc==0) {
            $admin_hub_api_array['key']   =    get_post_meta($lead_id_has_post_id, 'hubspot_api', true) ;
            $admin_hub_api_array['stage'] =   'appointmentscheduled';
            return $admin_hub_api_array;
        }
        $author_sc_id= get_the_author_meta('user_agent_id', $author_sc);
        $post_type_Sc = get_post_type($author_sc_id) ;
        if ($post_type_Sc=='estate_agency' || $post_type_Sc=='estate_developer') {
            $admin_hub_api_array['key']   =      get_post_meta($author_sc_id, 'hubspot_api', true) ;
            $admin_hub_api_array['stage'] =   'appointmentscheduled';
            return $admin_hub_api_array;
        } else {
            $admin_hub_api_array['key']   =     get_post_meta($author_sc_id, 'hubspot_api', true) ;
            $admin_hub_api_array['stage'] =     'appointmentscheduled';
            return $admin_hub_api_array;
        }
    }



    $admin_hub_api_array['key']   =  wpresidence_get_option('wp_estate_hubspot_api', '');
    $admin_hub_api_array['stage'] =    wpresidence_get_option('wp_estate_hubspot_first_stage', '');
    return $admin_hub_api_array;
}

/*
 *  Test hubsport
 *
 *
 *
 *
 */
 function wpestate_hubspot_enabled($who='')
 {
     $enabled= wpresidence_get_option('wp_estate_enable_hubspot_integration', '');
     if ($who=='all') {
         $enabled= wpresidence_get_option('wp_estate_enable_hubspot_integration_for_all', '');
         ;
     }

     if ($enabled=='yes') {
         return true;
     }
     return false;
 }


 function wpestate_crm_split_name($name)
 {
     $name = trim($name);
     $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
     $first_name = trim(preg_replace('#'.preg_quote($last_name, '#').'#', '', $name));
     return array($first_name, $last_name);
 }
