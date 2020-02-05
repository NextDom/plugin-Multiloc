<?php
/*
 * This file is part of the NextDom software (https://github.com/NextDom or http://nextdom.github.io).
 * Copyright (c) 2018 NextDom.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 2.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
include_file("desktop", "leaflet", "js", "Multiloc");
include_file("desktop", "leaflet", "css", "Multiloc");
include_file('3rdparty', 'jquery.fileupload/jquery.fileupload', 'js');

$plugin = plugin::byId('Multiloc');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());


?>

<style>
    .divIconSel {
        height: 80px;
        border: 1px solid #fff;
        box-sizing: border-box;
        cursor: pointer;
    }

    .iconSel {
        line-height: 1.4;
        font-size: 1.5em;
    }

    .iconSelected {
        background-color: #563d7c;
        color: white;
    }

    .iconDesc {
        font-size: 0.8em;
    }

    .fileinput-button input {
        display: inline;
        top: 0;
        right: 0;
        margin: -40px;
        opacity: 0;
        -ms-filter: 'alpha(opacity=0)';
        cursor: pointer;
        font-size: 40px !important;
        direction: ltr;
</style>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;"
                   data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter une Multiloc}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm"
                                                                      placeholder="{{Rechercher}}" style="width: 100%"/>
                </li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay"
         style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend><i class="fa fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction" data-action="add"
                 style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
                <i class="fa fa-plus-circle" style="font-size : 6em;color:#94ca02;"></i>
                <br>
                <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02">{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction" data-action="gotoPluginConf"
                 style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
                <i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
                <br>
                <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
            </div>
        </div>
        <legend><i class="fa fa-table"></i> {{Mes cartes}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
                echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
                echo "<br>";
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogic"
         style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i>
            {{Sauvegarder}}</a>
        <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i>
            {{Supprimer}}</a>
        <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i>
            {{Configuration avancée}}</a>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab"
                                       data-action="returnToThumbnailDisplay"><i
                            class="fa fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab"
                                                      data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a>
            </li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i
                            class="fa fa-list-alt"></i> {{Commandes}}</a></li>
            <li role="presentation"><a href="#avatartab" aria-controls="avatar" role="tab" data-toggle="tab"><i
                            class="fa fa-list-alt"></i> {{Avatar}}</a></li>

        </ul>
        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br/>
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Général}}</legend>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                            <div class="col-sm-3">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id"
                                       style="display : none;"/>
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name"
                                       placeholder="{{Nom de l'équipement}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Objet parent}}</label>
                            <div class="col-sm-3">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    foreach (jeeObject::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Catégorie}}</label>
                            <div class="col-sm-9">
                                <?php
                                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                    echo '<label class="checkbox-inline">';
                                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                    echo '</label>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Commentaire}}</label>
                            <div class="col-sm-3">
                                <textarea class="eqLogicAttr form-control" data-l1key="configuration"
                                          data-l2key="commentaire"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-8">
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr"
                                                                      data-l1key="isEnable" checked/>{{Activer}}</label>
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr"
                                                                      data-l1key="isVisible"
                                                                      checked/>{{Visible}}</label>
                            </div>
                        </div>
                    </fieldset>
                    <div class="col-sm-12">
                        <form class="form-horizontal">
                            <fieldset>
                                <legend>{{Paramètres}}</legend>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{distance detection de localisation}}</label>
                                    <div class="col-sm-2">
                                        <input id="dist_loc" type="text" class="eqLogicAttr form-control"
                                               data-l1key="configuration" data-l2key="dist_loc" placeholder="250"
                                               value="250"/>
                                    </div>
                                    <div class="col-sm-2">
                                        metre(s)
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{niveau de zoom par defaut}}</label>
                                    <div class="col-sm-2">
                                        <input id="zoom" type="text" class="eqLogicAttr form-control"
                                               data-l1key="configuration" data-l2key="zoom" value="18"/>
                                    </div>
                                    <div class="col-sm-2">
                                        metre(s)
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{Centrage de la carte}}</label>
                                    <div class="col-sm-3">
                                        <input id="dist_loc" type="text" class="eqLogicAttr form-control"
                                               data-l1key="configuration" data-l2key="map_center" disabled/>

                                    </div>
                                </div>
                            </fieldset>

                    </div>


                </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <a class="btn btn-success btn-sm cmdAction pull-right bt_addlocate" data-action="add"
                       style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{commande}}</a><br/><br/>

                    <thead>
                    <tr>
                        <th>{{Nom}}</th>
                        <th>{{Type}}</th>
                        <th>{{Recherche adresse}}</th>
                        <th>{{Type de loc}}</th>
                        <th>{{Coordonnées GPS}}</th>
                        <th>{{Parametres}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="avatartab">
                <div id="collapseTwo" class="panel-collapse" role="tabpanel" aria-labelledby="headingTwo">

                    <div class="panel-body" id="bsImagesPanel">
                        <div class="col-sm-12" id="bsImagesView" style="min-height: 50px"></div>
                    </div>
                </div>
            </div>


        </div>

    </div>
</div>

<?php include_file('desktop', 'Multiloc', 'js', 'Multiloc'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
