# Prevent Brute Force Attack

This plugin is designed for blocking ips which are trying to bruteforce login via some scripts.

Even if they are not successfull they use a lot of resources as every login attempt will do multiple queries and hit the Database.

For every 10 failed login attempts in 1 minute this plugin will block the IP and for next onward request it will throw 429 (Too Many Requests) to the client. This will result in usage of very few resources of the web server.
