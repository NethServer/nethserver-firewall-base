<?php

echo "<div class='dashboard-item'>";
echo $view->header()->setAttribute('template',$T('Providers_Title'));
if (!$view['providers']) {
    echo $T('no_providers');
} else {
    echo "<dl>";
    foreach ($view['providers'] as $name => $n) {
        echo "<dt>".$T($name)."</dt>"; 
        if ($n == "0") {
            echo "<dd class='provider-green'>" . $T('Provider_status_UP') . "</dd>";
        } else if ($n == "1") {
            echo "<dd class='provider-red'>" . $T('Provider_status_DOWN') . "</dd>";
        } else {
            echo "<dd class='provider-grey'>" . $T('Provider_status_OFF') . "</dd>";
        }
        echo "</dd>";
    }
    echo "</dl>";
}
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

