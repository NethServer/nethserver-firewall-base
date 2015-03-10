<?php
$view->includeCss('
    .AliasIp {
        margin-bottom: 15px;
        margin-right: 10px;
    }
    .FwObjectNat {
        margin-top: -1px;
    }
    .test {
        display: flex
    }
    .empty {
        margin: 15px;
    }');

echo $view->header()->setAttribute('template', $T('NAT11_Configure_header'));

$ds = $view['FwObjectNatDatasource'];

echo $view->objectsCollection('interfaces')
        ->setAttribute('ifEmpty', function ($view) use ($T) {
         return '<div class="empty">' . \htmlspecialchars($T('no_alias_found'))   . '</div>';
        })
         ->setAttribute('template', function ($view) use ($T, $ds) {
                return $view->panel()->setAttribute('class', 'test')
                    ->insert($view->textInput('AliasIp', $view::STATE_DISABLED | $view::STATE_READONLY ))
                    ->insert($view->selector('FwObjectNat', $view::SELECTOR_DROPDOWN)->setAttribute('choices', $ds))
                ;
    })->setAttribute('key', 'id');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);

