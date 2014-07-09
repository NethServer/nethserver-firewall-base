<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'port_update_header_label';
} else {
    $headerText = 'port_create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

if ($view->getModule()->getIdentifier() == 'update') {
    $port = $view->textInput('port', $view::STATE_DISABLED | $view::STATE_READONLY);
} else {
    $port = $view->textInput('port');
}


echo $view->panel()
    ->insert($port)
    ->insert($view->selector('Proto', $view::SELECTOR_DROPDOWN))
    ->insert($view->selector('Priority', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

