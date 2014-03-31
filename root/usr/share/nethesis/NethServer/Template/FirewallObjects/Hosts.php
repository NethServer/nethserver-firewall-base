<?php

if ($view->getModule()->getIdentifier() === 'create') {
    $headerTemplate = $T('host_create_header');
} else {
    $headerTemplate = $T('host_update_header');
}

echo $view->header('name')->setAttribute('template', $headerTemplate);

echo $view->panel()
    ->insert($view->textInput('name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->textInput('IpAddress'))
    ->insert($view->textInput('MacAddress')->setAttribute('placeholder', '00:00:00:00:00:00'))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);

