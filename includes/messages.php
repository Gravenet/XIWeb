<?php

// Let's get the list of messages that we have here.

$strSQL = "SELECT * FROM messages WHERE (sender = :ID OR receiver = :ID) GROUP BY cid";
$statement = $xi->prepare($strSQL);
$statement->bindValue(':ID',getAccountID($_SESSION['auth']['username']));
$statement->bindValue(':ID',getAccountID($_SESSION['auth']['username']));

if (!$statement->execute()) {
  watchdog($statement->errorInfo(),'SQL');
}
else {
  $conversations = $statement->fetchAll(PDO::FETCH_ASSOC);
}

$page = 'messages';
?>
