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

if (isset($_REQUEST['del'])) $action = 'del';

$extData = isset($_REQUEST['extData']) ? $_REQUEST['extData'] :  '';


$label = isset($_REQUEST['label']) ? $_REQUEST['label'] :  '';
$description = isset($_REQUEST['description']) ? $_REQUEST['description'] :  '';
$extdisplay = isset($_REQUEST['extdisplay']) ? $_REQUEST['extdisplay'] :  '';

$phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] :  '';

if (isset($_REQUEST['goto0']) && $_REQUEST['goto0']) {
	$dest = $_REQUEST[ $_REQUEST['goto0'].'0' ];
}


switch ($action) {
	case 'add':
		sccp_add_extension($extData);
		needreload();
		redirect_standard();
	break;
	case 'edit':
		sccp_edit_extension($extData);
		needreload();
		redirect_standard('extdisplay');
	break;
	case 'del':
		sccp_delete_extension($extdisplay);
		needreload();
		redirect_standard();
	break;
}

?>
</div>

<div class="rnav"><ul>


<?php

echo '<li><a href="config.php?display=sccp_extensions&amp;type='.$type.'">'._('Add Extension').'</a></li>';

foreach (sccp_list_extensions() as $row) {
	echo '<li><a href="config.php?display=sccp_extensions&amp;type='.$type.'&amp;extdisplay='.$row['name'].'" class="">' .$row['name'].' - '.$row['label'] . ' (' .$row['device'] . ') </a></li>';
}
echo '<li>&nbsp;</li>';

$row_temp = sccp_list_extensions_wo_device();
if ( count($row_temp) > 0 ) {
	echo '<li>'._('Extensions without device associated').'</li>';
	foreach (sccp_list_extensions_wo_device() as $row) {
		echo '<li><a href="config.php?display=sccp_extensions&amp;type='.$type.'&amp;extdisplay='.$row['name'].'" class="">' .$row['name'].' - '.$row['label'] .  ' ( ) </a></li>';
	}
}

?>
</ul></div>

<div class="content">




<?php
//var_dump( $_REQUEST );



if ($extdisplay) {
	// load
	$row = sccp_get_extension($extdisplay);
	$extData = sccp_get_extension_full($extdisplay);


	$label = $row['label'];
	$description = $row['description'];
	
	$row_dev = sccp_get_dev_assoc($extdisplay);
	
	$phone = $row_dev['device'];
	
	$delURL = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&action=del';
    $tlabel_del = sprintf(_("Delete Extension %s"),$extdisplay);
    $label_del = '<span>&nbsp;&nbsp;&nbsp;<img width="16" height="16" border="0" title="'.$tlabel_del.'" 
	alt="" src="images/user_delete.png"/>&nbsp;
	<a href="'.$delURL.'">'.$tlabel_del.'</a></span>';
	echo $label_del;
	
	

	echo "<h2>&nbsp;&nbsp;&nbsp;"._("SCCP Extension: ")."$extdisplay "."</h2>";
} else {
	echo "<h2>&nbsp;&nbsp;&nbsp;"._("Add SCCP Extension")."</h2>";
}

?>

<form name="edit_sccp_extensions" action="<?php  $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return check_sccp_extension(edit_sccp_extensions);">

	<input type="hidden" name="extdisplay" value="<?php echo $extdisplay; ?>">
	<input type="hidden" name="action" value="<?php echo ($extdisplay ? 'edit' : 'add'); ?>">
<table>
<tr><td width="50px">&nbsp;</td>
	<td>
	<table>
	<tr><td colspan="3"><h5><?php  echo ($extdisplay ? _("Edit Extension") : _("Add Extension")) ?><hr></h5></td></tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("SCCP Extension")?>:<span><?php echo _("Must match a FreePBX Extension.")?></span></a></td>
		<td><input size="6" type="text" maxlength="10" name="extData[name]" id="extension" value="<?php  echo $extData['name'] ?>"></td>
	</tr>



	<tr> 
    	<td><a href="#" class="info"><?php echo _("Label")?>:<span><?php echo _("Extension Label")?></span></a></td>
 		<td>
        <input type='text' size='20' maxlength='20' name='extData[label]' value="<?php  echo $extData['label']?>" ></td> 
    </tr>

	<tr> 
    	<td><a href="#" class="info"><?php echo _("Description")?>:<span><?php echo _("Extension description. ")?></span></a></td>
 		<td>
        <input type='text' size='20' maxlength='20' name='extData[description]' value="<?php  echo $extData['description']?>" ></td> 
    </tr>

    
