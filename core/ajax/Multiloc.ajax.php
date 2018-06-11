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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');
    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));

    }


    if (init('action') == 'listImage') {
        $uploaddir = dirname(__FILE__) . '/../../desktop/images';
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir);
        }
        if (!file_exists($uploaddir)) {
            throw new Exception(__("{{Répertoire d'upload d'images non trouvé}} : ", __FILE__) . $uploaddir);
        }
        ajax::success(ls($uploaddir, "*", false, array('files')));
    }

    if (init('action') == 'removeImage') {
        $uploaddir = dirname(__FILE__) . '/../../desktop/images/';
        $name = init('image');
        ajax::success(unlink($uploaddir . $name));
    }

    if (init('action') == 'imageUpload') {
              $uploaddir = dirname(__FILE__) . '/../../desktop/images';
        if (!file_exists($uploaddir)) {
            throw new Exception(__("{{Répertoire d'upload non trouvé}} : ", __FILE__) . $uploaddir);
        }
        if (!isset($_FILES['images'])) {
            throw new Exception(__('{{Aucun fichier trouvé. Vérifié parametre PHP (post size limit}}', __FILE__));
        }
        $extension = strtolower(strrchr($_FILES['images']['name'], '.'));
        if (!in_array($extension, array('.png','.jpg'))) {
            throw new Exception('{{Seul les images sont acceptées (autorisé .jpg .png)}} : ' . $extension);
        }
        if (filesize($_FILES['images']['tmp_name']) > 1000000) {
            throw new Exception(__('{{Le fichier est trop gros}} (maximum 8mo)', __FILE__));
        }
        if (!move_uploaded_file($_FILES['images']['tmp_name'], $uploaddir . '/' . $_FILES['images']['name'])) {
            throw new Exception(__('{{Impossible de déplacer le fichier temporaire}}', __FILE__));
        }
        if (!file_exists($uploaddir . '/' . $_FILES['images']['name'])) {
            throw new Exception(__("{{Impossible d'uploader le fichier (limite du serveur web ?)}}", __FILE__));
        }

        ajax::success();
    }
  	
    
    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));

} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
