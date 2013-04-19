<?php

echo $view->header()->setAttribute('template', 'Traffic shaping');

echo $view->selector('tc');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
