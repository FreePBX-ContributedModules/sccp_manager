<?php
/**
 * @copyright Javier de la Fuente, EEZ CSIC
 * @license GPL2
 *
 *
 *
 */


function sccp_reset_phone($name) {
	global $astman; 

	$astman->send_request("Command", array("Command" => "sccp reset ".$name));
		
}
 
function sccp_list_devices() {
	global $db;
	
	$sql = "SELECT b.device, RIGHT(b.device,12) AS mac, b.name AS ext, d.type  
				FROM buttonconfig b LEFT JOIN sccpdevice d ON b.device = d.name
				WHERE b.type='line' ORDER BY b.name";	
	$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage()."<br><br>Error selecting from buttonconfig, sccpdevice");
	}
	return $results;
}

function sccp_list_extensions() {
	global $db;
	

	$sql = "SELECT sccpline.name, sccpline.label, buttonconfig.device 
				FROM sccpline, buttonconfig 
				WHERE sccpline.name=buttonconfig.name 
				ORDER BY sccpline.name";
					
	$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage()."<br><br>Error selecting from sccpline");
	}
	return $results;
}

function sccp_list_devices_wo_extension() {
	global $db;

	$sql = "SELECT name, type 
				FROM sccpdevice 
				WHERE name NOT IN (select device from buttonconfig where type='line')
				ORDER BY name";
			
	$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage()."<br><br>Error selecting from sccpdevice");
	}
	return $results;

}



function sccp_list_extensions_wo_device() {
	global $db;
	

	$sql = "SELECT name, label 
				FROM sccpline 
				WHERE name NOT IN (select name from buttonconfig where type='line')
				ORDER BY name";
			
	$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($results)) {
		die_freepbx($results->getMessage()."<br><br>Error selecting from sccpline");
	}
	return $results;
}

function sccp_get_device($device) {
	global $db;
	
	$sql = "SELECT RIGHT(name,12) AS mac, type, description 
			FROM sccpdevice 
			WHERE name='$device'";

	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from buttonconfig, sccpdevice");
	}

	return $row;
}

function sccp_get_device_full($device) {
	global $db;
	
	$sql = "SELECT * 
			FROM sccpdevice 
			WHERE name='$device'";

	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from sccpdevice");
	}

	return $row;
}


function sccp_get_extension($extension) {
	global $db;
	
	$extension = (int) $extension;

	$sql = "SELECT label, description  
				FROM sccpline
				WHERE name=$extension ";	

				
	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from sccpline");
	}

	return $row;
}

function sccp_get_extension_full($extension) {
	global $db;
	
	$sql = "SELECT * 
			FROM sccpline 
			WHERE name='$extension'";

	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from sccpline");
	}

	return $row;
}




function sccp_get_buttons_assoc($device) {
	global $db;
	
	$sql = "SELECT instance, type, name as extension
		FROM buttonconfig 
		WHERE device='$device' 
		ORDER BY instance";
		
	$res = mysql_query($sql);		
	
	$num_row = 0;
	
	while ($row[$num_row] = mysql_fetch_assoc($res)) {
		$num_row++;
	}

	return $row;

	
}


function sccp_get_ext_assoc($device) {
	global $db;
	
	$sql = "SELECT name as extension
		FROM buttonconfig 
		WHERE device='$device' ";

	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from buttonconfig, sccpdevice");
	}

	return $row;
	
}


function sccp_get_dev_assoc($extension) {
	global $db;
	
	$extension = (int) $extension;

	$sql = "SELECT device 
		FROM buttonconfig 
		WHERE name='$extension' ";

	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from buttonconfig, sccpdevice");
	}

	return $row;
	
}



function get_buttons_devtype($type) {
	global $db;

	$res = mysql_query("SELECT dns, buttons
						FROM sccpdevmodel
						WHERE model='$type' ");
						
	while ($row = mysql_fetch_row($res)) {
		$modelData['dns'] = $row[0];
		$modelData['buttons'] = $row[1];
	}
	return $modelData;

}

function get_speeddial_extension($device, $instance){
	global $db;

	$sql = "SELECT name 
		FROM buttonconfig 
		WHERE device='$device' AND instance='$instance' ";

	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from buttonconfig");
	}

	list ($description, $extension, $context) = explode(",",$row['name']);
	return $extension;	
	
}


function make_speeddial_string($extension, $type){
	global $db;
	$sdString='';

	$sql = "SELECT description 
			FROM devices 
			WHERE id='$extension' ";

	$res = $db->getone($sql);
	if(DB::IsError($res)) {
		die_freepbx($res->getMessage().$sql);
	}

	$description = $res;

	$sdString = ($description != '') ? "$description,$extension,$extension" : "$extension,$extension,$extension";
	$sdString .= ($type == "blf") ? "@default" : "@internal" ;

	return $sdString;	
	
}



