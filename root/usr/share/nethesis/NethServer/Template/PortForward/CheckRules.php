<?php

$view->requireFlag($view::INSET_DIALOG);

echo $view->header()->setAttribute('template', $T('check-rules_Header'));

echo "<pre>";
echo $view->textLabel('check-rules');
echo "</pre>";

echo $view->buttonList()
    ->insert($view->button('Close', $view::BUTTON_CANCEL))
;

/*$id =  $view->getClientEventTarget('check-rules');

$view->includeJavascript("
(function ( $ ) {
    $(document).ready(function() {
      $('.$id').on('nethguiupdateview', function(event, value, httpStatusCode) {
           $('.ui-dialog').width('auto');
      });
    });

} ( jQuery ));
");
*/
