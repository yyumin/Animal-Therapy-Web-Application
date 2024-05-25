<?php
  
  function showMessage($message, $type) {
    if ($type == 'message') { $color = 'grey'; }
    if ($type == 'error') { $color = 'red'; }
    if ($type == 'warning') { $color = 'orange'; }
    if ($type == 'success') { $color = 'green'; }

    echo 
      "<style>
      .footer {    
      background-color:" .$color .";
      }
      </style>";
    echo 
      '<div class="footer">
      <p>' . $message . ' </p>
      </div> ';

  }
?>