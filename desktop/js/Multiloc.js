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

 $("#table_cmd").delegate(".listEquipementInfo", 'click', function () {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
        calcul.atCaret('insert', result.human);
    });
});

$("#table_cmd").delegate(".listEquipementInfo", 'click', function () {
    var el = $(this);
    jeedom.cmd.getSelectModal({cmd: {type: 'info'}}, function (result) {
        var calcul = el.closest('tr').find('.cmdAttr[data-l1key=configuration][data-l2key=' + el.data('input') + ']');
        calcul.atCaret('insert', result.human);
    });
});

 $("#bt_addlocate").on('click', function (event) {
    var _cmd = {type: 'info', subType: 'string'};
    addCmdToTable(_cmd);
});

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
  				tr += '<div class="row fileupload-buttonbar" style="width : 250px;">';
  					tr += '<div class="col-lg-9" >';
  						tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
        				tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom}}">';
  						tr += '<span class="form-control btn-info fileinput-button">';
   						tr += '<i class="glyphicon glyphicon-plus"></i>';
  						tr += '<span> {{Ajouter avatar}}</span>';
  						tr += '<input class="cmdAttr form-control" type="file" id="bsImagesFileload' + init(_cmd.id) + '" name="images" data-url="plugins/Multiloc/core/ajax/Multiloc.ajax.php?action=imageUpload"/>';
  						tr += ' </span>';
					tr += ' </div>';
  					tr += '  <div class="col-lg-3">';
  						tr += '<img src="'+ _cmd.configuration.icon +'" style="width:auto; height:50px"></a>';
  					tr += ' </div>';

    			tr += '<input class="cmdAttr  form-control input-sm id' + init(_cmd.id) + '" data-l1key="configuration" data-l2key="icon" style="display:none ">';
    				tr += ' </div>';				
  tr += '<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">'; 
        tr += '</td>';

       tr += '<input class="cmdAttr form-control type input-sm" data-l1key="type" value="info"  disabled style="margin-bottom : 5px; display:none" />';
       tr += '<input class="cmdAttr form-control type input-sm" data-l1key="subType" value="string" disabled style="margin-bottom : 5px; display:none" />';

  		tr += '<td>';
		tr += '<div class="form-group">';
        tr += '<div class="col-lg-2">';
        tr += ' <label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="configuration" data-l2key="reverse"></label>';
        tr += ' </div>';
        tr += '</div>';
  		tr += '</td>';
   		tr += '<td>';
  		tr += '<select type="text" id="Typeloc" class="cmdAttr configuration form-control" data-l1key="configuration" data-l2key="Typeloc">';
  		tr += '<option value="lieu">{{lieu}}</option>';
    	tr += '<option value="personne">{{personne}}</option>';
        tr += '</select>';
  		tr += '</td>';
  		tr += '<td><textarea class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="virtEq" style="height : 33px;" placeholder="{{Equipement}}"></textarea>';
       	tr += '<a class="btn btn-default cursor listEquipementInfo btn-sm" data-input="virtEq"><i class="fa fa-list-alt "></i> {{Rechercher équipement}}</a>';      
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
           $('#table_cmd tbody tr:last .cmdAttr[data-l1key=subType]').value(init(_cmd.subType));
           $('#table_cmd tbody tr:last .cmdAttr[data-l1key=configuration][data-l2key=icon]').value('/plugins/Multiloc/desktop/images/defaut.png');

        }
        jeedom.cmd.changeType($('#table_cmd tbody tr:last'), 'string');
  
        $('#bsImagesFileload' + init(_cmd.id)).fileupload({
        dataType: 'json',
        url: "plugins/Multiloc/core/ajax/Multiloc.ajax.php?action=imageUpload",
        dropZone: '#bsImagesPanel' + init(_cmd.id),
        done: function (e, data) {
            if (data.result.state !== 'ok') {
                $('#div_alert').showAlert({message: data.result.result, level: 'danger'});
                return;
            }
		console.log($('.id' + init(_cmd.id) +'.cmdAttr'));
          $('#collapseTwo').collapse('show');
            notify("{{Ajout d'une Image}}", '{{Image ajoutée avec succès}}', 'success');
        }
    });
  
  function updateListImages() {
    $.ajax({
        type: "POST",
        url: "plugins/Multiloc/core/ajax/Multiloc.ajax.php",
        data: {
            action: "listImage"
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state !== 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var images = '';
            imagesWidgets = [];
            data = data.result;
            for (var i in data) {
                images += '<div class="media-left col-sm-2" style="min-width: 105px">';
                images += '<div class="well col-sm-12 noPaddingWell noPaddingLeft noPaddingRight noMarginBottom">';
                images += '<button type="button" class="pull-left btn btn-xs btn-danger bsDelImage" data-image="' + data[i] + "\" title=\"{{Supprimer l'image}}\"><i class='fa fa-trash-o'></i></button>";
                images += '<div class="col-sm-6 noPaddingLeft noPaddingRight text-right pull-right" id="bsViewImageSize' + i + '"></div>';
                images += '</div>';
                images += '<img class="img-thumbnail center-block" src="plugins/Multiloc/desktop/images/' + data[i] + '" alt="' + data[i] + '" title="' + data[i] + '" id="bsViewImage' + i + '">';
                images += '<div class="well col-sm-12 noPaddingLeft noPaddingRight noPaddingWell text-center" id="bsViewImageWH' + i + '"></div>';
                images += '</div>';
                imagesWidgets.push(data[i]);
            }
            $('#bsImagesView'+ init(_cmd.id)).html('<div class="media">' + images + '</div>');
            for (var i in data) {
                addImage(data[i], i);
            }
        }
    });
}

  $( "#Typeloc" ).change(function(){
     
     
  });
function addImage(image, index) {
    var img = new Image();
    img.src = "plugins/Multiloc/desktop/images/" + image + "";
    var xhr = new XMLHttpRequest();
    xhr.open('HEAD', img.src, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var size = Math.round(xhr.getResponseHeader('Content-Length') / 1024);
                $('#bsImagesView' + init(_cmd.id)).find('#bsViewImageSize' + index).append('<strong class="text-right text-nowrap">' + size + 'Ko</strong>');
            }
        }
    };
    xhr.send(null);
  /* img.on('load', function() {
        $('#bsImagesView').find('#bsViewImageWH' + index).append('<strong style="font-size:12px" class="text-nowrap">H: ' + this.width + ' - L:' + this.height + '</strong>');
    });*/
};
}




