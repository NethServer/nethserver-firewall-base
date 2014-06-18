<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'interface_update_header_label';
} else {
    $headerText = 'interface_create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

if ($view->getModule()->getIdentifier() == 'update') {
    $device = $view->textInput('device', $view::STATE_DISABLED | $view::STATE_READONLY);
} else {
    $device = $view->selector('device', $view::SELECTOR_DROPDOWN);
}
echo $view->panel()
    ->insert($device)
    ->insert($view->textInput('In'))
    ->insert($view->textInput('Out'))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

