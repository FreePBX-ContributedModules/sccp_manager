<?php
/** SCCP MANAGER Module for FreePBX 2.5
 * Copyright 2012 Javier de la Fuente, GT-TOIP CSIC
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'setup';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] :  '';
$RestartPhone = isset($_REQUEST['RestartPhone']) ? $_REQUEST['RestartPhone'] :  '';

if (isset($_REQUEST['del'])) $action = 'del';


$devdisplay = isset($_REQUEST['devdisplay']) ? $_REQUEST['devdisplay'] :  '';
$mac = isset($_REQUEST['mac']) ? $_REQUEST['mac'] :  '';
$extension = isset($_REQUEST['extension']) ? $_REQUEST['extension'] :  '';

$devData = isset($_REQUEST['devData']) ? $_REQUEST['devData'] :  '';
$buttonData = isset($_REQUEST['buttonData']) ? $_REQUEST['buttonData'] :  '';

if (isset($_REQUEST['goto0']) && $_REQUEST['goto0']) {
	$dest = $_REQUEST[ $_REQUEST['goto0'].'0' ];
}


if ($RestartPhone){
	sccp_reset_phone($devdisplay);		
}

//var_dump($_REQUEST);



global $astman; 
switch ($action) {
	case 'add':
		if ( $_REQUEST['Submit'] ) {
			sccp_add_device($devData, $buttonData);
			//needreload();
			redirect_standard();
		}
	break;
	case 'edit':
			sccp_edit_device($devData, $buttonData);
			$astman->send_request("Command", array("Command" => "sccp restart ".$devdisplay));
			//needreload();
			redirect_standard('devdisplay');
	break;
	case 'del':
			sccp_delete_device($devdisplay);
			//needreload();
			redirect_standard();
	break;
}

?>

</div>

<div class="rnav"><ul>
<?php

echo '<li><a href="config.php?display=sccp_devices&amp;type='.$type.'">'._('Add Phone').'</a></li>';

foreach (sccp_list_devices() as $row) {
	echo '<li><a href="config.php?display=sccp_devices&amp;type='.$type.'&amp;devdisplay='.$row['device'].'" class="">'.$row['device'] . ' (' .$row['type'] . ') - ' .$row['ext'].'</a></li>';
}
echo '<li>&nbsp;</li>';
$row_temp = sccp_list_devices_wo_extension();
if ( count($row_temp) > 0 ) {
	echo '<li>'._('Devices without extension associated').'</li>';
	foreach (sccp_list_devices_wo_extension() as $row) {
		echo '<li><a href="config.php?display=sccp_devices&amp;type='.$type.'&amp;devdisplay='.$row['name'].'" class="">' .$row['name'].' ('.$row['type'] .  ')  </a></li>';

	}
}

?>
</ul></div>

<div class="content">

<?php
	$rowButtons = get_buttons_devtype($devData['type']);
	$Lines = $rowButtons['dns'];
	$SpeedDials = $rowButtons['buttons'];



if ($devdisplay) {
	// load
	$row = sccp_get_device($devdisplay);
	$devData = sccp_get_device_full($devdisplay);
	$rowButtons = get_buttons_devtype($devData['type']);
	$Lines = $rowButtons['dns'];
	$SpeedDials = $rowButtons['buttons'];
	$mac = $row['mac'];

if (!isset($_REQUEST['extension'])) {
	$b_Data = sccp_get_buttons_assoc($devdisplay);
	$buttonData = tras_button_data($b_Data);

	$extension = $buttonData['extension'][0];
}

	$delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=del';
    $tlabel_del = sprintf(_("Delete Phone %s"),$devdisplay);
    $label_del = '<span>&nbsp;&nbsp;&nbsp;<img width="16" height="16" border="0" title="'.$tlabel_del.'" 
	alt="" src="images/user_delete.png"/>&nbsp;
	<a href="'.$delURL.'">'.$tlabel_del.'</a></span>';
	echo $label_del;

	echo "<h2>&nbsp;&nbsp;&nbsp;"._("Edit: ")."SEP$mac (".$devData['type'].")"."</h2>";
} else {
	echo "<h2>&nbsp;&nbsp;&nbsp;"._("Add Phone")."</h2>";
}

?>

<form name="edit_sccp_devices" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return check_sccp_device(edit_sccp_devices);">
	<input type="hidden" name="devdisplay" value="<?php echo $devdisplay; ?>">
	<input type="hidden" name="action" value="<?php echo ($devdisplay ? 'edit' : 'add'); ?>" >
<table>
<tr><td width="50px">&nbsp;</td>
	<td>
    
	<table>
	<tr><td colspan="3"><h5><?php  echo ($devdisplay ? _("Edit Phone") : _("Add Phone")) ?><hr></h5></td></tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("MAC")?>:<span><?php echo _("The MAC address of the phone")?></span></a></td>
		<td><input size="12" type="text" <?php if ($devData['name']) echo 'readonly="readonly"'?> name="devData[name]" id='mac_address' value="<?php  echo substr($devData['name'], -12); ?>" maxlength="12" onchange="mayusculas()" ></td>
	</tr>


<?php
	$modelData = sccp_get_model_data();
	$numModels = count($modelData['model']);

?>
	<tr>
		<td><a href="#" class="info"><?php echo _("Type")?>:<span><?php echo _("The type of phone: 7911, 7940, 7960...")?></span></a></td>
  		<td>
        <select name='devData[type]' id='phone_type' <?php if ($devdisplay) echo 'disabled="disabled"'?> onchange="submit()" >
		<?php
    
        echo "<option value=''></option>";
        for ($i=0; $i < $numModels; $i++){
            if ($devData['type'] == $modelData['model'][$i]) {
                echo "<option value='{$modelData['model'][$i]}' selected='selected'>{$modelData['model'][$i]} </option>";
				$valToHidden = $modelData['model'][$i];
            } else {
                echo "<option value='{$modelData['model'][$i]}'>{$modelData['model'][$i]} </option>";
            }
        }
        ?>  
	    </select>
        
        <?php  
		if ($devdisplay) { 
			echo "<input type='hidden' name='devData[type]' id='phone_type' value='$valToHidden'>";
			
		}
		?>
        
      </td>
	</tr>

	<tr> 
    	<td><a href="#" class="info"><?php echo _("Description")?>:<span><?php echo _("Phone description. This text is shown in the upper right corner, close to date")?></span></a></td>
 		<td>
        <input type='text' size='20' maxlength='20' name='devData[description]' value="<?php  echo $devData['description']?>" ></td> 
    </tr>


	<tr><td colspan="3"><h5><?php  echo "<br>"; echo (_("Associated Extension & Speeddials")); ?><hr></h5></td></tr>
    
    
<?php
?>
	<tr>
		<td><a href="#" class="info"><?php echo ("Button 1")?>:<span><?php echo _("Must match a FreePBX Extension.<br>New SCCP Extensions especified will be created with defaults parameters.<br>Unused SCCP Extension won't be deleted, just disassociated from device.")?></span></a></td>
		<td><select name="<?php print "buttonData[type0]" ?>">
        		 <option value="line" <?php if ($buttonData['type0']=="line") echo "selected='selected'" ?> >Line</option>
            </select>
        </td> 
		<td><input size="6" type="text" name="<?php print "buttonData[button0]" ?>" value="<?php print $buttonData['button0']; ?>" /></td> 
     </tr>

<?php
	for ($Instance = 1; $Instance < $Lines; $Instance++){   
		$tybuData = get_properties_in_button($devdisplay,($Instance+1));

?>

	<tr>
		<td><a href="#" class="info"><?php echo ("Button ".($Instance+1))?>:<span><?php echo _("Assigned Values to the Button")?></span></a></td>
		<td><select name="<?php print "buttonData[type$Instance]" ?>">
        		 <option value="empty" <?php if ($tybuData['type']=="empty") echo "selected='selected'" ?> ></option>
        		 <option value="blf" <?php if ( ($tybuData['type']=="blf")   ) echo "selected='selected'" ?> >BLF</option>
        		 <option value="speeddial" <?php if ($tybuData['type']=="speeddial") echo "selected='selected'" ?> >SpeedDial</option>
        		 <option value="line" <?php if ($tybuData['type']=="line") echo "selected='selected'" ?> >Line</option>
            </select>
        </td> 
		<td><input size="6" type="text" name="<?php print "buttonData[button$Instance]" ?>" value="<?php print $tybuData['name']; ?>" /></td> 
     </tr>
<?php
}  // END OF LINES
?>

<?php
	$Instance = $Lines;
	for ($Instance = $Lines; $Instance < $SpeedDials; $Instance++){   
		$tybuData = get_properties_in_button($devdisplay,($Instance+1));
?>

	<tr>
		<td><a href="#" class="info"><?php echo ("Button ".($Instance+1))?>:<span><?php echo _("Assigned Values to the Button")?></span></a></td>

		<td><select name="<?php print "buttonData[type$Instance]" ?>">
        		 <option value="empty" <?php if ( ($tybuData['type']=="empty")   ) echo "selected='selected'" ?> ></option>
        		 <option value="blf" <?php if ( ($tybuData['type']=="blf")   ) echo "selected='selected'" ?> >BLF</option>
        		 <option value="speeddial" <?php if ( ($tybuData['type']=="speeddial")   ) echo "selected='selected'" ?> >SpeedDial</option>
            </select>
        </td> 

		<td><input size="6" type="text" name="<?php print "buttonData[button$Instance]" ?>" value="<?php print $tybuData['name']; ?>" /></td> 
     </tr>
<?php
}  // END OF SPEEDDIALS
?>


	<tr><td colspan="3"><h5><?php  echo "<br>"; echo (_("Device Properties")); ?><hr></h5></td></tr>

	<tr> 
    	<td><a href="#" class="info"><?php echo _("Transfer")?>:<span><?php echo _("Transfer allowed")?></span></a></td>
 		<td>
        <select name="devData[transfer]" id="devData[transfer]">
		    <option value="on" <?php if ($devData['transfer']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($devData['transfer']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>

        
	<tr> 
    	<td><a href="#" class="info"><?php echo _("cfwdall")?>:<span><?php echo _("Activate the callforward stuff and softkeys. Default is On")?></span></a></td>
 		<td>
        <select name="devData[cfwdall]" id="devData[cfwdall]">
		    <option value="on" <?php if ($devData['cfwdall']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($devData['cfwdall']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>

        
	<tr> 
    	<td><a href="#" class="info"><?php echo _("cfwdbusy")?>:<span><?php echo _("Activate the callforward stuff and softkeys. Default is On")?></span></a></td>
 		<td>
        <select name="devData[cfwdbusy]" id="devData[cfwdbusy]">
		    <option value="on" <?php if ($devData['cfwdbusy']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($devData['cfwdbusy']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>

	<tr> 
    	<td><a href="#" class="info"><?php echo _("DTMFmode")?>:<span><?php echo _("Dual-Tone Multi-Frequency: outofband is the native cisco dtmf tone play")?></span></a></td>
 		<td>
        <select name="devData[dtmfmode]" id="devData[dtmfmode]">
		    <option value="outofband" <?php if ($devData['dtmfmode']=="outofband") echo "selected='selected'" ?> >outofband</option>
		    <option value="inband" <?php if ($devData['dtmfmode']=="inband") echo "selected='selected'" ?> >inband</option>
  	    </select>
    </tr>


	<tr> 
    	<td><a href="#" class="info"><?php echo _("DND")?>:<span><?php echo _("Do Not Disturb feature. Default is On")?></span></a></td>
 		<td>
        <select name="devData[dndFeature]" id="devData[dndFeature]">
		    <option value="on" <?php if ($devData['dndFeature']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($devData['dndFeature']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>


	<tr> 
    	<td><a href="#" class="info"><?php echo _("mwilamp")?>:<span><?php echo _("Set the MWI lamp style when MWI active to on, off, wink, flash or blink")?></span></a></td>
 		<td>
        <select name="devData[mwilamp]" id="devData[mwilamp]">
		    <option value="on" <?php if ($devData['mwilamp']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($devData['mwilamp']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>

	<tr>
    	<td>&nbsp;</td>
    </tr>
        
	<tr>
		<td><a href="#" class="info"><?php echo _("Phone Load Name")?>:<span><?php echo _("Firmware version for upgrade ")?></span></a></td>
		<td><input size="20" type="text" name="devData[imageversion]" id="devData[imageversion]" value="<?php  echo $devData['imageversion']?>" /></td> 
     </tr>

	<tr>
    	<td>&nbsp;</td>
    </tr>
        
	<tr> 
    	<td><a href="#" class="info"><?php echo _("NAT")?>:<span><?php echo _("Device NAT support (default Off)")?></span></a></td>
 		<td>
        <select name="devData[nat]" id="devData[nat]">
		    <option value="off" <?php if ($devData['nat']=="off") echo "selected='selected'" ?> >Off</option>
		    <option value="on" <?php if ($devData['nat']=="on") echo "selected='selected'" ?> >On</option>
  	    </select>
    </tr>

        
	<tr> 
    	<td><a href="#" class="info"><?php echo _("directrtp")?>:<span><?php echo _("This option allow devices to do direct RTP sessions (default Off)")?></span></a></td>
 		<td>
        <select name="devData[directrtp]" id="devData[directrtp]">
		    <option value="off" <?php if ($devData['directrtp']=="off") echo "selected='selected'" ?> >Off</option>
		    <option value="on" <?php if ($devData['directrtp']=="on") echo "selected='selected'" ?> >On</option>
  	    </select>
    </tr>

        
	<tr> 
    	<td><a href="#" class="info"><?php echo _("earlyrtp")?>:<span><?php echo _("The audio strem will be open in the progress and connected state.<br>Valid options: none, progress, offhook, dial, ringout. Default may be Progress.")?></span></a></td>
 		<td>
        <select name="devData[earlyrtp]" id="devData[earlyrtp]">
		    <option value="progress" <?php if ($devData['earlyrtp']=="progress") echo "selected='selected'" ?> >Progress</option>
		    <option value="offhook" <?php if ($devData['earlyrtp']=="offhook") echo "selected='selected'" ?> >Offhook</option>
		    <option value="dial" <?php if ($devData['earlyrtp']=="dial") echo "selected='selected'" ?> >Dial</option>
		    <option value="none" <?php if ($devData['earlyrtp']=="none") echo "selected='selected'" ?> >None</option>
  	    </select>
    </tr>

	<tr>
    	<td>&nbsp;</td>
    </tr>

        
	<tr> 
    	<td><a href="#" class="info"><?php echo _("pickupexten")?>:<span><?php echo _("Enable Pickup function to direct pickup an extension. Default is On")?></span></a></td>
 		<td>
        <select name="devData[pickupexten]" id="devData[pickupexten]">
		    <option value="on" <?php if ($devData['pickupexten']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($devData['pickupexten']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>

        
	<tr>
		<td><a href="#" class="info"><?php echo _("pickupcontext")?>:<span><?php echo _("Context where direct pickup search for extensions. Default value in FreePBX is from-internal-xfer.")?></span></a></td>
		<td><input size="20" type="text" name="devData[pickupcontext]" id="devData[pickupcontext]" value="<?php  echo $devData['pickupcontext']?>" /></td> 
     </tr>

        
	<tr> 
    	<td><a href="#" class="info"><?php echo _("pickupmodeanswer")?>:<span><?php echo _("On (Default)= the call has been answered when picked up<br />Off = call manager way, the phone who picked up the call rings the call")?></span></a></td>
 		<td>
        <select name="devData[pickupmodeanswer]" id="devData[pickupmodeanswer]">
		    <option value="on" <?php if ($devData['pickupmodeanswer']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($devData['pickupmodeanswer']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>


        

	<tr>
    	<td>&nbsp;</td>
    </tr>
        

	<tr>
		<td colspan="3"><br /><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>"> 
        <?php
			if ($devdisplay) { 
        		echo '<input name="ResetPhone" id="ResetPhone" type="button" value="Reset Phone" 
						onclick="reset_phone(edit_sccp_devices)" />';
				echo "<input type='hidden' name='RestartPhone' id='RestartPhone' value=0>";

						
			}
		?>
		</td>
	</tr>
    

   </table>
  </td>
  </tr>

</table>
</form>


<?php echo add_free_space(7); ?>



<script language="javascript">

function check_sccp_device(theForm) {
	var msgInvalidMAC = "<?php echo _('Invalid MAC address specified'); ?>";
	var msgInvalidPhoneType = "<?php echo _('Must select phone type'); ?>";

	// set up the Destination stuff
	setDestinations(theForm, '_post_dest');
	
	defaultEmptyOK = false;
	
	
	if (theForm.phone_type.value==""){
		alert (msgInvalidPhoneType);
		return false;
	}
	

	if (theForm.mac_address.value.length != 12) 
			return warnInvalid(theForm.mac_address, msgInvalidMAC);

	 
	if (!validateDestinations(theForm, 1, true))
			return false;
	
	return true;
}

function reset_phone(theForm) {
	var msgResetPhone = "<?php echo _('Reset phone: '); ?>";
	var Phone = "SEP"+theForm.mac_address.value;

	if (confirm(msgResetPhone+Phone+'.  OK ?' )) {
		
		theForm.RestartPhone.value = 1;
		document.edit_sccp_devices.submit();
	
	} else {
		return false;
	}

}

function mayusculas(){

	texto = document.getElementById("mac_address").value;
	document.getElementById("mac_address").value = texto.toUpperCase();
}
 
</script>
