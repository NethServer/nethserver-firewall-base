<?php

if ($view->getModule()->getIdentifier() === 'create') {
    $headerTemplate = $T('provider_create_header');
    $interface = $view->selector('interface', $view::SELECTOR_DROPDOWN);
} else {
    $headerTemplate = $T('provider_update_header');
    $interface = $view->textInput('interface', $view::STATE_READONLY);
}

echo $view->header('name')->setAttribute('template', $headerTemplate);

echo $view->panel()
    ->insert($view->textInput('name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->fieldsetSwitch('status', 'disabled'))
    ->insert($view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert($interface)
        ->insert($view->textInput('weight'))
        ->insert($view->textInput('Description'))
        ->insert($view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('ProviderAdvanced_label'))
            ->insert($view->textInput('checkip'))
        )
    );



echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);

