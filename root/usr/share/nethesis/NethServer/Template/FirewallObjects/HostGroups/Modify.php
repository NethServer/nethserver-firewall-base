<?php
$view->requireFlag($view::INSET_FORM);

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = $T('group_update_label`');
} else {
    $headerText = $T('group_edit_label');
}

echo $view->header('groupname')->setAttribute('template', $headerText);

echo $view->panel()
    ->setAttribute('title', $T('Group_Title'))    
    ->insert($view->textInput('groupname', ($view->getModule()->getIdentifier() === 'create' ? 0 : $view::STATE_DISABLED | $view::STATE_READONLY)))
    ->insert($view->textInput('Description'))
    ->insert($view->objectPicker('Members')
        ->setAttribute('objects', 'MembersDatasource')
        ->setAttribute('template', $T('Members'))
        ->setAttribute('objectLabel', 1));



echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
