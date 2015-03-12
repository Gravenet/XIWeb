<?php
#ini_set("display_errors","Off");

function doLogin($username,$password) {
  global $db;

  $strSQL = "SELECT * FROM accounts WHERE (login = :username OR email = :username) AND password = PASSWORD(:password)";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':username',$_POST['username']);
  $statement->bindValue(':password',$_POST['password']);

  if (!$statement->execute()) { 
    var_dump( $statement->errorInfo() ); 
  }
  else {
    $arrReturn = $statement->fetchAll(); 
  }

  if (!empty($arrReturn)) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

// Character related functions

function getCharacterRace($charid) {
  global $db,$races;
  
  $strSQL = "SELECT race FROM char_look WHERE charid = :charID";
  $statement = $db->prepare($strSQL);
  $statement->bindValue(':charID',$charid);
  
  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
  if (!empty($arrReturn)) {
    return $races[$arrReturn[0]['race']];
  }
  else {
    return '';
  }
}

function getCharacterGender($charid) {
  global $db,$races;
  
  $strSQL = "SELECT race FROM char_look WHERE charid = :charID";
  $statement = $db->prepare($strSQL);
  $statement->bindValue(':charID',$charid);
  
  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
  if (!empty($arrReturn)) {
    $temp = $races[$arrReturn[0]['race']];
    
    if ($temp == '3' || $temp == '5' || $temp == '6') {
      return 'Female';
    }
    else {
      return 'Male';
    }
  }
  else {
    return '';
  }
}

function getCharacterAppearance($charid) {
  global $db,$faces;
  
  $strSQL = "SELECT face FROM char_look WHERE charid = :charID";
  $statement = $db->prepare($strSQL);
  $statement->bindValue(':charID',$charid);
  
  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
  if (!empty($arrReturn)) {
    $temp = array_flip($faces);
    $face = $arrReturn[0]['face'];
    return $temp[$face];
  }
  else {
    return '';
  }
}

function getCharacterEquipment($charid,$slot) {
  global $db, $equipment_ids;
  
  // The inventory/equipment system is weird. First you must look up the slot that the item is in in the char_equip table, under slotid. The equipslotid is for the equipment slot (1-16).
  // If the slotid for equipslotid returns NULL or 0, there's nothing equipped in that slot
  // If it returns a value, you then must look up that value in char_inventory, to find out what itemid is stored in that slot
  
  $strSQL = "SELECT slotid FROM char_equip WHERE charid = :charID AND equipslotid = :slotID";
  $statement = $db->prepare($strSQL);
  $statement->bindValue(':charID',$charid);
  $statement->bindValue(':slotID',$equipment_ids[$slot]);
  
  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
  // If the character has nothing stored in the equipslot, return nothing, else return the slotID that the item is in in char_inventory
  if (empty($arrReturn)) {
    return 0;
  }
  else {
    $slotid = $arrReturn[0]['slotid'];
    
    $strSQL = "SELECT itemid FROM char_inventory WHERE slot = :slot AND charid = :charID AND (location = 0 OR location = 8)";
    $statement = $db->prepare($strSQL);
    $statement->bindValue(':slot',$slotid);
    $statement->bindValue(':charID',$charid);
    
    if (!$statement->execute()) {
      var_dump( $statement->errorInfo() );
    }
    else {
      $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // If the result isn't empty, return the itemid of the item in this slot, else return 0 to signify nothing.
    if (!empty($arrReturn)) {
      return $arrReturn[0]['itemid'];
      var_dump($arrReturn[0]['itemid']);
    }
    else {
      return 0;
    }
  }
}

function getCharacterName($charid) {
  global $db;

  $strSQL = "SELECT `charname` FROM chars WHERE charid = :charID";
  $statement = $db->prepare($strSQL);
  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['charname'];  }
  else {
    return '';
  }
}

// Deprecated, remove this eventually
function isProfilePublic() {
  return true;
}

function getCharacterOwner($charid) {
  global $db,$skill_ids;
  
  $strSQL = "SELECT `accid` FROM chars WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['accid'];  }
  else {
    return '0';
  }
}

function getCharacterSkill($charid,$skill) {
  global $db,$skill_ids;
  
  $strSQL = "SELECT `value` FROM char_skills WHERE charid = :charID AND skillid = :skillID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);
  $statement->bindValue(':skillID',$skill_ids[$skill]);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['value'];  }
  else {
    return '0';
  }
}

