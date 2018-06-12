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
        $eqLogic->refreshWidget();
    }



    /*
    * Fonction exécutée automatiquement toutes les heures par Jeedom
    public static function cronHourly() {

}
*/

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

      $cmd = $this->getCmd(null, 'Maison');
		if (!is_object($cmd)) {
			$cmd = new MultilocCmd();
		}
		$cmd->setName(__('Maison', __FILE__));
		$cmd->setEqLogic_id($this->id);
		$cmd->setLogicalId('Maison');
		$cmd->setType('info');
		$cmd->setSubType('string');
      	$cmd->setConfiguration('Typeloc', 'lieu');
  		$cmd->setConfiguration('reverse', '1');
  		$cmd->setConfiguration('icon', '/plugins/Multiloc/desktop/images/house.png');
  		$cmd->setConfiguration('position', '');
		$cmd->save();

  $cmd = $this->getCmd(null, 'personne');
		if (!is_object($cmd)) {
			$cmd = new MultilocCmd();
		}
		$cmd->setName(__('personne', __FILE__));
		$cmd->setEqLogic_id($this->id);
		$cmd->setLogicalId('personne');
		$cmd->setType('info');
		$cmd->setSubType('string');
      	$cmd->setConfiguration('Typeloc', 'personne');
  		$cmd->setConfiguration('reverse', '1');
  		$cmd->setConfiguration('icon', '/plugins/Multiloc/desktop/images/defaut.png');
  		$cmd->setConfiguration('position', '');
		$cmd->save();

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

}

public function updateGeocoding($geoloc, $cmd) {
    log::add('Multiloc', 'debug', 'Coordonnées ' . $geoloc);
    if ($geoloc == '' || strrpos($geoloc, ',') === false) {
        log::add('Multiloc', 'debug', 'Format de coordonnées non valide');

    }
    $loc = explode(',',$geoloc);
    $lat = $loc[0];
    $lon = $loc[1];
    if (config::byKey('email', 'Multiloc') == '') {
        log::add('Multiloc', 'debug', 'Vous devez remplir votre email dans la page de configuration');
        return;
    }
    if ($cmd->getConfiguration('reverse')) {
        $url = 'https://nominatim.openstreetmap.org/reverse.php?format=jsonv2&addressdetails=1&lat='.$lat.'&lon='.$lon.'&&email='.$this->getConfiguration('email') ;
        $request_http = new com_http($url);
        $data = $request_http->exec(30);
        if (!is_string($data) || !is_array(json_decode($data, true)) || (json_last_error() !== JSON_ERROR_NONE)) {
            log::add('Multiloc', 'debug', 'Erreur  ' . $url);
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

        if ($cmd->getConfiguration("Typeloc") == "personne"){
        	if (!preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $cmd->getConfiguration("position"))) {
                 log::add('Multiloc', 'debug', 'Erreur: position non conforme pour ' .$cmd->getName());
				 $icon = '';
        	}else{
             $icon = $icon . 'var icon'.$cmd->getName() .' = L.divIcon({html:"<img src=\"'.$cmd->getConfiguration("icon").'\" />",className: "image-icon",iconSize:     [40, 40], iconAnchor:[20, 20]});';
		  	 $personne = $personne .'L.marker(['. $cmd->getConfiguration("position") .'], {icon: icon'.$cmd->getName() .'}).addTo(map'. $cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'"); ';
        	}
          $replace['#icons#'] = $replace['#icons#'] . $icon;
          $replace['#'.$cmd->getConfiguration("Typeloc").'#'] = $replace['#'.$cmd->getConfiguration("Typeloc").'#'] .  $personne;

        }elseif ($cmd->getConfiguration("Typeloc") == "lieu"){
          	if ($cmd->getConfiguration("position") == '' || strrpos($cmd->getConfiguration("position"), ',') == false) {
                log::add('Multiloc', 'debug', 'Erreur: position non conforme pour ' .$cmd->getName());

        }else{
             $icon = $icon . 'var icon'.$cmd->getName() .' = L.icon({iconUrl: "'.$cmd->getConfiguration("icon").'",iconSize:     [40, 40], iconAnchor:   [20, 20]});';
            }
          $replace['#'.$cmd->getConfiguration("Typeloc").'#'] = $replace['#'.$cmd->getConfiguration("Typeloc").'#'] . 'L.marker(['. $cmd->getConfiguration("position") .'], {icon: icon'.$cmd->getName() .'}).addTo(map'. $cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'");L.circle(['. $cmd->getConfiguration("position") .'], '.$this->getConfiguration('dist_loc').', {color: "red",fillColor: "#f03",fillOpacity: 0.5}).addTo(map'.$cmd->getEqLogic_id().').bindPopup("' .$cmd->getName() .'");';

        }

        $replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();

        if ($cmd->getIsHistorized() == 1) {
            $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
        }
    }
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
