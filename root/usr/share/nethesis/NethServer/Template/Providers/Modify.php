<?php

if ($view->getModule()->getIdentifier() === 'create') {
    $headerTemplate = $T('provider_create_header');
} else {
    $headerTemplate = $T('provider_update_header');
}

echo $view->header('name')->setAttribute('template', $headerTemplate);

echo $view->panel()
    ->insert($view->textInput('name', ($view->getModule()->getIdentifier() == 'update' ? $view::STATE_READONLY : 0)))
    ->insert($view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE)
        ->insert($view->selector('interface', $view::SELECTOR_DROPDOWN))
        ->insert($view->textInput('weight'))
        ->insert($view->textInput('Description'))
        ->insert($view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('ProviderAdvanced_label'))
            ->insert($view->textInput('checkip'))
        )

    )
    ->insert($view->fieldsetSwitch('status', 'disabled'));



echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL);

