# WordPress-To-Sympa
Connect your Sympa-Mailing-List with your WordPress.
Give your website visitor a form to subscribe to your sympa-mailinglist (or unsubscribe) without leaving your website.

# Settings in Sympa
For every mailinglist you need to change the permissions for "subscribe" and "unsubscribe" to anything with the word "auth" in it.
You will find this settings in Admin -> Privileges.

#Settings in WordPress
Add the shortcode [wptosympa_form domain="lists.my-company.org" list="mylistname"] to your WordPress site to embed the form with the given listname and sympa-server.
If you would like to show many mailinglists, change the lines 72, 195, 196 and 249.

#Planned features
- Everything is in english at the moment. The translation to other languages is planned for a later version. (Feel free to change the code into your own language.)
- The option for many mailinglists at the same time needs changing inside the code. For a later version there will be a better way. 