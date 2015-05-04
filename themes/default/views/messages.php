<?php

if (!empty($_SESSION['errors'])) {
  $output .= '
   <div class="uk-alert uk-alert-danger uk-width-1-2 uk-align-center">
      <i class="uk-icon uk-icon-times"></i> '.$lang['error']['general']['error_message'].'
      <ul>
  ';
  foreach ($_SESSION['errors'] as $error) {
    $output .= '
        <li>'.$error.'</li>
    ';
  }
  $output .= '
      </ul>
    </div>
';
}
else {
  $output .= '    <br />';
}

if ($page == 'messages') {
  $output .= '
  <div class="uk-panel uk-panel-box uk-align-center uk-width-1-2">
      <h3 class="uk-panel-title"><i class="uk-icon uk-icon-envelope"></i> Messages</h3>
      <hr class="uk-panel-divider" />
      <span class="uk-text-small"><i class="uk-icon uk-icon-plus"></i> <a href="messages.php?a=compose">Compose</a></span>
      <hr class="uk-panel-divider" />
      <div class="uk-panel uk-panel-box uk-panel-box-secondary">';
      if (!empty($conversations)) {
        $output .= '
        <table class="uk-table uk-table-hover uk-table-condensed uk-text-small">
          <tbody>';
          foreach ($conversations as $conversation) {
            // Let's trim any messages that are over 340 characters, and concat a ... to the end
            if (strlen($conversation['body']) >= 340) {
              $body = substr($conversation['body'],0, 336);
              $body .= ' ...';
            }
            else {
              $body = $conversation['body'];
            }
            // If the receiver is us, we need to make the message bold, so we know it's new
            if ($conversation['receiver'] == getAccountID($_SESSION['auth']['username'])) {
              $output .= '
            <tr>
              <td><a href="messages.php?cid='.$conversation['cid'].'" style="display: block;"><strong>'.getAccountName($conversation['sender']).'</strong><br />
              '.(getMessageStatus($conversation['mid']) == 0 ? '<strong>'.$body.'</strong>' : ''.$conversation['body'].'').'<br />
              <em class="uk-text-muted uk-align-right">'.date('j/n/Y g:i A',$conversation['timestamp']).'</em></a></td>
            </tr>';
            }
            else {
              $output .= '
            <tr>
              <td><a href="messages.php?cid='.$conversation['cid'].'" style="display: block;"><strong>'.getAccountName($conversation['sender']).'</strong><br />
              '.(getMessageStatus($conversation['mid']) == 0 ? '<i class="uk-icon uk-icon-reply"></i> '.$body.'' : '<i class="uk-icon uk-icon-check"></i> '.$body.'').'<br />
              <em class="uk-text-muted uk-align-right">'.date('j/n/Y g:i A',$conversation['timestamp']).'</em></a></td>
            </tr>';
            }
          }
        $output .= '
          </tbody>
        </table>';
      }
      else {
        $output .= '
        <em class="uk-text-muted">You have no messages.</em>';
      }
      $output .= '
      </div>
  </div>
  ';
}
else {
  $output .='
  <div class="uk-panel uk-panel-box uk-align-center uk-width-1-2">
  <div class="uk-grid">
    <div class="uk-width-1-2">
      <a href="'.$_SERVER['REQUEST_URI'].'"><i class="uk-icon uk-icon-refresh uk-icon-spin"></i> Reload page</a><br /><br />
    </div>
    <div class="uk-width-1-2 uk-text-right">
      <a href="'.$_SERVER['REQUEST_URI'].'" class="uk-text-danger"><i class="uk-icon uk-icon-times"></i> Delete Conversation</a><br /><br />
    </div>
  </div>';
  
  if (!empty($messages)) {
    foreach ($messages as $message) {
      if ($message['sender'] == getAccountID($_SESSION['auth']['username'])) {
        $output .= '
    <div class="uk-panel uk-panel-box uk-panel-box-primary uk-text-right">
    <strong>'.getAccountName($message['sender']).'</strong> said:<br />
    '.$message['body'].'
    </div>
    <br />';
      }
      else {
        $output .= '
    <div class="uk-panel uk-panel-box uk-panel-box-secondary uk-text-left">
    <strong>'.getAccountName($message['sender']).'</strong> said:<br />
    '.$message['body'].'
    </div>
    <br />';
      }
    }
  }
  else {
    $output .= '
  <em class="uk-text-muted">No messages, or invalid conversation ID</em>';
  }
  
  if (isFriend($message['sender'],$message['receiver'])) {
    $output .= '
  <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
    <input type="hidden" name="send_message" value="1" />
    <div class="uk-form-row">
      <input type="textbox" name="message" style="width: 90%;" /> <button class="uk-button uk-button-primary" type="submit" style="10%;">Send</button>
    </div>
  </form>
    ';
  }
  $output .= '      
  </div>';
}
$output .= ' 
    <!-- This is the modal -->
    <div id="my-modal" class="uk-modal">
        <div class="uk-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            <div class="uk-panel">
              <div class="uk-panel-title">Friend 1</div>
              <hr class="uk-panel-divider" />
              <h3 class="uk-panel-title uk-text-right">You</h3>
              <div class="uk-panel uk-panel-box uk-panel-box-primary uk-text-right">
                Hey there!
              </div>
              <br />
              <h3 class="uk-panel-title">Friend 1</h3>
              <div class="uk-panel uk-panel-box uk-panel-box-secondary uk-text-left">
                Hi! Thanks for contacting me!
                
                What is up with you ?
              </div>
              <br />
              <div class="uk-panel uk-width-1-1 uk-align-left">
                <form class="uk-form-horizontal">
                  <div class="uk-form-row">
                    <div style="width: 100%;"><input type="text" placeholder="username or email" style="width: 90%;" /><button style="width: 10%;">Send</button></div>
                  </div>
                </form>
              </div>
            </div>
        </div>
    </div>
    
    <!-- This is the off-canvas sidebar -->
    <div id="my-id" class="uk-offcanvas">
      <div class="uk-offcanvas-bar uk-offcanvas-bar-flip">
      <div class="uk-panel">
        <h3 class="uk-panel-title">Online Friends (1)</h3>
        <span><a href="#" class="uk-text-primary"><i class="uk-icon uk-icon-user-plus"></i> Add friend</a></span>
        <hr class="uk-panel-divider" />
        <i class="uk-icon uk-icon-toggle-on uk-text-success"></i> <a href="#my-modal"  data-uk-modal>Friend 1</a> <i class="uk-icon uk-icon-times uk-text-danger"></i>
        <hr class="uk-panel-divider" />
        <h3 class="uk-panel-title">Offline Friends (4)</h3>
        <hr class="uk-panel-divider" />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 2 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 3 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 4 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 5 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <hr class="uk-panel-divider" />
      </div>
    </div>
    
    <!-- This is the modal -->
    <div id="my-modal" class="uk-modal">
        <div class="uk-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            <div class="uk-panel">
              <div class="uk-panel-title">Friend 1</div>
              <hr class="uk-panel-divider" />
              <h3 class="uk-panel-title uk-text-right">You</h3>
              <div class="uk-panel uk-panel-box uk-panel-box-primary uk-text-right">
                Hey there!
              </div>
              <br />
              <h3 class="uk-panel-title">Friend 1</h3>
              <div class="uk-panel uk-panel-box uk-panel-box-secondary uk-text-left">
                Hi! Thanks for contacting me!
                
                What is up with you ?
              </div>
              <br />
              <div class="uk-panel uk-width-1-1 uk-align-left">
                <form class="uk-form-horizontal">
                  <div class="uk-form-row">
                    <div style="width: 100%;"><input type="text" placeholder="username or email" style="width: 90%;" /><button style="width: 10%;">Send</button></div>
                  </div>
                </form>
              </div>
            </div>
        </div>
    </div>
    
    <!-- This is the off-canvas sidebar -->
    <div id="my-id" class="uk-offcanvas">
      <div class="uk-offcanvas-bar uk-offcanvas-bar-flip">
      <div class="uk-panel">
        <h3 class="uk-panel-title">Online Friends (1)</h3>
        <span><a href="#" class="uk-text-primary"><i class="uk-icon uk-icon-user-plus"></i> Add friend</a></span>
        <hr class="uk-panel-divider" />
        <i class="uk-icon uk-icon-toggle-on uk-text-success"></i> <a href="#my-modal"  data-uk-modal>Friend 1</a> <i class="uk-icon uk-icon-times uk-text-danger"></i>
        <hr class="uk-panel-divider" />
        <h3 class="uk-panel-title">Offline Friends (4)</h3>
        <hr class="uk-panel-divider" />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 2 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 3 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 4 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <em class="uk-text-muted"><i class="uk-icon uk-icon-toggle-off"></i> Friend 5 <i class="uk-icon uk-icon-times uk-text-danger"></i></em><br />
        <hr class="uk-panel-divider" />
      </div>
    </div>
';
?>