function sccp_add_device($devData, $buttonData) {
	global $db;
	

	if ( strpos($devData['name'],"SEP") === false )
		$devData['name'] = "SEP".$devData['name'];
	
    foreach ($devData as $Campo => $Valor) $ListaCampos .= ($ListaCampos ? "," : "")."$Campo=".(trim($Valor)!="" ? "\"$Valor\"" : "null");
	$sql = "INSERT INTO sccpdevice set $ListaCampos";
	
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
	
	$numButton = 0;
	while ( isset($buttonData['button'.$numButton])  ) {
		$buttonData['button'.$numButton] = trim($buttonData['button'.$numButton] );

			switch ($buttonData['type'.$numButton]) {

				case ('line'):
					if (!empty($buttonData['button'.$numButton])) {

					$sql = "SELECT * from sccpline
					WHERE name = '{$buttonData['button'.$numButton]}' ";
			
					$result = mysql_query($sql);
					if(DB::IsError($result)) {
						die_freepbx($result->getMessage().$sql);
					}
			
					$num_rows = mysql_num_rows($result);
					if ($num_rows == 0 ) {
						$sql = "INSERT INTO sccpline
								(id, name, label, description, mailbox, cid_num)
								VALUES ('{$buttonData['button'.$numButton]}', '{$buttonData['button'.$numButton]}', 'Extension {$buttonData['button'.$numButton]}', 'Line {$buttonData['button'.$numButton]}', '{$buttonData['button'.$numButton]}', '{$buttonData['button'.$numButton]}' )";
					
						$result = $db->query($sql);
						if(DB::IsError($result)) {
							die_freepbx($result->getMessage().$sql);
						}
					}
					

						$sql = "INSERT INTO buttonconfig
								(device, instance, type, name)
								VALUES ('{$devData['name']}', $numButton+1, 'line', '{$buttonData['button'.$numButton]}')";
		
						$result = $db->query($sql);
						if(DB::IsError($result)) {
							die_freepbx($result->getMessage().$sql);
						}
					}

				break;
				case ('speeddial'):
				case ('blf'):
				
					if (!empty($buttonData['button'.$numButton])) {
					
					$sdString = make_speeddial_string($buttonData['button'.$numButton], $buttonData['type'.$numButton]);
					$buttonData['type'.$numButton]="speeddial";
					} else {
						$buttonData['type'.$numButton]="empty";
						$sdString = "";
					}
					$sql = "INSERT INTO buttonconfig
							(device, instance, type, name)
							VALUES ('{$devData['name']}', $numButton+1, '{$buttonData['type'.$numButton]}', '$sdString')";
				
					$result = $db->query($sql);
					if(DB::IsError($result)) {
						die_freepbx($result->getMessage().$sql);
					}
					
				break;
				default:
				
					if ($buttonData['type'.$numButton]=="empty") { $buttonData['button'.$numButton]=NULL; };
					$sql = "INSERT INTO buttonconfig
							(device, instance, type, name)
							VALUES ('{$devData['name']}', $numButton+1, '{$buttonData['type'.$numButton]}', '{$buttonData['button'.$numButton]}')";
				
					$result = $db->query($sql);
					if(DB::IsError($result)) {
						die_freepbx($result->getMessage().$sql);
					}
					
				break;			
				
		} 
		
		$numButton++;		
	}  
	
	sccp_create_tftp_SEP($devData['name']);			
	
}


function sccp_add_extension($extData) {
	global $db;
	
	$extData['id'] = $extData['name'];
	$extData['cid_num'] = $extData['name'];
	$extData['mailbox'] = ( !$extData['mailbox'] ? $extData['name'] : "NULL");

	foreach ($extData as $Campo => $Valor) {
		if (!empty($Valor))
		$ListaCampos .= ($ListaCampos ? "," : "")."$Campo=".(trim($Valor)!="" ? "\"$Valor\"" : "null");
	}
	$sql = "INSERT INTO sccpline set $ListaCampos";
	
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}

}


function sccp_edit_device($devData, $buttonData) {
	global $db;

	$devData['name'] = "SEP".$devData['name'];

	$sql = "DELETE FROM buttonconfig
			WHERE device = '{$devData['name']}' ";

	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}


	$sql = "DELETE FROM sccpdevice
			WHERE name = '{$devData['name']}' ";

	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}

	sccp_delete_tftp_SEP($devData['name']);
	
	sccp_add_device($devData, $buttonData);
		
}


function sccp_edit_extension($extData) {
	global $db;

	$extData['id'] = $extData['name'];
	$extData['mailbox'] = ( !$extData['mailbox'] ? $extData['name'] : "NULL");
	$extData['cid_num'] = $extData['name'];

    foreach ($extData as $Campo => $Valor) $ListaCampos .= ($ListaCampos ? "," : "")."$Campo=".(trim($Valor)!="" ? "\"$Valor\"" : "null");
	$sql = "UPDATE sccpline set $ListaCampos
			WHERE name = '{$extData['name']}' ";
	
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}
	
}



function sccp_delete_device($device) {
	global $db;


	$sql = "DELETE FROM buttonconfig
					WHERE device = '$device'";

	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}

	$sql = "DELETE FROM sccpdevice
					WHERE name = '$device'";
	
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}

	sccp_delete_tftp_SEP($device);

}



