<?php
/* @var $view \Nethgui\Renderer\Xhtml */

echo $view->header()->setAttribute('template', $T('Index_header'));

echo $view->buttonList()
    //->insert($view->button('ApplySelection', $view::BUTTON_SUBMIT))    
    ->insert($view->buttonList()
        ->setAttribute('class', 'Buttonset v1 inlineblock')
        ->insert($view->button('Create_last', $view::BUTTON_LINK))
        ->insert($view->button('Create_first', $view::BUTTON_LINK))
        ->insert($view->button('Configure', $view::BUTTON_LINK)->setAttribute('value', $view->getModuleUrl('../General')))
    )
    ->insert($view->button('Commit', $view::BUTTON_SUBMIT)->setAttribute('receiver', 'Commit'))
    ->insert($view->button('Help', $view::BUTTON_HELP))
;

// 'groups' contains an array of views..
echo $view->objectsCollection('Rules')
    ->setAttribute('key', 'id')
    ->setAttribute('ifEmpty', function ($view) use ($T) {
        return $T('NoRulesDefined_label');
    })
    ->setAttribute('template', function ($view) use ($T) {
        return $view->panel()
            ->setAttribute('class', 'fwrule')
            //->insert($view->checkbox('selected', '1', $view::LABEL_NONE)->setAttribute('uncheckedValue', FALSE))
            ->insert($view->hidden('metadata', $view::STATE_DISABLED))
            ->insert($view->textInput('Position', $view::LABEL_NONE))
            ->insert($view->panel()->setAttribute('class', 'actbox')                
                ->insert($view->textLabel('id')->setAttribute('tag', 'div')->setAttribute('template', $T("RuleId_label")))

                ->insert($view->textLabel('Action')->setAttribute('tag', 'div'))
                )
            ->insert($view->panel()->setAttribute('class', 'descbox')
                ->insert($view->textLabel('RuleText')->setAttribute('tag', 'div'))
                ->insert($view->textLabel('Description')->setAttribute('tag', 'div')))
            ->insert($view->buttonList()->setAttribute('class', 'Buttonset v1')
                ->insert($view->button('Edit', $view::BUTTON_LINK))
                ->insert($view->button('Delete', $view::BUTTON_LINK))
            )
        ;
    });

echo $view->hidden('hasChanges');

$rulesId = $view->getUniqueId('Rules');
$actionId = $view->getUniqueId();
$commitId = $view->getUniqueId('Commit');
$deleteId = $view->getUniqueId('Delete');
$deleteUrl = $view->getModuleUrl('../Delete');
$hasChangesTarget = $view->getClientEventTarget('hasChanges');

$ruleStep = \NethServer\Module\FirewallRules::RULESTEP;

$view->includeCss('
.fwrule {min-height: 50px; border:1px solid #d3d3d3; cursor: move; display: flex; margin-bottom: 1.5em; border-radius: 3px; background: linear-gradient(to bottom, #e6e6e6 0%, #fff 100%);}
.fwrule .Buttonset {flex-grow: 0; margin-right: 0}
.fwrule .Buttonset [role=button] {border-top: none}
.fwrule .actbox {padding: 3px; min-width: 6em; font-size: 120%; background: white; text-transform: uppercase;}
.fwrule .descbox {flex-grow: 8; border-left: 1px solid #d3d3d3; padding: 3px}
.placeholder {background-color: yellow; margin-bottom: 1.5em; background: linear-gradient(to bottom, rgba(234,239,181,1) 0%,rgba(225,233,160,1) 100%);}
.fwrule .id {color: #bbb; font-size: 0.7em}
.Position {width: 2em}
');

$view->includeJavascript("
jQuery(function ($) {
    $(window).on('unload beforeunload', function(e) {
       if($('input.${hasChangesTarget}').val() == '1') {
            return false;
       }
    });

    $('#${rulesId}').sortable({
        axis: 'y',
        placeholder: 'placeholder',
        opacity: 0.6,        
        forcePlaceholderSize: true,
        update: function(e, ui) {            
            var prev = Number(ui.item.prev().find('input.Position').val());
            var next = Number(ui.item.next().find('input.Position').val());

            if( ! prev) {
                prev = 0;
            }
            if( ! next) {
                next = prev + 2 * $ruleStep;
            }

            var newpos = prev + Math.floor((next - prev) / 2);          
            ui.item.find('input.Position').val(newpos);            

            var formElement = $('#${actionId}').find('form');
            $.Nethgui.Server.ajaxMessage({
                isMutation: true,
                url: formElement.prop('action') + '/sortonly',
                data: formElement.serialize(),
                freezeElement: $(this)
            });
        }
    });
    var style = '<style type=\"text/css\">.Position {display: none}</style>';
    $('head').append(style);
    
    $('input.${hasChangesTarget}').on('nethguiupdateview', function (e, val) {
        $('#${commitId}').trigger(val === '1' ? 'nethguienable' : 'nethguidisable');
    }).on('nethguicreate', function () {
        var val = $(this).attr('value');
        window.setTimeout(function() {
            $('#${commitId}').trigger(val === '1' ? 'nethguienable' : 'nethguidisable');
        }, 100);
    });
});
" . '
/*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);
');
