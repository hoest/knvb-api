<?php
/**
 * Plugin Name: KNVB Api
 * Plugin URI: http://www.hoest.nl
 * Description: A plugin to use the KNVB Data API
 * Version: 1.5
 * Author: Jelle de Jong
 * Author URI: http://www.hoest.nl
 * */

// include the KnvbClient class
include 'knvb-client.php';

/***********************************************************************
 Registreer [knvb ...]
 */
function knvb_shortcode($atts) {
  $client = new KnvbClient(get_option('knvb_api_key'),
                           get_option('knvb_api_pathname'),
                           get_option('knvb_api_clubname'));

  extract(shortcode_atts(array(
    'uri' => 'uri',
    'extra' => 'extra',
  ), $atts));

  return '<div class="knvb">'.$client->getData($uri, $extra).'</div>';
}

add_shortcode("knvb", "knvb_shortcode");

/***********************************************************************
 Registreer [knvbteam ...]
 */
function knvbteam_shortcode($atts) {
  $client = new KnvbClient(get_option('knvb_api_key'),
                           get_option('knvb_api_pathname'),
                           get_option('knvb_api_clubname'));

  extract(shortcode_atts(array(
    'list' => 'list',
  ), $atts));

  $output = '';
  if(isset($list) && count(explode(';', $list)) > 0) {
    foreach(explode(';', $list) as $teamId) {
      $output = $output.'<div class="team">';
      $output = $output.'<div class="team-results">'.$client->getData('/teams/'.$teamId.'/results', 'weeknummer=A').'</div>';
      $output = $output.'<div class="team-ranking">'.$client->getData('/teams/'.$teamId.'/ranking').'</div>';
      $output = $output.'<div class="team-schedule">'.$client->getData('/teams/'.$teamId.'/schedule', 'weeknummer=A').'</div>';
      $output = $output.'</div>';
    }
  }

  return '<div class="knvbteam">'.$output.'</div>';
}

add_shortcode("knvbteam", "knvbteam_shortcode");

/***********************************************************************
 Registreer [knvbteam-slider ...]
 */
function knvbteam_slider_shortcode($atts) {
  $client = new KnvbClient(get_option('knvb_api_key'),
                           get_option('knvb_api_pathname'),
                           get_option('knvb_api_clubname'));

  extract(shortcode_atts(array(
    'id' => 'id',
    'extra' => 'extra',
  ), $atts));

  $output = '';
  if(isset($id)) {
    $output = $output.$client->getData('/teams/'.$id.'/schedule', 'weeknummer=C&slider=1&'.$extra);
    $output = $output.$client->getData('/teams/'.$id.'/results', 'weeknummer=A&slider=1&'.$extra);
  }

  return '<div class="knvbteam-slider">'.$output.'</div>';
}

add_shortcode("knvbteam-slider", "knvbteam_slider_shortcode");

/***********************************************************************
 Voeg een optie-scherm toe
 */
function knvb_api_menu() {
  add_options_page('KNVB API Opties',
                   'KNVB API',
                   'manage_options',
                   'knvb-api-menu',
                   'knvb_api_options');
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

      <tr valign="top">
        <th scope="row">Clubnaam (volgens KNVB-site)</th>
        <td>
          <input type="text" name="knvb_api_clubname" value="<?php echo esc_attr(get_option('knvb_api_clubname')); ?>" />
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>

  <h2>Alle teams</h2>
  <?php
    $cache_folder = plugin_dir_path(__FILE__).'cache/';

    // create cache folder
    if (!file_exists($cache_folder)) {
      mkdir($cache_folder, 0777);
    }

    // emtpy cache folder
    $files = glob($cache_folder.'*.rtpl.php'); // get all file names
    foreach($files as $file) { // iterate files
      if(is_file($file)) {
        unlink($file); // delete file
        echo '<p>Cache removed: <code>'.$file.'</code></p>';
      }
    }

    // create a client and receive data
    $client = new KnvbClient(get_option('knvb_api_key'), get_option('knvb_api_pathname'), get_option('knvb_api_clubname'));
    $knvb_data = trim($client->getData('/teams', null, false));

    if(!empty($knvb_data)) {
      $dt = new DateTime('now');
      $dt->setTimezone(new DateTimeZone('Europe/Amsterdam'));
      echo '<p><em>Cache geleegd en data vernieuwd op '.$dt->format('d-m-Y \o\m H:i:s').'</em></p>';
      echo $knvb_data;
    }
    else {
      echo '<p>Zodra de bovenstaande settings correct zijn ingevoerd, verschijnt hier een overzicht van alle teams.</p>';
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
  register_setting('knvb-api-settings-group', 'knvb_api_clubname');
}

/***********************************************************************
 Admin init functie
 */
if(is_admin()) {
  add_action('admin_menu', 'knvb_api_menu');
  add_action('admin_init', 'knvb_api_regiter_settings');
}

?>
