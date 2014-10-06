<?php
/**
 * Plugin Name: KNVB Api
 * Plugin URI: http://www.hoest.nl
 * Description: A plugin to use the KNVB Data API
 * Version: 1.0
 * Author: Jelle de Jong
 * Author URI: http://www.hoest.nl
 * License: ...
 * */

//include the RainTPL class
include "inc/rain.tpl.class.php";

define("BASE_URI", "http://api.knvbdataservice.nl/api");
define("API_KEY", "...");

function knvb_initialize() {
  $init_path = "/initialisatie/...";

  // initialiseer api request voor sessie-ID
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "HTTP_X_APIKEY" => API_KEY,
    "Content-Type" => "application/json"
  ));
  curl_setopt($ch, CURLOPT_URL, BASE_URI."$init_path");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($ch);
  curl_close($ch);

  $json_data = json_decode($result);

  // verzamel benodigde parameters
  $session_id = $json_data->List[0]->PHPSESSID;

  return $session_id;
}

function knvb_do_api_request($url_path = "/teams", $extra = null) {
  $session_id = knvb_initialize();
  $hash = md5(sprintf("%s#%s#%s", API_KEY, $url_path, $session_id));

  // voer de 'echte' request uit
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "HTTP_X_APIKEY" => API_KEY,
    "Content-Type" => "application/json"
  ));
  curl_setopt($ch,
              CURLOPT_URL,
              BASE_URI."$url_path?PHPSESSID=$session_id&hash=$hash&$extra");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($ch);
  curl_close($ch);

  $json_data = json_decode($result);

  $list = $json_data->List;

  return $list;
}

function knvb_get_data($url_path = "/teams", $extra = null) {
  $list = knvb_do_api_request($url_path, $extra);

  raintpl::configure("base_url", null );
  raintpl::configure("tpl_dir", "templates/" );
  raintpl::configure("cache_dir", "cache/" );

  //initialize a Rain TPL object
  $tpl = new RainTPL;
  $tpl->assign("data", $list);

  return $tpl->draw(basename($url_path), $return_string = true);
}

function knvb_shortcode($atts) {
  extract(shortcode_atts(array(
    "uri" => "uri",
    "extra" => "extra"
  ), $atts));

  return "<div class=\"knvb\">".knvb_get_data($uri, $extra)."</div>";
}

add_shortcode("knvb", "knvb_shortcode");

?>
