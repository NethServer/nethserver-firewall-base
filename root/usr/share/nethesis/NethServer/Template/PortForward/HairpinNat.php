<?php

echo $view->header()->setAttribute('template', $T('hairpin_header'));

echo $view->panel()
    ->insert($view->fieldset()->setAttribute('template', $T('HairpinNat_label'))
        ->insert($view->radioButton('HairpinNat', 'enabled'))
        ->insert($view->radioButton('HairpinNat', 'disabled'))
    );


echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
