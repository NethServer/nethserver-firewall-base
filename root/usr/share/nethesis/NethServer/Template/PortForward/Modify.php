<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'update_header_label';
} else {
    $headerText = 'create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

echo $view->panel()
    ->insert($view->selector('Proto'))
    ->insert($view->textInput('Src'))
    ->insert($view->textInput('Dst'))
    ->insert($view->textInput('DstHost'))
    #->insert($view->textInput('srcHost'))
    ->insert($view->textInput('Allow'))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

