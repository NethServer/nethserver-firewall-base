<?php
/* @var $view \Nethgui\Renderer\Xhtml */
$view->includeCss('
    .columns {
        margin: 10px;
    }');

echo $view->header()->setAttribute('template', $T('NAT11_Configure_header'));

echo $view->objectsCollection('interfaces')
    ->setAttribute('template', function ($view) use ($T) {
        return $view->panel()
                ->insert($view->columns()
                    ->insert($view->textInput('InterIp', $view::LABEL_NONE | $view::STATE_DISABLED | $view::STATE_READONLY ))
                    ->insert($view->textInput('FwObjectNat', $view::LABEL_NONE ))
                    )
            
        ;
    })->setAttribute('key', 'id');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
