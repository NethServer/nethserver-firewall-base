<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'ip_update_header_label';
} else {
    $headerText = 'ip_create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

if ($view->getModule()->getIdentifier() == 'update') {
    $address = $view->textInput('address', $view::STATE_DISABLED | $view::STATE_READONLY);
} else {
    $address = $view->textInput('address');
}

echo $view->panel()
    ->insert($address)
    ->insert($view->selector('Priority', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

