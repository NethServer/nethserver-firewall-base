<?php
/* @var $view \Nethgui\Renderer\Xhtml */

$headerText = $T(sprintf('Zones_%s_label', $view->getModule()->getIdentifier()));
echo $view->header('name')->setAttribute('template', $headerText);

echo $view->panel()
    ->insert($view->textInput('name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->textInput('Network'))
    ->insert($view->selector('Interface', $view::SELECTOR_DROPDOWN))
    ->insert($view->textInput('Description'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);

