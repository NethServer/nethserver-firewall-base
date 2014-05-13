<?php

echo "<div class='dashboard-item'>";
echo $view->header()->setAttribute('template',$T('Providers_Title'));
echo "<dl>";
foreach ($view['providers'] as $name => $n) {
    echo "<dt>".$T($name)."</dt>"; 
    if ($n == "0") {
        echo "<dd class='provider-green'>UP</dd>"; 
    } else if ($n == "1") {
        echo "<dd class='provider-red'>DOWN</dd>"; 
    } else {
        echo "<dd class='provider-grey'>OFF</dd>"; 
    }
    echo "</dd>";
}
echo "</dl>";
echo "</div>";

$view->includeCSS("
  dd.provider-green {
      padding: 3px;
      color: green;
      font-weight: bold;
  }
  dd.provider-red {
      padding: 3px;
      color: red;
      font-weight: bold;
  }
  dd.provider-grey {
      padding: 3px;
      color: grey;
      font-weight: bold;
  }

");

