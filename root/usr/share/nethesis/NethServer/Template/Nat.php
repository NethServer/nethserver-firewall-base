<?php
/* @var $view \Nethgui\Renderer\Xhtml */
$view->includeCss('
    .columns {
        margin: 10px;
    }');

echo $view->header()->setAttribute('template', $T('NAT11_Configure_header'));

echo $view->objectsCollection('interfaces')
        ->setAttribute('ifEmpty', function ($view) use ($T) {
         return '<div class="empty">' . \htmlspecialchars($T('no_alias_found'))   . '</div>';
        })
         ->setAttribute('template', function ($view) use ($T) {
                return $view->panel()
                    ->insert($view->textInput('InterIp', $view::LABEL_NONE | $view::STATE_DISABLED | $view::STATE_READONLY ))
                    ->insert($view->textInput('FwObjectDesc', $view::LABEL_NONE)->setAttribute('class', 'pencil'))
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
             window.setTimeout(function() { node.val(ui.item.label) }, 0);
        }});
    }
    initializePencils();
    $('.${interfacesTarget}').on('nethguiupdateview', function () { window.setTimeout(initializePencils,0) });
});
");
