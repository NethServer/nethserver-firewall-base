<?php
$view->includeCss('
    .columns {
        margin: 10px;
    }
    .InterIp {
        margin-bottom: 15px;
        margin-left: 10px;
    }
    .FwObjectDesc {
        margin-left: 10px;
        width: 275px;
    }
    .empty {
        margin: 15px;
    }');

echo $view->header()->setAttribute('template', $T('NAT11_Configure_header'));

echo $view->objectsCollection('interfaces')
        ->setAttribute('ifEmpty', function ($view) use ($T) {
         return '<div class="empty">' . \htmlspecialchars($T('no_alias_found'))   . '</div>';
        })
         ->setAttribute('template', function ($view) use ($T) {
                return $view->panel()
                    ->insert($view->textInput('InterIp', $view::LABEL_NONE | $view::STATE_DISABLED | $view::STATE_READONLY ))
                    ->insert($view->textInput('FwObjectDesc', $view::LABEL_NONE))
                    ->insert($view->hidden('FwObjectNat'))                
                ;
    })->setAttribute('key', 'id');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);

$interfacesTarget = $view->getClientEventTarget('interfaces');
$dstHostsDatasource = json_encode($view['DstHosts'], TRUE);
$view->includeJavascript("
jQuery(function ($) {    
    var datasource = ${dstHostsDatasource};  

    var initializePencils = function () {
        $('.TextInput.FwObjectDesc').autocomplete({source: datasource, select: function( event, ui ) {        
             var node = $(this);
             node.next().val(ui.item.value);
             //check = true;
             window.setTimeout(function() { node.val(ui.item.label) }, 0);
        }});
        $('.TextInput.FwObjectDesc').keyup(function(e) {
            if(!$(this).val())
                $(this).next().val('');
        });
        
    }
    initializePencils();
    $('.${interfacesTarget}').on('nethguiupdateview', function () { window.setTimeout(initializePencils,0) });
});
");
