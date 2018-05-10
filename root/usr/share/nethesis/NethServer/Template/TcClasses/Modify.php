<?php

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = 'update_header_label';
} else {
    $headerText = 'create_header_label';
}

echo $view->header()->setAttribute('template',$T($headerText));

echo $view->panel()
    ->insert($view->textInput('Name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->textInput('Description'))
    ->insert($view->hidden('Mark'))
    ->insert($view->fieldset()->setAttribute('template', $T('Inbound_bandwidth_limits_label'))
        ->insert($view->columns()
           ->insert($view->textInput('MinInputRate'))
           ->insert($view->textInput('MaxInputRate'))
        )
    )
    ->insert($view->fieldset()->setAttribute('template', $T('Outbound_bandwidth_limits_label'))
        ->insert($view->columns()
          ->insert($view->textInput('MinOutputRate'))
          ->insert($view->textInput('MaxOutputRate'))
        )
    );


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);

