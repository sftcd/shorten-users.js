# shorten users.js

[YAMon3](http://usage-monitoring.com/index.php) is a nice device and bandwidth
monitoring tool for OpenWRT and similar operating systems. It works based on
iptables and creates a users.js file that records MAC and IP addresses 
so those can be named and presented more nicely to the network owner/admin.

This code is to shorten a users.js that's grown overly big.
My users.js file has grown to 1926293 bytes in less than
2 months. I'm guessing that's down to temporary IPv6 addresses, the duplication
also seems to cause the tool to lose track of the IPv4 addresses, presumably
as those are way up-file or something. (Yeah, I need to go read the bash
scripts that do the work, but that's for another day;-)

user.js lives in the YAMon3 \_wwwPath directory determined by the config.file
used at runtime. That file seems to grow based on iptables (and ip6tables).
It's also changed based on browser action, which is sometimes confusing (to me
at least;-).  The reason is that the users.js content is also written to HTML
LocalStorage in the browser(s) with which you use YAMon3. That can cause some
confusion, though there is a way to import/export that from LocalStorage to
disk (via the tool's JS).

I'll likely have to play with how to get the abbreviated/fix user.js into use
effectively.

This is a not yet working work-in-progress.
