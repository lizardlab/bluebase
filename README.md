# BlueBase

BlueBase is an authentication program written in PHP and Python for the purpose of providing an easy and lightweight username/password authentication system for OpenVPN, specifically for my circumvention project known as BlueBust. [BlueBust](https://lizard.company/boards/lcrad/2-bluebust-setup) is a combination of OpenVPN, stunnel, and iptables (with an optional BIND server) to create a robust filter circumvention system that was specifically targeted at BlueCoat. BlueBase is an extension of this body of work as the original BlueBust system relied on a Public Key Infrastructure in order to authenicate users, which is slightly more secure than username/password combinations, but has a higher administrative burden. This new system allows temporary disabling and automatic expiration of users, which allows for monetization or at least more control to make sure users do not abuse the service.

Passwords are salted and hashed using Bcrypt, and the expiration date is NULLABLE but requires ISO 8601 format if used. The dashboard also comes with all the necessary javascript and css files within the directory so there is no need to make any external requests and possibly require unencrypted content if you run it over SSL. The dashboard allows you to create, edit, delete, and examine all the users and gives a nice little pie chart of the percentage of users enabled, disabled, and expired. **WARNING:** The dashboard is completely unprotected so it is highly recommended that you first run over SSL (to prevent password sniffing) and secure access to the directory using an .htpasswd file.

*Published: 2015-08-19*

## What this means

Much more scalability and ease of administration to run any OpenVPN server when implemented. Allows people to make money off of their OpenVPN service by adding the capability to automatically expire users, but it is a completely optional field. Gives more privacy to users, because deleting the record removes every trace of that user but using easy-rsa a revoked user must be permanently accounted for within the system.

## Setting up

These instructions are designed for Debian 8 systems. Debian 7 systems could work with this system, however the PHP version does not support password hashing, which means an extra file needs to be included wherever the password_hash() method is used.

Run the following command:

`sudo apt-get install python python-bcrypt ntp php5 php5-mysql apache2 openvpn easy-rsa`

OpenVPN and other configurations are not detailed here, look to the BlueBust setup for specific instructions on how to setup a BlueBust instance.

1. Create a database and import the schema into the database, then configure the parameters located within the *auth.py* file and the *config.json* file to reflect the database and respective user.
2. Place the *auth.py* file in the */etc/openvpn/* directory and place the contents of the *frontend* directory in your webroot or in a directory within the webroot. (Webroot on Debian is */var/www/*)
3. Give all ownership to the group/user www-data. `sudo chown -R www-data:www-data /var/www/`
4. Add the following lines to your openvpn config to switch from using certificate based authentication to username/password.
```
auth-user-pass-verify auth.py via-file
client-cert-not-required
username-as-common-name
```

## TODO
 * Create more specific instructions on how to configure the openvpn file to make sure it is a secure authencation still.
 * Stress testing
 * Login system for the dashboard itself.
 * Adding support for multiple databases.
 * Adding an install script to lessen the installation burden.

## License

This program is released under [GPLv3](https://www.gnu.org/licenses/gpl.html).
