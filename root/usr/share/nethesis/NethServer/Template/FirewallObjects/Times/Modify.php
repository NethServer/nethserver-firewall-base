<?php
/* @var $view \Nethgui\Renderer\Xhtml */

$headerText = $T(sprintf('Times_%s_label', $view->getModule()->getIdentifier()));
echo $view->header('name')->setAttribute('template', $headerText);

echo $view->panel()
    ->insert($view->textInput('name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->textInput('Description'))
    ->insert($view->columns()
        ->insert($view->timeInput('TimeStart'))
        ->insert($view->timeInput('TimeStop'))
    )
    ->insert($view->selector('WeekDays', $view::SELECTOR_MULTIPLE))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);

