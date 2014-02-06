SCCP-Manager v. 1.0 for FreePBX


Introduction.

	This module has been developed to help IT Staff with their Asterisk-Cisco infrastructure deployment, 
providing easily provisioning and managing Cisco IP phones and extensions in a similar way as it does with Cisco CallManager.

Advantages.

As we are using SCCP Channel, no SIP firwmare upgrade is needed for each phone, saving a lot of time 
and money (you can not come back from SIP to SCCP under CallManager without paying new licenses).  

If you are thinking to migrate from CallManager to Asterisk (or did it), SCCP-Manager allows you 
to administer SCCP extensions and a wide range of Cisco phone types (including IP Communicator).
You can control phone buttons (depending on the phone model) assigning multiple lines, speeddials and BLF’s.
And you can also reset phones from the module GUI.


Previous Requirements.

- Chan-SCCP V4.0.0 channel driver for Asterisk (http://chan-sccp-b.sourceforge.net/)
- TFTP Server running under /tftpboot/

Previous Requirements.

- Chan-SCCP V4.0.0 channel driver for Asterisk (http://chan-sccp-b.sourceforge.net/)
- TFTP Server running under /tftpboot/


Module installation:

1. Download module into your local system.
2. Goto FreePBX Admin -> Module Admin.
3. Click Upload Modules.
4. Browse to the location of the module on your computer and select Upload.
5. Click Manage Local Modules.
6. Find and click SCCP Manager. Check Install. Click Process button.
7. Confirm installation.
8. Close Status window.
9. Apply Config to FreePBX.
10. Two new forms are available in Applications:
    -   SCCP Extension.
    -   SCCP Phones.
11. Go to Applications -> Extensions and set up a new Custom Extension (e.g.):
    - User Extension: 7777
    - Display Name:  IT Service
    - Dial: 7777/SCCP
12. Submit Changes and Apply Config.
11. Go to Applications -> SCCP Phones.
12. Add the phone with its MAC address, type and the freepbx extension.
13. Submit Changes and you are done!

