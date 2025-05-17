<?php

function pr($data, $type = false) {
  echo '<pre>';

  if ($type) {
    var_dump(print_r($data));
  }
  else {
    print_r($data);
  }

  echo '</pre>';
}