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
    'extra' => 'extra'
  ), $atts));

  return '<div class="knvb">'.$client->getData($uri, $extra).'</div>';
}

add_shortcode("knvb", "knvb_shortcode");

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
    $client = new KnvbClient(get_option('knvb_api_key'), get_option('knvb_api_pathname'), get_option('knvb_api_clubname'));
    $knvb_data = trim($client->getData('/teams', false, 'weeknummer=A&thuis=1'));

    if(!empty($knvb_data)) {
      $dt = new DateTime('now');
      $dt->setTimezone(new DateTimeZone('Europe/Amsterdam'));
      echo '<p><em>Vernieuwd op '.$dt->format('d-m-Y \o\m H:i:s').'</em></p>';
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
