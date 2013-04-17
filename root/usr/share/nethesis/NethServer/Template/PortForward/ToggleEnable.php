<?php
$view->requireFlag($view::INSET_DIALOG);

if ($view->getModule()->getIdentifier() == 'disable') {
    $headerText = 'Disable port forward';
    $panelText = 'Proceed with port forward disable?';
} else {
    $headerText = 'Enable port forward';
    $panelText = 'Proceed with port forward enable?';
}

echo $view->panel()
    ->insert($view->header('pf')->setAttribute('template', $headerText))
    ->insert($view->textLabel('pf')->setAttribute('template', $view->translate($panelText)))
;

echo $view->buttonList()
    ->insert($view->button('Yes', $view::BUTTON_SUBMIT))
    ->insert($view->button('No', $view::BUTTON_CANCEL)->setAttribute('value', $view['Cancel']))
;

