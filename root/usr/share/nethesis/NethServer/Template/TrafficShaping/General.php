<?php

echo $view->header()->setAttribute('template', $T('TrafficShaping_General_header'));

echo $view->selector('tc');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
