<?php

echo $view->header('Configure')->setAttribute('template', $T('Configure_header'));

echo $view->panel()
     ->insert($view->selector('WanMode'));
     //->insert($view->inputText('WanMode'));

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
