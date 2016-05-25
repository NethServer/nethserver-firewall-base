<?php

echo $view->header()->setAttribute('template', $T('Configure_header'));

echo $view->panel()
     ->insert($view->selector('WanMode'))
     ->insert($view->textInput('CheckIP'));

echo $view->fieldset()
     ->setAttribute('template', $T('LSM_Params'))
     ->insert($view->textInput('MaxNumberPacketLoss'))
     ->insert($view->textInput('MaxPercentPacketLoss'))
     ->insert($view->textInput('PingInterval'));

echo $view->panel()
    ->insert($view->fieldsetSwitch('NotifyWan', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->textInput('NotifyWanFrom'))
        ->insert($view->textInput('NotifyWanTo'))
    );


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
