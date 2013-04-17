<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'update_header_label';
} else {
    $headerText = 'create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

echo $view->panel()
    ->insert($view->selector('status'))
    ->insert($view->selector('proto'))
    ->insert($view->textInput('src'))
    ->insert($view->textInput('dst'))
    ->insert($view->textInput('dstHost'))
    #->insert($view->textInput('srcHost'))
    ->insert($view->textInput('allow'))
    ->insert($view->textInput('description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

