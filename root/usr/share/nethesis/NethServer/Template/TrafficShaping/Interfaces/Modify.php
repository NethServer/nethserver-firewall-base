<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'interface_update_header_label';
} else {
    $headerText = 'interface_create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

echo $view->panel()
    ->insert($view->selector('device', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('in'))
    ->insert($view->textInput('out'))
    ->insert($view->selector('priority', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

