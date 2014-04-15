<?php
/* @var $view \Nethgui\Renderer\Xhtml */

echo $view->header()->setAttribute('template', $T('general_header'));

echo $view->panel()
    ->insert($view->selector('Policy'))
    ->insert($view->selector('ExternalPing'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
