<?php

// We are on the message thread page, let's get the messages available for this conversation

$strSQL = "SELECT * FROM messages WHERE cid = :cID ORDER BY timestamp ASC";
$statement = $xi->prepare($strSQL);
$statement->bindValue(':cID',$cid);

if (!$statement->execute()) {
  watchdog($statement->errorInfo(),'SQL');
}
else {
  $messages = $statement->fetchAll(PDO::FETCH_ASSOC);
  if (!empty($messages)) {
    // Let's mark all messages in this thread as read
    markMessagesRead($cid);
  }
}

if (!empty($_POST['send_message'])) {
  if (!empty($_POST['message'])) {
    $strSQL = "INSERT INTO messages (`cid`,`status`,`timestamp`,`sender`,`receiver`,`body`) VALUES (:cID,'0',:time,:sender,:receiver,:body)";
    $statement = $xi->prepare($strSQL);
    $statement->bindValue(':cID',$cid);
    $statement->bindValue(':time',time());
    $statement->bindValue(':sender',getAccountId($_SESSION['auth']['username']));
    $statement->bindValue(':receiver',getRecipient($cid,getAccountId($_SESSION['auth']['username'])));
    $statement->bindValue(':body',$_POST['message']);
    
    if (!$statement->execute()) {
      watchdog($statement->errorInfo(),'SQL');
    }
    else {
      header("Location: ". $_SERVER['REQUEST_URI']);
    }
  }
}

$page = 'messages read';
?>