function getCharacterRank($charid,$rank) {
  global $db;
  
  $strSQL = "SELECT `rank_$rank` FROM char_profile WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['rank_'.$rank];  }
  else {
    return '0';
  }
}

function getCharacterFame($charid,$fame) {
  global $db;
  
  $strSQL = "SELECT `fame_$fame` FROM char_profile WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['fame_'.$fame];  }
  else {
    return '0';
  }
}

function getCharacterInventory($charid,$slot) {
  global $db;
  
  $strSQL = "SELECT `$slot` FROM char_storage WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0][$slot];  }
  else {
    return '0';
  }
}

function getCharacterHP($charid) {
  global $db;
  
  $strSQL = "SELECT hp FROM char_stats WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['hp'];  }
  else {
    return '';
  }
}

function getCharacterMP($charid) {
  global $db;
  
  $strSQL = "SELECT mp FROM char_stats WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['mp'];  }
  else {
    return '';
  }
}

function getCharacterExp($charid,$job) {
  global $db;
  
  $strSQL = "SELECT $job FROM char_exp WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0][$job];  }
  else {
    return '';
  }
}

function getCharacterMaxExp($charid) {
  global $db;
  
  $strSQL = "SELECT exp FROM exp_base WHERE level = :level";
  $statement = $db->prepare($strSQL);

  $level = getJobLevel($charid,getCharMJob($charid)) +1;
  
  $statement->bindValue(':level',$level);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['exp'];  }
  else {
    return '';
  }
}

function getTitle($charid) {
  global $db,$titles;
  
  $strSQL = "SELECT title FROM char_stats WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return ucwords(strtolower(str_replace('_',' ',$titles[$arrReturn[0]['title']])));  
  }
  else {
    return '';
  }
}

function getCharMJob($charid) {
  global $db,$jobs;
  
  $strSQL = "SELECT mjob FROM char_stats WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll();
  }

  if (!empty($arrReturn)) {
    return $jobs[$arrReturn[0]['mjob']];  }
  else {
    return '';
  }
}

function getCharSJob($charid) {
  global $db,$jobs;
  
  $strSQL = "SELECT sjob FROM char_stats WHERE charid = :charID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll();
  }

  if (!empty($arrReturn)) {
    return $jobs[$arrReturn[0]['sjob']];
  }
  else {
    return '';
  }
}

function getJobLevel($charid,$job) {
  global $db;
  
  if ($job != '') {
    
    $strSQL = "SELECT $job FROM char_jobs WHERE charid = :charID";
    $statement = $db->prepare($strSQL);
    
    $statement->bindValue(':charID',$charid);

    if (!$statement->execute()) {
      var_dump( $statement->errorInfo() );
    }
    else {
      $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($arrReturn)) {
      #var_dump($arrReturn);
      return $arrReturn[0][$job];
    }
    else {
      return '0';
    }
  }
  else {
    return '';
  }
}

function getCharacterZone($charid) {
global $db;

  $strSQL = "SELECT pos_zone FROM chars WHERE charid = :charID";
  $statement = $db->prepare($strSQL);
  
  $statement->bindValue(':charID',$charid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    #var_dump($arrReturn);
    return $arrReturn[0]['pos_zone'];
  }
  else {
    return '';
  }
}

// Account related functions

function getAccountName($accid) {
  global $db;

  $strSQL = "SELECT login FROM accounts WHERE id = :id";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':id',$accid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll();
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['login'];
  }
  else {
    return 0;
  }
}

function getAccountID($account) {
  global $db;

  $strSQL = "SELECT id FROM accounts WHERE login = :username OR email = :username";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':username',$_SESSION['auth']['username']);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll();
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['id'];
  }
  else {
    return 0;
  }

}

// MISC functions
function serverstatus() {
  global $server_address;
  
  if (fsockopen($server_address,54230))
  {
    return 1;
  }
  else
  {
    return 0;
  }
}

function getZoneName($zoneid) {
  global $db, $lang;
  
  $strSQL = "SELECT name FROM zone_settings WHERE zoneid = :zoneID";
  $statement = $db->prepare($strSQL);

  $statement->bindValue(':zoneID',$zoneid);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    $zone = str_replace("_"," ",$arrReturn[0]['name']);
    $zone = str_replace("-"," - ",$zone);
    return $zone;
  }
  else {
    return $lang['text']['general']['unavailable'];
  }
}

