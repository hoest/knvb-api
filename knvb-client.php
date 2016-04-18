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

  public function doRequest($url_path = '/teams', $extra = NULL) {
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
      if(property_exists($json_data->List[0], 'Datum') &&
         property_exists($json_data->List[0], 'Tijd') &&
         isset($json_data->List[0]->Datum) &&
         isset($json_data->List[0]->Tijd)) {
        usort($json_data->List, function($a, $b) {
          $dt_a = str_replace('-', '', $a->Datum) . $a->Tijd;
          $dt_b = str_replace('-', '', $b->Datum) . $b->Tijd;

          return strcmp($dt_a, $dt_b);
        });
      }
      else if( is_array( $json_data->List ) && count( $json_data->List ) > 1
               && $json_data->List[0] instanceof stdClass
               && property_exists($json_data->List[0], 'pouleid') && property_exists($json_data->List[0], '0')
      ) {
        $oPoule     = $json_data->List[0]; // $json_data->List[1] is less recent competitionseason
        $arrNewList = array();
        $nIt        = 0;
        while ( property_exists( $oPoule, $nIt ) ) {
          $arrNewList[] = $oPoule->$nIt;
          $nIt ++;
        }
        $json_data->List = $arrNewList;
      }
      return $json_data->List;
    }

    return NULL;
  }

  public function getData($url_path = '/teams', $extra = NULL, $template_file = NULL, $useCache = true) {
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

    if(!isset($template_file) || $template_file == 'template') {
      $template_file = basename($url_path);

      if(strpos($extra, 'slider=1') > -1) {
        // logica voor de slider: 'slider=1'
        $template_file = $template_file.'_slider';
      }
    }

    RainTPL::configure('base_url', NULL);
    RainTPL::configure('tpl_dir', $pluginFolder.'/templates/');
    RainTPL::configure('cache_dir', $pluginFolder.'/cache/');
    RainTPL::configure('path_replace', false);

    $tpl = new RainTPL;

    // standaard 15 minuten cache
    $cache_key = sanitize_file_name($url_path.'_'.$extra);
    if($useCache && $cache = $tpl->cache($template_file,
                                         $expire_time = 900,
                                         $cache_id = $cache_key)) {
      return $cache;
    }
    else {
      $list = $this->doRequest($url_path, $extra);

      $tpl->assign('logo', strpos($extra, 'logo=1') > -1);
      $tpl->assign('thuisonly', strpos($extra, 'thuisonly=1') > -1);
      $tpl->assign('uitonly', strpos($extra, 'uitonly=1') > -1);

      if(isset($list) && strpos($extra, 'thuis=1') > -1) {
        // logica voor thuisclub eerst in overzichten als 'thuis=1' in $extra zit
        if(strpos($extra, 'uitonly=1') === false) {
          $thuis = array_filter($list, function($row) {
            $length = strlen($this->clubName);
            return (isset($row->ThuisClub) && substr($row->ThuisClub, 0, $length) === $this->clubName);
          });

          if(count($thuis) > 0) {
            $tpl->assign('thuis', $thuis);
          }
        }

        if(strpos($extra, 'thuisonly=1') === false) {
          $uit = array_filter($list, function($row) {
            $length = strlen($this->clubName);
            return (isset($row->ThuisClub) && substr($row->UitClub, 0, $length) === $this->clubName);
          });

          if(count($uit) > 0) {
            $tpl->assign('uit', $uit);
          }
        }
      } else {
        $tpl->assign('data', $list);
      }

      return $tpl->draw($template_file, $return_string = true);
    }
  }
}

?>
