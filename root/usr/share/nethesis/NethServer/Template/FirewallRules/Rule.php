<?php
/* @var $view \Nethgui\Renderer\Xhtml */


echo $view->header('RuleId')->setAttribute('template', $view->getModule()->getIdentifier() === 'Create' ? $T('Create_header') : $T('Edit_header'));

echo $view->checkbox('status', 'enabled')->setAttribute('uncheckedValue', 'disabled');
echo $view->selector('Action', $view::SELECTOR_DROPDOWN)->setAttribute('choices', array(
    array('accept', $T('ActionAccept_label')),
    array('reject', $T('ActionReject_label')),
    array('drop', $T('ActionDrop_label'))
));

echo $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Source'), \htmlspecialchars($T('Source_label')))))
    ->insert($view->textInput('Source', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickSource', $view::BUTTON_LINK))
    ->insert($view->hidden('SrcRaw'))
;

echo $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Destination'), \htmlspecialchars($T('Destination_label')))))
    ->insert($view->textInput('Destination', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickDestination', $view::BUTTON_LINK))
    ->insert($view->hidden('DstRaw'))
;

echo $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Service'), \htmlspecialchars($T('Service_label')))))
    ->insert($view->textInput('Service', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickService', $view::BUTTON_LINK))
    ->insert($view->hidden('ServiceRaw'))
;

echo $view->checkbox('LogType', 'info')->setAttribute('uncheckedValue', 'none');
echo $view->textInput('Description');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP)
    ->insert($view->button('Cancel', $view::BUTTON_LINK)->setAttribute('value', $view->getModuleUrl('../Index')));

foreach (array('Source', 'Destination', 'Service') as $target) {
    $buttonTarget = $view->getClientEventTarget('Pick' . $target);
    $inputTarget = $view->getClientEventTarget($target);
    $view->includeJavascript("
jQuery(function ($) {
    $('.${buttonTarget}').hide();
    $('.${inputTarget}').css({'background-color': 'white', 'cursor': 'pointer'}).on('click', function(e) { $(this).next().click(); });
});
");
}