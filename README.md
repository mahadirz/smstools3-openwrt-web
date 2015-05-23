SMS Tools3 Web
=====================

Web interface for [SMS Tools 3](http://smstools3.kekekasvi.com/) that should works 
on OpenWrt. 

Screenshot
-------------------

![alt tag](https://github.com/mahadirz/smstools3-openwrt-web/raw/master/screenshot.png)

Frameworks Used
-------------------

✔ [Swiftlet](http://swiftlet.org/)  
✔ Smarty

Features
-------------------

✔ SMS inbox     
✔ SMS Sent Box      
✔ SMS outgoing  
✔ USSD  
✔ Compose SMS  
✔ Templates/Draft  


Installation
------------
**OpenWrt**

*  smstools3 must be installed first ([https://github.com/mahadirz/smstools3-openwrt](https://github.com/mahadirz/smstools3-openwrt))
*  [Install php](http://wiki.openwrt.org/doc/howto/php)
*  Install php5-mod-session and php5-mod-json `root@OpenWrt:/# opkg install php5-mod-session php5-mod-json`
*  Edit `/etc/php.ini` remove (uncomment) `;` for `extension=session.so` and `extension=json.so`
*  Clone (or download and extract) this repositories into your local directory
*  Having [Composer](https://getcomposer.org) installed, run `composer install`.
*  Upload `smstools3-openwrt-web` directory into openwrt `/www` directory using scp or any ftp clients
*  The app can be accessed by http://[your-router-ip]/smstools3-openwrt-web
*  The default username and password is admin

If encountered errors
------------

* Fixing: `"PHP Fatal error: strtotime(): Timezone database is corrupt"`

`root@OpenWrt:/# vim timezone /etc/php.ini`

set date.timezone = "Asia/Kuala_Lumpur"

check your timezone directory, whether it contains the continent and the city:

`root@OpenWrt:/# ls -lah /usr/share/zoneinfo/`

If you don’t have it, then you need to install the appropriate continent specific package:

`root@OpenWrt:/# opkg install zoneinfo-asia`
