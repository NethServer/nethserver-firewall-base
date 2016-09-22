<?php
/* @var $view \Nethgui\Renderer\Xhtml */


echo $view->header('RuleId')->setAttribute('template', $T($view->getModule()->getIdentifier() . '_header'));

echo $view->checkbox('status', 'enabled')->setAttribute('uncheckedValue', 'disabled');
echo $view->selector('Action', $view::SELECTOR_DROPDOWN);

echo $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Source'), \htmlspecialchars($T('Source_label')))))
    ->insert($view->textInput('Source', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickSource', $view::BUTTON_SUBMIT))
    ->insert($view->hidden('SrcRaw'))
;

echo $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Destination'), \htmlspecialchars($T('Destination_label')))))
    ->insert($view->textInput('Destination', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickDestination', $view::BUTTON_SUBMIT))
    ->insert($view->hidden('DstRaw'))
;

echo $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Service'), \htmlspecialchars($T('Service_label')))))
    ->insert($view->textInput('Service', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickService', $view::BUTTON_SUBMIT))
    ->insert($view->hidden('ServiceRaw'))
;

echo $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Time'), \htmlspecialchars($T('Time_label')))))
    ->insert($view->textInput('Time', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickTime', $view::BUTTON_SUBMIT))
    ->insert($view->hidden('TimeRaw'))
;

echo $view->checkbox('LogType', 'info')->setAttribute('uncheckedValue', 'none');
echo $view->textInput('Description');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP)
    ->insert($view->button('Cancel', $view::BUTTON_LINK)->setAttribute('value', $view->getModuleUrl('../Index')));

$actionId = $view->getUniqueId('Action');
$jsCode .= "
    var uiupdate = function (e) {
       $('#" . $view->getUniqueId('LogType') . "').prop('disabled', $('#${actionId}').val().match(/^(provider|priority);/));
    };
    $('#" . $view->getUniqueId() . "').on('nethguishow', uiupdate);
    $('#${actionId}').on('change', uiupdate);
";

foreach (array('Source', 'Destination', 'Service', 'Time') as $target) {
    $buttonTarget = $view->getClientEventTarget('Pick' . $target);
    $inputTarget = $view->getClientEventTarget($target);
    $jsCode .= "
    $('.${buttonTarget}').hide();
    $('.${inputTarget}').css({'background-color': 'white', 'cursor': 'pointer'}).on('click', function(e) { $(this).next().click(); });
";
}

$view->includeJavascript("
jQuery(function ($) {
${jsCode}
});
");