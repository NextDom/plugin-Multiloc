<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once 'MultilocCmd.class.php';

class Multiloc extends eqLogic
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /* Fonction exécutée automatiquement toutes les minutes par Jeedom */
    public static function cron() {

        foreach (eqLogic::byType('Multiloc', true) as $eqLogic) {
            $eqLogic->updateInfo();
        }
    }

 public static function cron5() {

$eqLogic->GetCenterFromDegrees();
    }

/*  Fonction exécutée automatiquement toutes les heures par Jeedom */
public static function cronHourly() {
}


/*
* Fonction exécutée automatiquement tous les jours par Jeedom
public static function cronDaily() {

}
*/


/*     * *********************Méthodes d'instance************************* */

public function preInsert()
{

}

public function postInsert()
{

}

public function preSave()
{

}

public function postSave()
{
  
       
    $this->updateInfo();
}



public function preUpdate()
{
if($this->getConfiguration('dist_loc') ==''){
    throw new \Exception(__('distance detection de localisation doit etre renseignée', __FILE__));
  }
if($this->getConfiguration('zoom') ==''){
    throw new \Exception(__('niveau de zoom par defaut doit etre renseigné', __FILE__));
  }
}

public function postUpdate()
{

}

public function preRemove()
{

}

public function postRemove()
{

}

public function updateInfo()
{
    foreach ($this->getCmd('info') as $cmd) {
        $cmd_id =  substr($cmd->getConfiguration('virtEq'),1,-1);
        $cmd_virt = cmd::byId($cmd_id);
        if (is_object($cmd_virt)) {
            $cmd_value = $cmd_virt->execCmd();
            $cmd->setConfiguration('position', $cmd_value);
            $cmd->save();
            $this->updateGeocoding($cmd->getConfiguration('position'), $cmd);

        }
    }
        $this->refreshWidget();
}

  function GetCenterFromDegrees()
{
$data = array();
    foreach ($this->getCmd('info') as $cmd) {
        $data[] = array($cmd->getConfiguration("lat"), $cmd->getConfiguration("lon"));
    }

    if (!is_array($data)) {
      throw new \Exception(__('erreur d\'array', __FILE__));
    }

    $num_coords = count($data);

    $X = 0.0;
    $Y = 0.0;
    $Z = 0.0;

    foreach ($data as $coord)
    {
        $lat = $coord[0] * pi() / 180;
        $lon = $coord[1] * pi() / 180;
        $a = cos($lat) * cos($lon);
        $b = cos($lat) * sin($lon);
        $c = sin($lat);

        $X += $a;
        $Y += $b;
        $Z += $c;
    }

    $X /= $num_coords;
    $Y /= $num_coords;
    $Z /= $num_coords;

    $lon = atan2($Y, $X);
    $hyp = sqrt($X * $X + $Y * $Y);
    $lat = atan2($Z, $hyp);
    $this->setConfiguration('map_center', $lat * 180 / pi(). ',' . $lon * 180 / pi());
    $this->save();
    log::add('Multiloc', 'debug', $lat * 180 / pi(). ',' . $lon * 180 / pi());

}
  

public function updateGeocoding($geoloc, $cmd) {
    if ($geoloc == '' || strrpos($geoloc, ',') === false) {
        log::add('Multiloc', 'debug', 'Format de coordonnées non valide');

    }
    $loc = explode(',',$geoloc);
  	$cmd->setConfiguration('lat', $loc[0]);
  	$cmd->setConfiguration('lon', $loc[1]);
  	$cmd->save();
    if (config::byKey('email', 'Multiloc') == '') {
        log::add('Multiloc', 'debug', 'Vous devez remplir votre email dans la page de configuration');
        return;
    }
    if ($cmd->getConfiguration('reverse')) {
        $url = 'https://nominatim.openstreetmap.org/reverse.php?format=jsonv2&addressdetails=1&lat='.$loc[0].'&lon='.$loc[1].'&email='.config::byKey('email', 'Multiloc') ;
        $data =  file_get_contents($url);
        if (!is_string($data) || !is_array(json_decode($data, true)) || (json_last_error() !== JSON_ERROR_NONE)) {
            log::add('Multiloc', 'debug', 'Erreur  url  ' . $url);
        }
        $jsondata = json_decode($data, true);
    } else {

    }
    $this->updateData($jsondata, $cmd);
}

public function updateData($jsondata, $cmd) {
  	$cmd->setConfiguration('numero', $jsondata['address']['house_number']);
    $cmd->setConfiguration('rue', $jsondata['address']['road']);
    $cmd->setConfiguration('ville', $jsondata['address']['town']);
    $cmd->setConfiguration('codepostale', $jsondata['address']['postcode']);
    $cmd->save();
}
//Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
public function toHtml($_version = 'dashboard') {
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
        return $replace;
    }
    $version = jeedom::versionAlias($_version);
    $replace['#version#'] = $_version;
    if ($this->getDisplay('hideOn' . $version) == 1) {
        return '';
    }


    foreach ($this->getCmd('info') as $cmd) {

if ($cmd->getConfiguration("Typeloc") == "lieu"){
          	if ($cmd->getConfiguration("position") == '' || strrpos($cmd->getConfiguration("position"), ',') == false) {
                log::add('Multiloc', 'debug', 'Erreur: position non conforme pour ' .$cmd->getName());
				$icon = $icon .'';
              	$lieu = $lieu .'';
        }else{
             $icon = $icon . 'var icon'.$cmd->getName() .' = L.icon({iconUrl: "'.$cmd->getConfiguration("icon").'",iconSize:     [40, 40], iconAnchor:   [20, 20]});';	
            $lieu = $lieu . 'L.marker(['. $cmd->getConfiguration("position") .'], {icon: icon'.$cmd->getName() .'}).addTo(map'. $cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'");L.circle(['. $cmd->getConfiguration("position") .'], '.$this->getConfiguration('dist_loc').', {color: "red",fillColor: "#f03",fillOpacity: 0.5}).addTo(map'.$cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'");';
            }
        }else {
        	if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $cmd->getConfiguration("position"))) {
                 log::add('Multiloc', 'debug', 'Erreur: position non conforme pour ' .$cmd->getName());
				 $icon = $icon . '';
              	 $personne = $personne .'';
        	}else{
             $icon = $icon . 'var icon'.$cmd->getName() .' = L.divIcon({html:"<img src=\"'.$cmd->getConfiguration("icon").'\" />",className: "image-icon",iconSize:     [40, 40], iconAnchor:[20, 20]});';
		  	 $personne = $personne .'L.marker(['. $cmd->getConfiguration("position") .'], {icon: icon'.$cmd->getName() .'}).addTo(map'. $cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'"); ';
        	}

        }

        if ($cmd->getIsHistorized() == 1) {
            $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
        }
    }
  	$replace['#lieu#'] = $lieu;
   	$replace['#personne#'] = $personne;
	$replace['#icons#'] =  $icon;
 	$replace['#dist_loc#'] = $this->getConfiguration('dist_loc');
   	$replace['#zoom#'] = $this->getConfiguration('zoom');
  if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $this->getConfiguration('map_center'))){
  	$replace['#map_center#'] = '48.8620645,2.3587779';
  }else{
    $replace['#map_center#'] = $this->getConfiguration('map_center');
  }

    return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'map', 'Multiloc')));

}


/*
* Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
public static function postConfig_<Variable>() {
}
*/

/*
* Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
public static function preConfig_<Variable>() {
}
*/

/*     * **********************Getteur Setteur*************************** */
}
