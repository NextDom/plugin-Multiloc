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
      		$personne = $this->getCmd(null, 'personne');
		if (!is_object($personne)) {
			$personne = new Multiloc();
			$personne->setTemplate('dashboard', 'tile');
			$personne->setTemplate('mobile', 'tile');
		}
		$personne->setName(__('personne', __FILE__));
		$personne->setEqLogic_id($this->id);
		$personne->setLogicalId('personne');
		$personne->setType('info');
		$personne->setSubType('string');
      	$personne->setConfiguration('modes', 'personne');
		$personne->save();     
	}
		
  

    public function preUpdate()
    {

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
            $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
            $replace['#' . $cmd->getLogicalId() . '#'] = $cmd->getConfiguration("commande");
            $replace['#' . $cmd->getLogicalId() . '_collect#'] = $cmd->getCollectDate();

            if ($cmd->getIsHistorized() == 1) {
                $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
            }
            $replace['#' . $cmd->getLogicalId() . '_id_display#'] = ($cmd->getIsVisible()) ? '#' . $cmd->getLogicalId() . "_id_display#" : "none";
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