function getServerUptime() {
  global $db;

  $strSQL = "SELECT value FROM server_variables WHERE `name` = 'server_start_time' LIMIT 1";
  $statement = $db->prepare($strSQL);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return dateDiff(date('Y-m-d',time()),date('Y-m-d',$arrReturn[0]['value']));

  }
  else {
    return '0 seconds';
  }
}

function dateDiff($time1, $time2, $precision = 6) {
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
 
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
 
    // Set up intervals and diffs arrays
    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();
 
    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Create temp time from time1 and interval
      $ttime = strtotime('+1 ' . $interval, $time1);
      // Set initial values
      $add = 1;
      $looped = 0;
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
        // Create new temp time from time1 and interval
        $add++;
        $ttime = strtotime("+" . $add . " " . $interval, $time1);
        $looped++;
      }
 
      $time1 = strtotime("+" . $looped . " " . $interval, $time1);
      $diffs[$interval] = $looped;
    }
    
    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
 break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
 // Add s if value is not 1
 if ($value != 1) {
   $interval .= "s";
 }
 // Add value and interval to times array
 $times[] = $value . " " . $interval;
 $count++;
      }
    }
 
    // Return string with times
    return implode(", ", $times);
}

function onlineCount() {
  global $db;

  $strSQL = "SELECT COUNT(*) FROM `accounts_sessions`";
  $statement = $db->prepare($strSQL);

  if (!$statement->execute()) {
    var_dump( $statement->errorInfo() );
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!empty($arrReturn)) {
    return $arrReturn[0]['COUNT(*)'];

  }
  else {
    return '0';
  }
}

