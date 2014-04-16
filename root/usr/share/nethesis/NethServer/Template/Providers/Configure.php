<?php

echo $view->header()->setAttribute('template', $T('Configure_header'));

echo $view->panel()
     ->insert($view->selector('WanMode'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
