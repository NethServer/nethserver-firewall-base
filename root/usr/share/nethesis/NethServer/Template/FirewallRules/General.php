<?php

echo $view->header()->setAttribute('template', $T('general_header'));

echo $view->panel()
    ->insert($view->selector('Policy'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
