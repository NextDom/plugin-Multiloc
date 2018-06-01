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


$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});
$("#table_map").sortable({axis: "y", cursor: "move", items: ".map", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

/*
* Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
*/

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }


       var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
        tr += '<td>';
        tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
        tr += '</td>';
        tr += '<td>';
        tr += '<span class="type" type="' + init(_cmd.type) + '">' + jeedom.cmd.availableType() + '</span>';
        tr += '<span class="subType" subType="' + init(_cmd.subType) + '"></span>';
        tr += '</td>';
       	tr += '<td>';
        tr += '<input class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="commande" style="width: 90%;display: inherit" ></input>';
        tr += '</td>';
        tr += '<td style="width: 150px;">';
        tr += '<span><input type="checkbox" class="cmdAttr" data-size="mini" data-l1key="isVisible" checked/> {{Afficher}}<br/></span>';
      	tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span> ';
        tr += '</td>';
        tr += '<td>';
        if (is_numeric(_cmd.id)) {
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
            tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
        }

        tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
        tr += '</td>';
        tr += '</tr>';
        $('#table_cmd tbody').append(tr);
        $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
        if (isset(_cmd.type)) {
            $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
        }
        jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
  
tr='    <div id="map" style="height: 800px"></div>';
tr+='    <script>';
tr+='	var planes = [';
tr+='		["7C6B07",-40.99497,174.50808],';
tr+='		["7C6B38",-41.30269,173.63696],';
tr+='		["7C6CA1",-41.49413,173.5421],';
tr+='		["7C6CA2",-40.98585,174.50659],';
tr+='		["C81D9D",-40.93163,173.81726],';
tr+='		["C82009",-41.5183,174.78081],';
tr+='		["C82081",-41.42079,173.5783],';
tr+='		["C820AB",-42.08414,173.96632],';
tr+='		["C820B6",-41.51285,173.53274]';
tr+='		];';
tr+='        var map = L.map("map").setView([-41.3058, 174.82082], 8);';
tr+='        mapLink = \'<a href="http://openstreetmap.org">OpenStreetMap</a>\';';
tr+='        L.tileLayer(';
tr+='            "http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {';
tr+='            attribution: "&copy; " + mapLink + " Contributors",';
tr+='            maxZoom: 18,';
tr+='            }).addTo(map);';
tr+='		for (var i = 0; i < planes.length; i++) {';
tr+='			marker = new L.marker([planes[i][1],planes[i][2]])';
tr+='				.bindPopup(planes[i][0])';
tr+='				.addTo(map);';
tr+='		}';         
tr+='    </script>';
   	
$('#table_map').append(tr);
    }