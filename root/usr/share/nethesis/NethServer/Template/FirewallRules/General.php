<?php
/* @var $view \Nethgui\Renderer\Xhtml */

echo $view->header()->setAttribute('template', $T('general_header'));

echo $view->panel()
    ->insert($view->selector('Policy'))
    ->insert($view->selector('ExternalPing'));

echo $view->panel()
    ->insert($view->fieldsetSwitch('MACValidation', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->selector('MACValidationPolicy'))
    );

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
