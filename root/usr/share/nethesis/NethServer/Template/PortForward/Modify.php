<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'update_header_label';
} else {
    $headerText = 'create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

$dstHost = $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('Destination'), \htmlspecialchars($T('DstHost_label')))))
    ->insert($view->textInput('Destination', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickDestination', $view::BUTTON_SUBMIT))
    ->insert($view->hidden('DstRaw'))
;

echo $view->panel()
    ->insert($view->selector('Proto'))
    ->insert($view->selector('OriDst'))
    ->insert($view->textInput('Src'))
    ->insert($view->textInput('Dst'))
    ->insert($dstHost)
    ->insert($view->textInput('Allow'))
    ->insert($view->checkbox('Log', 'info')->setAttribute('uncheckedValue', 'none'))
    ->insert($view->textInput('Description'));


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);


$buttonTarget = $view->getClientEventTarget('PickDestination');
$inputTarget = $view->getClientEventTarget('Destination');
$view->includeJavascript("
jQuery(function ($) {
    $('.${buttonTarget}').hide();
    $('.${inputTarget}').css({'background-color': 'white', 'cursor': 'pointer'}).on('click', function(e) { $(this).next().click(); });
});
");
