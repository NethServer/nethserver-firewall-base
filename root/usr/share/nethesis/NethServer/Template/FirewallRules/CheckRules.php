<?php

echo $view->header()->setAttribute('template', $T('check-rules_Header'));

echo "<pre>";
echo $view->textLabel('check-rules');
echo "</pre>";

echo $view->buttonList($view::BUTTON_CANCEL);
