<?php
/* @var $view \Nethgui\Renderer\Xhtml */

$headerText = $T(sprintf('HostGroups_%s_label', $view->getModule()->getIdentifier()));
echo $view->header('name')->setAttribute('template', $headerText);

echo $view->panel()
    ->setAttribute('title', $T('Group_Title'))    
    ->insert($view->textInput('name', ($view->getModule()->getIdentifier() === 'update' ? $view::STATE_DISABLED | $view::STATE_READONLY : 0)))
    ->insert($view->textInput('Description'))
    ->insert($view->objectPicker('Members')
        ->setAttribute('objects', 'MembersDatasource')
        ->setAttribute('template', $T('Members'))
        ->setAttribute('objectLabel', 1));



echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
