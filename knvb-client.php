<?php

// include the RainTPL class
include "inc/rain.tpl.class.php";

class KnvbClient {
  const BASEURI = 'http://api.knvbdataservice.nl/api';

  public $session_id;

  protected $apiKey;
  protected $apiPath;
  protected $clubName;

  public function __construct($apiKey, $apiPath, $clubName = 'VVZ \'49') {
    $this->apiKey = $apiKey;
    $this->apiPath = $apiPath;
    $this->clubName = $clubName;

    $init_path = '/initialisatie/'.$this->apiPath;

    // initialiseer api request voor sessie-ID
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'HTTP_X_APIKEY' => $this->apiKey,
      'Content-Type' => 'application/json'
    ));
    curl_setopt($ch, CURLOPT_URL, KnvbClient::BASEURI."$init_path");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);

    $json_data = json_decode($result);

    // verzamel benodigde parameters
    $this->session_id = $json_data->List[0]->PHPSESSID;
  }

  public function doRequest($url_path = '/teams', $extra = null) {
    $hash = md5($this->apiKey.'#'.$url_path.'#'.$this->session_id);

    // voer de 'echte' request uit
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'HTTP_X_APIKEY' => $this->apiKey,
      'Content-Type' => 'application/json'
    ));
    curl_setopt($ch,
                CURLOPT_URL,
                KnvbClient::BASEURI.$url_path.'?PHPSESSID='.$this->session_id.'&hash='.$hash.'&'.$extra);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);

    $json_data = json_decode($result);

    if(isset($json_data) && property_exists($json_data, 'List')) {
      return $json_data->List;
    }

    return null;
  }

  public function getData($url_path = '/teams', $extra = null, $useCache = true) {
    $wn_current = ltrim(date('W'), '0');
    $wn_previous = ltrim(date('W', strtotime('-7 days')), '0');
    $wn_next = ltrim(date('W', strtotime('+7 days')), '0');
    $extra = str_replace(array('weeknummer=C',
                               'weeknummer=P',
                               'weeknummer=N'),
                         array('weeknummer='.$wn_current,
                               'weeknummer='.$wn_previous,
                               'weeknummer='.$wn_next),
                         $extra);
    $pluginFolder = dirname(__FILE__);

    RainTPL::configure('base_url', null);
    RainTPL::configure('tpl_dir', $pluginFolder.'/templates/');
    RainTPL::configure('cache_dir', $pluginFolder.'/cache/');

    $tpl = new RainTPL;

    // standaard 15 minuten cache
    $cache_key = sanitize_file_name($url_path.'_'.$extra);
    if($useCache && $cache = $tpl->cache(basename($url_path),
                                         $expire_time = 900,
                                         $cache_id = $cache_key)) {
      return $cache;
    }
    else {
      $list = $this->doRequest($url_path, $extra);

      // logica voor thuisclub eerst in overzichten als 'thuis=1' in $extra zit
      if(isset($list) && strpos($extra, 'thuis=1') !== false) {
        $thuis = array_filter($list, function($row) {
          $length = strlen($this->clubName);
          return (isset($row->ThuisClub) && substr($row->ThuisClub, 0, $length) === $this->clubName);
        });

        $uit = array_filter($list, function($row) {
          $length = strlen($this->clubName);
          return (isset($row->ThuisClub) && substr($row->UitClub, 0, $length) === $this->clubName);
        });

        if(count($thuis) > 0 && count($uit) > 0) {
          $tpl->assign('thuis', $thuis);
          $tpl->assign('uit', $uit);
        }
      } else {
        $tpl->assign('data', $list);
      }

      return $tpl->draw(basename($url_path), $return_string = true);
    }
  }
}

?>
