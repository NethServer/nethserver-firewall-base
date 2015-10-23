<?php

/* @var $view \Nethgui\Renderer\Xhtml */

if ($view->getModule()->getIdentifier() === 'create') {
    $headerTemplate = $T('provider_create_header');
    $interface = $view->selector('interface', $view::SELECTOR_DROPDOWN);
} else {
    $headerTemplate = $T('provider_update_header');
    $interface = $view->textInput('interface', $view::STATE_DISABLED | $view::STATE_READONLY);
}

echo $view->header('name')->setAttribute('template', $headerTemplate);

echo $view->panel()
    ->insert($view->textInput('name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY | $view::STATE_DISABLED : 0)))
    ->insert($view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert($interface)
        ->insert($view->textInput('weight'))
    )
    ->insert($view->fieldsetSwitch('status', 'disabled'))
    ->insert($view->textInput('Description'));



echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);

