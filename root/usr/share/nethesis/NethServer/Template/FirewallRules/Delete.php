<?php
/* @var $view \Nethgui\Renderer\Xhtml */

$view->requireFlag($view::INSET_DIALOG);

echo $view->header('ruleId')->setAttribute('template', $T('Delete_header'));
echo $view->textLabel('message');
echo $view->buttonList($view::BUTTON_CANCEL)
    ->insert($view->button('Delete', $view::BUTTON_SUBMIT))
;