function getRegionCount($region) {
	global $db;
		
	$s = "SELECT COUNT(*) FROM chars INNER JOIN accounts_sessions ON accounts_sessions.charid = chars.charid WHERE ";
	switch ($region) {
		case "Bastok":
			$cont = "pos_zone='246' OR pos_zone='234' OR pos_zone='235' OR pos_zone='237'";
			break;
		case "Sandoria":
			$cont = "pos_zone='230' OR pos_zone='231' OR pos_zone='232' OR pos_zone='233'";
			break;
		case "Ronfaure":
			$cont = "pos_zone='100' OR pos_zone='101' OR pos_zone='139' OR pos_zone='140' OR pos_zone='141' OR pos_zone='142' OR pos_zone='167' OR pos_zone='190'";
			break;
		case "Zulkheim":
			$cont = "pos_zone='102' OR pos_zone='103' OR pos_zone='108' OR pos_zone='193' OR pos_zone='196' OR pos_zone='248'";
			break;
		case "Windurst":
			$cont = "pos_zone='242' OR pos_zone='240' OR pos_zone='239' OR pos_zone='241' OR pos_zone='238'";
			break;
		case "Valdeaunia":
			$cont = "pos_zone='6' OR pos_zone=' 161' OR pos_zone='162' OR pos_zone='165' OR pos_zone='5' OR pos_zone='112'";
			break;
		case "Norvallen":
			$cont = "pos_zone='105' OR pos_zone='2' OR pos_zone='149' OR pos_zone='195' OR pos_zone='104' OR pos_zone='150' OR pos_zone='1'";
			break;
		case "Volbow":
			$cont = "pos_zone='113' OR pos_zone='201' OR pos_zone='212' OR pos_zone='174' OR pos_zone='128'";
			break;
		case "Sarutabaruta":
			$cont = "pos_zone='146' OR pos_zone='116' OR pos_zone='170' OR pos_zone='145' OR pos_zone='192' OR pos_zone='194' OR pos_zone='169' OR pos_zone='115'";
			break;
		case "ElshimoLL":
			$cont = "pos_zone='250' OR pos_zone='252' OR pos_zone='176' OR pos_zone='123'";
			break;
		case "ElshimoUL":
			$cont = "pos_zone='207' OR pos_zone='211' OR pos_zone='160' OR pos_zone='205' OR pos_zone='163' OR pos_zone='159' OR pos_zone='124'";
			break;
		case "Kuzotz":
			$cont = "pos_zone='209' OR pos_zone='114' OR pos_zone='168' OR pos_zone='208' OR pos_zone='247' OR pos_zone='125'";
			break;
		case "Tavnazian":
			$cont = "pos_zone='24' OR pos_zone='25' OR pos_zone='31' OR pos_zone='27' OR pos_zone='30' OR pos_zone='29'OR pos_zone='28' OR pos_zone='32' OR pos_zone='26'";
			break;
		case "Fauregandi":
			$cont = "pos_zone='207' OR pos_zone='211' OR pos_zone='160' OR pos_zone='205' OR pos_zone='163' OR pos_zone='159' OR pos_zone='124'";
			break;
		case "Qufim":
			$cont = "pos_zone='127' OR pos_zone='184' OR pos_zone='157' OR pos_zone='126' OR pos_zone='179' OR pos_zone='158'";
			break;
		case "Jeuno":
			$cont = "pos_zone='246' OR pos_zone='244' OR pos_zone='245' OR pos_zone='243'";
			break;
		case "Gustaberg":
			$cont = "pos_zone='191' OR pos_zone='173' OR pos_zone='106' OR pos_zone='143' OR pos_zone='107' OR pos_zone='144' OR pos_zone='172'";
			break;
		case "TuLia":
			$cont = "pos_zone='181' OR pos_zone='180' OR pos_zone='130' OR pos_zone='178' OR pos_zone='177'";
			break;
		case "LiTelor":
			$cont = "pos_zone='153' OR pos_zone='202' OR pos_zone='154' OR pos_zone='251' OR pos_zone='122' OR pos_zone='121'";
			break;
		case "Kolshushu":
			$cont = "pos_zone='4' OR pos_zone='44' OR pos_zone='118' OR pos_zone='213' OR pos_zone='3' OR pos_zone='198' OR pos_zone='249' OR pos_zone='117'";
			break;
		case "Aragoneu":
			$cont = "pos_zone='152' OR pos_zone='7' OR pos_zone='8' OR pos_zone='151' OR pos_zone='200' OR pos_zone='119' OR pos_zone='120'";
			break;
		case "Derfland":
			$cont = "pos_zone='147' OR pos_zone='197' OR pos_zone='109' OR pos_zone='148' OR pos_zone='110'";
			break;
		case "Movalpolos":
			$cont = "pos_zone='13' OR pos_zone='12' OR pos_zone='11'";
			break;
		default:
		$cont = "pos_zone='230' OR pos_zone='231'";
			break;
	}
		
	$strSQL = $s.$cont;

  $statement = $db->prepare($strSQL);
  if (!$statement->execute()) {
    var_dump($statement->errorInfo());
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
  if (!empty($arrReturn)) {
    return $arrReturn[0]['COUNT(*)'];
  }
  else {
    return 0;
  }
}

function totalRegionCount($region) {
  global $db;
  
  $strSQL = 'SELECT COUNT(*) FROM accounts_sessions JOIN chars ON chars.charid=accounts_sessions.charid WHERE '.$region.'';
  $statement = $db->prepare($strSQL);
  $statement->bindValue(':region',$region);
  
  if (!$statement->execute()) {
    var_dump($statement->errorInfo());
  }
  else {
    $arrReturn = $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
  if (!empty($arrReturn)) {
    return $arrReturn[0]['COUNT(*)'];
  }
  else {
    return 0;
  }
}

function getRegionName($region) {

  $name = array(
    1 => 'Bastok',  
    2 => 'Sandoria',
    3 => 'Ronfaure',
    4 => 'Zulkheim',
    5 => 'Windurst',
    6 => 'Valdeaunia',
    7 => 'Norvallen',
    8 => 'Volbow',
    9 => 'Sarutabaruta',
    10 => 'Elshimo Lowlands',
    11 => 'Elshimo Uplands',
    12 => 'Kuzotz',
    13 => 'Tavnazian Archipelago',
    14 => 'Fauregandi',
    15 => 'Qufim',
    16 => 'Jeuno',
    17 => 'Gustaberg',
    18 => 'Tu\'Lia',
    19 => 'Li\'Telor',
    20 => 'Kolshushu',
    21 => 'Aragoneu',
    22 => 'Derfland',
    23 => 'Movalpolos'
  );
  
  return $name[$region];
  
}
?>
