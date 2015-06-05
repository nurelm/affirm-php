# Web environment for testing

This is a *very* simple web page that you can use to test your installation
by expediting the fairly manual task of getting a `checkout_token`. This
directory should be installed on the root of an Apache/PHP web host. This site
must use HTTPS (it may be self-signed) and the server should be able to recieve
a webhook from Affirm.

If using this, please copy the `webconfig.php.default` to `webconfig.php` and
update the values to match your installation so that you can properly
authenticate the site integration side to harvest your `checkout_token`s.
