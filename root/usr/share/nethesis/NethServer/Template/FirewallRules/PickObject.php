<?php
/* @var $view \Nethgui\Renderer\Xhtml */

echo $view->header('PickObjectHeader');

/* @var $view \Nethgui\Renderer\Xhtml */
$view->rejectFlag($view::INSET_FORM);

$searchForm = $view->form('SearchForm')->setAttribute('method', 'get');

$searchForm->insert(
    $view->panel()->setAttribute('class', 'searchform')
        ->insert($view->panel()->setAttribute('class', 'styledinput search')->insert($view->textInput('q', $view::LABEL_NONE)->setAttribute('placeholder', $T("SearchPlaceholder_label"))))
        ->insert($view->literal(' '))
        ->insert($view->button('Find'))
        ->insert($view->hidden('f'))
        ->insert($view->hidden('m'))
);

$resultsForm = $view->form()->setAttribute('method', 'post');

$resultsForm
    ->insert($view->textLabel('ResultsCount')->setAttribute('tag', 'div'))
    ->insert($view->selector('Result', $view::LABEL_NONE))
    ->insert($view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL))
    ->insert($view->hidden('f'))
    //->insert($view->hidden('m'))
;

$createLinks = $view->objectsCollection('CreateLinks')
    ->setAttribute('template', function ($view) use ($T) {
    return $view->button('Create', $view::BUTTON_LINK)->setAttribute('label', 'Label');
});

echo $searchForm . $resultsForm . $createLinks;

$viewId = $view->getUniqueId();
$findButtonTarget = $view->getClientEventTarget('Find');

$view->includeJavascript("
jQuery(function ($) {
    $('#${viewId}').on('change', function (e) { $(e.target).closest('form').submit(); });
    $('#${viewId} .Buttonlist').hide();
    $('#${viewId} .${findButtonTarget}').hide();
    var style = $(['<style type=\'text/css\'>',
'#${viewId} .Selector .choice {display: none}',
'#${viewId} div.TextLabel, #${viewId} .searchform {margin-bottom: 1em}',
'#${viewId} .searchform  .styledinput {padding: 3px; border: 1px solid #d3d3de}',
'#${viewId} .searchform input[type=text] {display: block; width: 100%; border: 0; padding: 0; margin 0; outline: none; background: transparent}',
'#${viewId} .Selector label, #${viewId} a.Button.link.Create {vertical-align: middle; display: block; height: 30px; margin: 0 0 0.5em 0; cursor: pointer; border: 1px solid #d3d3d3; padding: 2px; background: linear-gradient(to bottom, #e6e6e6 0%, #fff 100%);}',
'#${viewId} .Selector label:hover, #${viewId} a.Button.link.Create  {background: linear-gradient(to bottom, #f0f0f0 0%, #fff 100%)}',
'#${viewId} .Selector li {margin: 0}',
'#${viewId} .Selector label { height: auto; padding: 12px;  font-style: normal;  font-weight: normal;  line-height: 1;  -webkit-font-smoothing: antialiased;  -moz-osx-font-smoothing: grayscale;}',
'#${viewId} .Selector label::before { font-family: FontAwesome; content: \"\\\\F10C\\\\20\"; letter-spacing: 4px }',
'#${viewId} .Selector input[value^=\"host\"] + label::before { content: \"\\\\F1B2\\\\20\" }',
'#${viewId} .Selector input[value^=\"remote\"] + label::before { content: \"\\\\F1B2\\\\20\" }',
'#${viewId} .Selector input[value^=\"local\"] + label::before { content: \"\\\\F108\\\\20\" }',
'#${viewId} .Selector input[value^=\"host-group\"] + label::before { content: \"\\\\F1B3\\\\20\" }',
'#${viewId} .Selector input[value^=\"iprange\"] + label::before { content: \"\\\\F1B3\\\\20\" }',
'#${viewId} .Selector input[value^=\"cidr\"] + label::before { content: \"\\\\F1B3\\\\20\" }',
'#${viewId} .Selector input[value^=\"any\"] + label::before { content: \"\\\\F0AC\\\\20\" }',
'#${viewId} .Selector input[value^=\"fw\"] + label::before { content: \"\\\\F06D\\\\20\" }',
'#${viewId} .Selector input[value^=\"fwservice\"] + label::before { content: \"\\\\F013\\\\20\" }',
'#${viewId} .Selector input[value^=\"service\"] + label::before { content: \"\\\\F1DB\\\\20\" }',
'#${viewId} .Selector input[value^=\"ndpi\"] + label::before { content: \"\\\\F016\\\\20\" }',
'#${viewId} .Selector input[value^=\"zone\"] + label::before { content: \"\\\\F096\\\\20\" }',
'#${viewId} .Selector input[value^=\"role\"] + label::before { content: \"\\\\F0C8\\\\20\" }',
'#${viewId} .Selector input[value^=\"role;red\"] + label { color: red }',
'#${viewId} .Selector input[value^=\"role;green\"] + label { color: green }',
'#${viewId} .Selector input[value^=\"role;orange\"] + label { color: orange }',
'#${viewId} .Selector input[value^=\"role;blue\"] + label { color: blue }',
'</style>'].join(\"\\n\"));
    $('html > head').append(style);
});
");
