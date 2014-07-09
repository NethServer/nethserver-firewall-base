<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'ip_update_header_label';
} else {
    $headerText = 'ip_create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

if ($view->getModule()->getIdentifier() == 'update') {
    $address = $view->textInput('Source', $view::STATE_DISABLED | $view::STATE_READONLY);
} else {
    $address = $view->panel()->setAttribute('class', 'labeled-control label-above')
    ->insert($view->literal(sprintf('<label for="%s">%s</label>', $view->getUniqueId('address'), \htmlspecialchars($T('Source_label')))))
    ->insert($view->textInput('Source', $view::STATE_READONLY | $view::LABEL_NONE)->setAttribute('class', 'pencil'))
    ->insert($view->button('PickSource', $view::BUTTON_SUBMIT))
    ->insert($view->hidden('SrcRaw'))
;

$buttonTarget = $view->getClientEventTarget('PickSource');
$inputTarget = $view->getClientEventTarget('Source');
$view->includeJavascript("
jQuery(function ($) {
    $('.${buttonTarget}').hide();
    $('.${inputTarget}').css({'background-color': 'white', 'cursor': 'pointer'}).on('click', function(e) { $(this).next().click(); });
});
");
}

echo $view->panel()
    ->insert($address)
    ->insert($view->selector('Priority', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