<?php
if ($extdisplay) {

	echo "<tr><td colspan='3'><h5>".(_('Associated Phone') )."<hr></h5></td></tr>";
	echo "<tr>";
	echo "	<td><a href='#' class='info'>".(_('SCCP Phone')).":<span>".(_('SCCP Phone associated: SEPXXXXXXXXXXXX'))."</span></a></td>";
	echo "	<td>";
	echo "<a href=config.php?display=sccp_devices&type=setup&devdisplay=".$phone.">".$phone."</a>";
	echo "</td></tr>";

}
?>    


	<tr><td colspan="2"><h5><?php  echo "<br>"; echo (_("Extension Properties")); ?><hr></h5></td></tr>
    
	<tr>
		<td><a href="#" class="info"><?php echo _("PIN")?>:<span><?php echo _("pin")?></span></a></td>
		<td><input size="6" maxlength="6" type="text" name="extData[pin]" id="extData[pin]" value="<?php  echo $extData['pin']?>" /></td> 
     </tr>
    
	<tr>
		<td><a href="#" class="info"><?php echo _("context")?>:<span><?php echo _("SCCP context this extension will send calls to. Only change this if you know what you are doing")?></span></a></td>
		<td><input size="20" maxlength="20" type="text" name="extData[context]" id="extData[context]" value="<?php  echo ($extData['context'] ? $extData['context']  : "from-internal-xfer" )?>" /></td> 
     </tr>
    
	<tr>
		<td><a href="#" class="info"><?php echo _("incominglimit")?>:<span><?php echo _("Inbound calls limit. Sugested value is 2.")?></span></a></td>
		<td><input size="6" maxlength="6" type="text" name="extData[incominglimit]" id="extData[incominglimit]" value="<?php echo ($extData['incominglimit'] ? $extData['incominglimit']  : "2" ) ?>" /></td> 
     </tr>
    
	<tr>
		<td><a href="#" class="info"><?php echo _("mailbox")?>:<span><?php echo _("Mailbox for this device. This should not be changed unless you know what you are doing.")?></span></a></td>
		<td><input size="6" maxlength="6" type="text" name="extData[mailbox]" id="extData[mailbox]" value="<?php echo( $extData['mailbox'] != $extData['name'] ? $extData['mailbox'] : "" ) ?>" /></td> 
     </tr>
    
	<tr>
		<td><a href="#" class="info"><?php echo _("vmnum")?>:<span><?php echo _("Asterisk dialplan extension to reach voicemail for this device. Some devices use this to auto-program the voicemail button on the endpoint. If left blank, the default vmexten setting is automatically configured by the voicemail module. Only change this on devices that may have special needs.")?></span></a></td>
		<td><input size="6" maxlength="6" type="text" name="extData[vmnum]" id="extData[vmnum]" value="<?php echo ($extData['vmnum'] ? $extData['vmnum']  : "*97" ) ?>" /></td> 
     </tr>


    
    
    

	<tr> 
    	<td><a href="#" class="info"><?php echo _("echocancel")?>:<span><?php echo _("Sets the phone echocancel for this line. Default is On")?></span></a></td>
 		<td>
        <select name="extData[echocancel]" id="extData[echocancel]">
		    <option value="on" <?php if ($extData['echocancel']=="on") echo "selected='selected'" ?> >On</option>
		    <option value="off" <?php if ($extData['echocancel']=="off") echo "selected='selected'" ?> >Off</option>
  	    </select>
    </tr>

	<tr> 
    	<td><a href="#" class="info"><?php echo _("silencesuppression")?>:<span><?php echo _("Sets the phone silence suppression for this line. Default is Off")?></span></a></td>
 		<td>
        <select name="extData[silencesuppression]" id="extData[silencesuppression]">
		    <option value="off" <?php if ($extData['silencesuppression']=="off") echo "selected='selected'" ?> >Off</option>
		    <option value="on" <?php if ($extData['silencesuppression']=="on") echo "selected='selected'" ?> >On</option>
  	    </select>
    </tr>

	<tr> 
    	<td><a href="#" class="info"><?php echo _("callgroup")?>:<span><?php echo _("Callgroup(s) that this device is part of, can be one or more callgroups, e.g. '1,3-5' would be in groups 1,3,4,5.")?></span></a></td>
 		<td>
        <input type='text' size='20' maxlength='20' name='extData[callgroup]' value="<?php  echo $extData['callgroup']?>" ></td> 
    </tr>

	<tr> 
    	<td><a href="#" class="info"><?php echo _("pickupgroup")?>:<span><?php echo _("Pickupgroups(s) that this device can pickup calls from, can be one or more groups, e.g. '1,3-5' would be in groups 1,3,4,5. Device does not have to be in a group to be able to pickup calls from that group.")?></span></a></td>
 		<td>
        <input type='text' size='20' maxlength='20' name='extData[pickupgroup]' value="<?php  echo $extData['pickupgroup']?>" ></td> 
    </tr>


	<tr><td colspan="2"><h5><?php  echo "<br>"; echo (_("Language")); ?><hr></h5></td></tr>
	<tr> 
    	<td><a href="#" class="info"><?php echo _("Language Code")?>:<span><?php echo _("?This will cause all messages and voice prompts to use the selected language if installed.")?></span></a></td>
 		<td>
        <input type='text' size='20' maxlength='20' name='extData[language]' value="<?php  echo $extData['language'] ?>" ></td> 
    </tr>




	<tr>
    	<td>&nbsp;</td>
    </tr>
        
    
	<tr>
		<td colspan="3"><br><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>">
		</td>
	</tr>
   </table>
  </td>
  </tr>
</table>
</form>

<?php echo add_free_space(7); ?>


<script language="javascript">

function check_sccp_extension(theForm) {
	var msgInvalidExtension = "<?php echo _('Invalid SCCP Extension specified'); ?>";

	// set up the Destination stuff
	setDestinations(theForm, '_post_dest');

	// form validation
	defaultEmptyOK = false;
	if (isEmpty(theForm.extension.value))
		return warnInvalid(theForm.extension, msgInvalidExtension);

	if (!validateDestinations(theForm, 1, true))
		return false;

	return true;
}

</script>
