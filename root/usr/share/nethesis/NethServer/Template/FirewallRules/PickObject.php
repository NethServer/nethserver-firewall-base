<?php
/* @var $view \Nethgui\Renderer\Xhtml */

echo $view->header('PickObjectHeader');

/* @var $view \Nethgui\Renderer\Xhtml */
$view->rejectFlag($view::INSET_FORM);

$searchForm = $view->form()->setAttribute('method', 'get');

$searchForm->insert(
    $view->panel()->setAttribute('class', 'searchform')
        ->insert($view->panel()->setAttribute('class', 'styledinput search')->insert($view->textInput('q', $view::LABEL_NONE)->setAttribute('placeholder', $T("SearchPlaceholder_label"))))
        ->insert($view->literal(' '))
        ->insert($view->button('Find'))
);

$resultsForm = $view->form()->setAttribute('method', 'post');

$resultsForm
    ->insert($view->textLabel('ResultsCount')->setAttribute('tag', 'div'))
    ->insert($view->selector('Result', $view::LABEL_NONE))
    ->insert($view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL))
;

$createLinks = $view->objectsCollection('CreateLinks')
    ->setAttribute('template', function ($view) use ($T) {
        return $view->button('Create', $view::BUTTON_LINK)->setAttribute('label', 'Label');
    });

echo $searchForm . $resultsForm  . $createLinks;

$viewId = $view->getUniqueId();
$findButtonTarget = $view->getClientEventTarget('Find');

$view->includeJavascript("
jQuery(function ($) {
    $('#${viewId}').on('change', function (e) { $(e.target).closest('form').submit(); });
    $('#${viewId} .Buttonlist').hide();
    $('#${viewId} .${findButtonTarget}').hide();
    var style = $('<style type=\'text/css\'>#${viewId} .Selector .choice {display: none}</style>');
    $('html > head').append(style);
});
");

$view->includeCss("
#${viewId} div.TextLabel, #${viewId} .searchform {margin-bottom: 1em}
#${viewId} .searchform  .styledinput {padding: 3px; border: 1px solid #d3d3de}
#${viewId} .searchform input[type=text] {display: block; width: 100%; border: 0; padding: 0; margin 0; outline: none; background: transparent}

#${viewId} .Selector label, #${viewId} a.Button.link.Create {vertical-align: middle; display: block; height: 30px; margin: 0 0 0.5em 0; cursor: pointer; border: 1px solid #d3d3d3; padding: 2px; background: linear-gradient(to bottom, #e6e6e6 0%, #fff 100%);}
#${viewId} .Selector label:hover, #${viewId} a.Button.link.Create  {background: linear-gradient(to bottom, #f0f0f0 0%, #fff 100%)}
#${viewId} .Selector li {margin: 0}
");
