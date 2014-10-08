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

// include the RainTPL class
include "inc/rain.tpl.class.php";

define("BASE_DIR", dirname(__FILE__).DIRECTORY_SEPARATOR);
define("BASE_URI", "http://api.knvbdataservice.nl/api");
define("API_KEY", get_option("knvb_api_key"));

/***********************************************************************
 Initialiseer de API-call en ontvang een SESSION_ID
 */
function knvb_initialize() {
  $init_path = "/initialisatie/".get_option("knvb_api_pathname");

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

/***********************************************************************
 Voer het werkelijke request uit
 */
function knvb_do_api_request($url_path = "/teams", $extra = null) {
  $session_id = knvb_initialize();
  $hash = md5(API_KEY."#".$url_path."#".$session_id);

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

/***********************************************************************
 Ontvang data van de KNVB API
 */
function knvb_get_data($url_path = "/teams", $extra = null) {
  $wn_current = date("W");
  $wn_previous = date("W", strtotime("-7 days"));
  $extra = str_replace(array("weeknummer=C", "weeknummer=P"),
                       array("weeknummer=".$wn_current, "weeknummer=".$wn_previous),
                       $extra);

  raintpl::configure("base_url", null);
  raintpl::configure("tpl_dir", BASE_DIR."templates/");
  raintpl::configure("cache_dir", BASE_DIR."cache/");

  $tpl = new RainTPL;

  // standaard 15 minuten cache
  $cache_key = sanitize_file_name($url_path."_".$extra);
  if($cache = $tpl->cache(basename($url_path),
                          $expire_time = 900,
                          $cache_id = $cache_key)) {
    return $cache;
  }
  else {
    $list = knvb_do_api_request($url_path, $extra);
    $tpl->assign("data", $list);
    return $tpl->draw(basename($url_path), $return_string = true);
  }
}

/***********************************************************************
 Registreer [knvb ...]
 */
function knvb_shortcode($atts) {
  extract(shortcode_atts(array(
    "uri" => "uri",
    "extra" => "extra"
  ), $atts));

  return "<div class=\"knvb\">".knvb_get_data($uri, $extra)."</div>";
}

add_shortcode("knvb", "knvb_shortcode");

/***********************************************************************
 Voeg een optie-scherm toe
 */
function knvb_api_menu() {
  add_options_page("KNVB API Opties",
                   "KNVB API",
                   "manage_options",
                   "knvb-api-menu",
                   "knvb_api_options");
}

/***********************************************************************
 De inhoud van het optie-scherm
 */
function knvb_api_options() {
  ?>

<div class="wrap">
  <h2>KNVB API Opties</h2>

  <form method="post" action="options.php">
    <?php settings_fields('knvb-api-settings-group'); ?>
    <?php do_settings_sections('knvb-api-settings-group'); ?>

    <table class="form-table">
      <tr valign="top">
        <th scope="row">API sleutel</th>
        <td>
          <input type="text" name="knvb_api_key" value="<?php echo esc_attr(get_option('knvb_api_key')); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Pathname</th>
        <td>
          <input type="text" name="knvb_api_pathname" value="<?php echo esc_attr(get_option('knvb_api_pathname')); ?>" />
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>

  <h2>Alle teams</h2>
  <?php
  $knvb_data = trim(knvb_get_data());

  if(!empty($knvb_data)) {
    echo $knvb_data;
  }
  else {
    echo "<p>Zodra de bovenstaande settings correct zijn ingevoerd, verschijnt hier een overzicht van alle teams.</p>";
  }

  ?>
</div>

  <?php
}

/***********************************************************************
 Registreerd de API-settings
 */
function knvb_api_regiter_settings() { // whitelist options
  register_setting('knvb-api-settings-group', 'knvb_api_key');
  register_setting('knvb-api-settings-group', 'knvb_api_pathname');
}

/***********************************************************************
 Admin init functie
 */
if(is_admin()) {
  add_action("admin_menu", "knvb_api_menu");
  add_action("admin_init", "knvb_api_regiter_settings");
}

?>
