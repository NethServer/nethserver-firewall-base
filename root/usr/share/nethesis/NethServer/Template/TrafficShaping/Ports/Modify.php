<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'port_update_header_label';
} else {
    $headerText = 'port_create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

echo $view->panel()
    ->insert($view->textInput('port'))
    ->insert($view->selector('proto', $view::SELECTOR_DROPDOWN))
    ->insert($view->selector('priority', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

