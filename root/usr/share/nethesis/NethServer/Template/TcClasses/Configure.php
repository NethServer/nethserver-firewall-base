<?php

echo $view->header()->setAttribute('template', $T('configure_header'));

echo $view->panel()
    ->insert($view->fieldset()->setAttribute('template', $T('Options_label'))
        ->insert($view->checkbox('TCTosOptimization', 'enabled')->setAttribute('uncheckedValue', 'disabled'))
        ->insert($view->textInput('TCVoipReservation'))
    );


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