function sccp_delete_extension($extension) {
	global $db;

	$sql = "DELETE FROM buttonconfig
					WHERE name = '{$extension}'";

	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}

	$sql = "DELETE FROM sccpline
					WHERE name = '{$extension}'";
	
	$result = $db->query($sql);
	if(DB::IsError($result)) {
		die_freepbx($result->getMessage().$sql);
	}

}


function sccp_get_model_data(){
	global $db;
		
	$res = mysql_query("SELECT model, dns, buttons
						FROM sccpdevmodel
						ORDER BY model ");
						
	while ($row = mysql_fetch_row($res)) {
		$modelData['model'][] = $row[0];
		$modelData['dns'][] = $row[1];
		$modelData['buttons'][] = $row[2];
	}
	return $modelData;
}

function make_speeds($speeds, $type = '7960'){
	global $db;
	$str = '';

	$speeds_arr = explode(';', $speeds);
	foreach ($speeds_arr as $speed){
		$speed_arr = explode(',', $speed);
		$speed = $speed_arr[0];
		$desc = $speed_arr[1];

		$speed = $db->escapeSimple($speed);

		if (!$desc){
			$sql = "SELECT d.`description`
							FROM devices d
							WHERE d.`id` = '$speed'";

			$res = $db->getone($sql);
			if(DB::IsError($res)) {
				die_freepbx($res->getMessage().$sql);
			}

			$desc = $res;
		}

		if ($desc != ''){
			$desc = str_replace(array(',', ';'), '', $desc);
			$str .= "$speed,$desc";
			if ($type != '7905' && $type != '7912')
				$str .= ",$speed@from-internal";
		}else{
			$str .= "$speed,$speed";
		}


		$str .= ';';
		$desc = '';
	}
	return $str;
}

function sccp_manager_create_device($mac, $ext, $type, $name, $speeds){
	global $db;

	$sep = 'SEP' . $db->escapeSimple(strtoupper($mac));

	$ext = (int) $ext;
	$type = $db->escapeSimple($type);
	$name = $db->escapeSimple($name);
	$speeds = make_speeds($speeds, $type);

	$sql = "SELECT COUNT(*)
					FROM sccpdevice_eez
					WHERE name = '$sep'";

	$res = $db->getone($sql);
	if(DB::IsError($res)) {
		die_freepbx($res->getMessage().$sql);
	}

	if ($res > 0){
		$sql = "UPDATE sccpdevice_eez
						SET type = '$type', autologin = $ext, description = '$name', speeddial = '$speeds'
						WHERE name = '$sep'";
	}else{
		$sql = "INSERT INTO sccpdevice_eez
						(type, autologin, description, speeddial, name)
						VALUES ('$type', $ext, '$name', '$speeds', '$sep')";
	}

	$res = $db->query($sql);
	if(DB::IsError($res)) {
		die_freepbx($res->getMessage().$sql);
	}

	return $sep;
}

function sccp_create_tftp_SEP($device, $cm_ip = ''){
	if ($cm_ip == ''){
		
		$ip_list = exec('grep -i bindaddr /etc/asterisk/sccp.conf');
		$cm_ip = explode(' = ',$ip_list);
		$asterisk_ip = $cm_ip[count($cm_ip)-1]; 
	}


	$template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/admin/modules/sccp_manager/SEPXML.txt');

	$template = str_replace('{$cm_ip}', $asterisk_ip, $template);

	$filename = '/tftpboot/' . $device . '.cnf.xml';
	file_put_contents($filename, $template);

	return true;
}


function sccp_delete_tftp_SEP($device){
	
	$filename = '/tftpboot/' . $device . '.cnf.xml';
	$command = 'rm -f '.$filename;
	exec($command); 

}



function add_free_space($lines){
	$cad = "";

	$lines = (int) $lines;
	for ($i=0; $i <= $lines; $i++) {
		$cad .= "<br />";
	}
	return $cad;
}

function tras_button_data($b_Data){
	
	$num_bt = count($b_Data);
	
	for ($i=0; $i < $num_bt; $i++) {
		$buttonData['instance'.$i] = $b_Data[$i]['instance'];
		$buttonData['type'.$i] = $b_Data[$i]['type'];
		$buttonData['button'.$i] = $b_Data[$i]['extension'];
	}

	return $buttonData;
}


function get_properties_in_button($device, $instance) {
	global $db;
	
	$sql = "SELECT type, name 
		FROM buttonconfig 
		WHERE device='$device' AND instance='$instance' ";

	$row = $db->getRow($sql, DB_FETCHMODE_ASSOC);
	if(DB::IsError($row)) {
		die_freepbx($row->getMessage()."<br><br>Error selecting row from buttonconfig, sccpdevice");
	}

	if (strpos($row['name'],"default")) {
		$row['type']= "blf";
	}

	if ( (strpos($row['name'],"default")) || (strpos($row['name'],"internal")) ) {
		
		list ($description, $row['name'], $context) = explode(",",$row['name']);

	}

	return $row;
}

?>
