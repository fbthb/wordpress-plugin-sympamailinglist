<?php

/*
Plugin Name: WP-to-Sympa-Bridge
Description: (un-)subscribe from/to a sympa mailinglist directly from Wordpress.
Version: 2.0beta
Author: fbthb
Author URI: https://github.com/fbthb
Domain path: /languages
License: Do what you want, but think about the omen.
License URI: https://www.youtube.com/watch?v=b-_wE0mJU5Y
Text Domain: wptosympa
*/

/* recommanded settings for every Sympa-List (you will find the settings in Admin -> Privileges): subscribe and unsubscribe should have the word "auth" in it.
/* before you upload this plugin: Please change every occurence of "lists.my-company.org" into your domain (line 72 & 249)*/
/* before you upload this plugin: add your mailinglists (line 195) and the labels (line 196) */
/* if you like: translate it into your own language (or write something better than my bad english) */
/* zip this file und upload it to your WordPress-Site*/


if ( ! defined( 'ABSPATH' ) ) exit;

function wptosympa_register_settings() {
	add_option( 'wptosympa_options', 'This is my option value.');
	register_setting( 'myplugin_options_group', 'wptosympa_options', 'myplugin_callback' );
}
add_action( 'admin_init', 'wptosympa_register_settings' );
function wptosympa_register_options_page() {
	add_options_page('WP to Sympa Bridge Settings', 'Sympa', 'manage_options', 'wptosympa', 'wptosympa_options_page');
}
add_action('admin_menu', 'wptosympa_register_options_page');
function wptosympa_options_page() {
    ?>
<div class="wrap">
	<h1>Settings for WP-to-Sympa</h1>
	<p>This plugin sends emails to your sympa mailing list server, make sure that the function to send mail is activated. Some plugins may cause problems. You should test as a regular user, not as a logged in user.</p>
	<p>Make sure that your sympa list has the list privilege "AUTH" in the name, so you can (un)subscribe a user by sending an email.</p>
	<h2>How to use</h2>
	<p>Use the shortcode <b>[wptosympa_form]</b> to list all sympa lists you configured (For now, you have to configure the lists inside the code).</p>
	<p>Use the shortcode <b>[wptosympa_form domain="" list=""]</b> to specify only a specific list.<br />
	<em>Example: <b>[wptosympa_form domain="lists.my-company.org" list="mylistname"]</b> is the shortcode to subscribe or unsubscribe only the list at mylistname@lists.my-company.org</em></p>

</div>
<?php
}

function wptosympa_sendMail($doaction, $listname, $mail, $name, $domain) {
	switch($doaction) {
		case "UNSUBSCRIBEALL":
			$content = "UNSUBSCRIBE * " . $mail;
			break;
		case "UNSUBSCRIBE":
			$content = "UNSUBSCRIBE " . $listname;
			break;
		case "SUBSCRIBE":
			$content = "SUBSCRIBE " . $listname . " " . $name;
			break;
	}		
	mail(
		"sympa@".$domain,
		$content,
		$content,
		'From: ' . $mail
	);
}
   
function wptosympa_form($atts) {
	ob_start();

    $atts = shortcode_atts( array(
        'domain' => "lists.my-company.org", /*Update this line*/
        'list' => null
    ), $atts, 'wptosympa_form' );

    if(isset($_POST['wptosympa_submit']) && ($_POST['wptosympa_doaction'] != "") && ($_POST['wptosympa_mail'] != "")) {
        wptosympa_sendMail(
			sanitize_text_field($_POST['wptosympa_doaction']),
            sanitize_text_field($_POST['wptosympa_art']),
            sanitize_email($_POST['wptosympa_mail']),
            sanitize_text_field($_POST['wptosympa_vorname']),
            $atts['domain']
        );
		if ($_POST['wptosympa_doaction'] == "SUBSCRIBE") {
        ?>
		
		<p style="border: 2px solid green">We have send you an email, that you have to confirm. You can only get messages of this list, if you confirm this mail.<br />If you don´t get a mail, wait a few minutes and/or check your spam-folder</p>
		<a href="<?php echo wptosympa_getCurrentUrl(); ?>">Refresh this page and subscribe to another list</a>

        <?php
		}
		else {
		?>
		
		<p style="border: 2px solid green">We have send you an email, that you have to confirm. You can only unsubscribe, if you confirm this mail.<br />If you don´t get a mail, wait a few minutes and/or check your spam-folder</p>
		<a href="<?php echo wptosympa_getCurrentUrl(); ?>">Refresh this page</a>
		
        <?php
		}
    } 
	else {
        ?>
	
		<script>
			function unsubscribeAllOrSpec() {
				document.getElementById('wptosympa_mailadress').innerHTML = "";
				document.getElementById('wptosympa_selection').innerHTML = "";
				document.getElementById('wptosympa_submit').innerHTML = "";

				elem = document.getElementById('wptosympa_button');
				elem.innerHTML = "<p>Would you like to unsubscribe from all lists or just a specifc list?</p>";
				elem.innerHTML += "<button type=\"button\" onclick=\"unsubscribeall()\" id=\"btnunsubscribeall\" style=\"padding: 16px 32px;\">unsubscribe all</button>&nbsp;";
				elem.innerHTML += "<button type=\"button\" onclick=\"unsubscribespec()\" id=\"btnunsubscribespec\" style=\"padding: 16px 32px;\">unsubscribe specific</button><br>";
				var elem_old = document.getElementById('btnunsubscribe');
				elem_old.onclick = "";
				var alt = document.getElementById('btnsubscribe');
				alt.onclick = subscribe
			}
			function unsubscribeall() {
				document.getElementById('wptosympa_mailadress').innerHTML = "";
				document.getElementById('wptosympa_selection').innerHTML = "";
				document.getElementById('wptosympa_submit').innerHTML = "";
				elem = document.getElementById('wptosympa_mailadress');
				elem.innerHTML = "<p>Please enter your mail-adress:</p>";
				wptosympa_doaction = "UNSUBSCRIBEALL"
				var elem_old = document.getElementById('btnunsubscribeall');
				elem_old.onclick = "";
				var alt = document.getElementById('btnunsubscribespec');
				alt.onclick = unsubscribespec
				mailadressform();
			}
			function unsubscribespec() {
				document.getElementById('wptosympa_selection').innerHTML = "";
				document.getElementById('wptosympa_submit').innerHTML = "";
				document.getElementById('wptosympa_mailadress').innerHTML = "";
				elem = document.getElementById('wptosympa_mailadress');
				wptosympa_doaction = "UNSUBSCRIBE"
				var elem_old = document.getElementById('btnunsubscribespec');
				elem_old.onclick = "";
				var alt = document.getElementById('btnunsubscribeall');
				alt.onclick = unsubscribeall
				getListOfLists();
			}
			function unsubscribeList() {
				document.getElementById('wptosympa_mailadress').innerHTML = "";
				document.getElementById('wptosympa_submit').innerHTML = "";
				elem = document.getElementById('wptosympa_mailadress');
				elem.innerHTML = "<p>Please enter your mail-adress:</p>";
				wptosympa_doaction = "UNSUBSCRIBE"
				var elem_old = document.getElementById('btnunsubscribe');
				elem_old.onclick = "";
				var alt = document.getElementById('btnsubscribe');
				alt.onclick = subscribeList
				mailadressform();
			}
			function subscribeList() {
				document.getElementById('wptosympa_mailadress').innerHTML = "";
				document.getElementById('wptosympa_submit').innerHTML = "";
				elem = document.getElementById('wptosympa_mailadress');
				elem.innerHTML = "<p>Please enter your mail-adress:</p>"
				wptosympa_doaction = "SUBSCRIBE"
				var elem_old = document.getElementById('btnsubscribe');
				elem_old.onclick = "";
				var alt = document.getElementById('btnunsubscribe');
				alt.onclick = unsubscribeList
				mailadressform();
			}
			function subscribe() {
				document.getElementById('wptosympa_button').innerHTML = "";
				document.getElementById('wptosympa_mailadress').innerHTML = "";
				document.getElementById('wptosympa_selection').innerHTML = "";
				document.getElementById('wptosympa_submit').innerHTML = "";
				elem = document.getElementById('wptosympa_mailadress');
				wptosympa_doaction = "SUBSCRIBE"
				var elem_old = document.getElementById('btnsubscribe');
				elem_old.onclick = "";
				var alt = document.getElementById('btnunsubscribe');
				alt.onclick = unsubscribeAllOrSpec
				getListOfLists();
			}
			function createListitem($actuallistname, $actualnicelistname, $i) {
				var elem = document.getElementById('wptosympa_fieldset');
				if ($i == 0) {
					elem.innerHTML += "<div style=\"float: left;padding: 2px;\"><input type=\"radio\" name=\"wptosympa_art\" id=\""+$actuallistname+"\" value=\""+$actuallistname+"\" checked /></div>"+
					"<div style=\"float: left;padding: 2px;\"><label for=\""+$actuallistname+"\">"+$actualnicelistname+"</label></div><div style=\"clear: both\"></div>";
				}
				else {
					elem.innerHTML += "<div style=\"float: left;padding: 2px;\"><input type=\"radio\" name=\"wptosympa_art\" id=\""+$actuallistname+"\" value=\""+$actuallistname+"\" /></div>"+
					"<div style=\"float: left;padding: 2px;\"><label for=\""+$actuallistname+"\">"+$actualnicelistname+"</label></div><div style=\"clear: both\"></div>";
				}
			}
			function getListOfLists() {

				/* Update this two lists*/
				var listarray = ['listname1', 'listname2', 'listname3'];
				var listarraylabel = ['The label for the first list', 'The label for the second list', 'The label for the third list'];


				document.getElementById('wptosympa_submit').innerHTML = "";
				var elem = document.getElementById('wptosympa_selection');
				elem.innerHTML = "<fieldset id=\"wptosympa_fieldset\"><legend>Please choose a list:</legend>";
				for (i=0; i<(listarray.length); i++) {
					createListitem(listarray[i], listarraylabel[i], i);	
				}
				elem.innerHTML += "</fieldset>";
				mailadressform();
			}
			function mailadressform() {
				document.getElementById('wptosympa_submit').innerHTML = "";
				var elem = document.getElementById('wptosympa_mailadress');
				elem.innerHTML += "<fieldset><legend>Your Data</legend><table cellpadding=\"0\" cellspacing=\"3\" width=\"100%\" style=\"border: 0px;\"><tr style=\"border: 0px;\">" + 
				"<td><label for=\"wptosympa_vorname\">Your name (optional)</label></td><td><input type=\"text\" name=\"wptosympa_vorname\" style=\"width: 100%;\" placeholder=\"John Doe\"></td>" +
                "</tr><tr style=\"border: 0px;\">" +
                    "<td><label for=\"wptosympa_mail\">Your mail-adress</label></td><td><input type=\"email\" name=\"wptosympa_mail\" id=\"wptosympa_mail\" style=\"width: 100%;\" placeholder=\"mail@example.org\" onfocusout=\"validateMail()\"></td>" +
                "</tr></table></fieldset><div id=\"wptosympa_errortext\" style=\"color: red\"></div>";
				getSubmitBtn();
			}
			function getSubmitBtn() {
				var elem = document.getElementById('wptosympa_submit');
				var label = "";
				if (wptosympa_doaction === "UNSUBSCRIBE") label = "Yes, unsubscribe";
				if (wptosympa_doaction === "UNSUBSCRIBEALL") label = "Yes, unsubscribe all";
				if (wptosympa_doaction === "SUBSCRIBE") label = "Yes, subscribe";
				elem.innerHTML += "<input type=\"hidden\" name=\"wptosympa_doaction\" value=\""+wptosympa_doaction+"\" >"
				elem.innerHTML += "<input type=\"submit\" style=\"padding: 16px 32px;\"name=\"wptosympa_submit\" value=\""+label+"\">"
			}
			function validate() {
				elem = document.getElementById('wptosympa_errortext');
				errorcount = 0;
				if (wptosympa_doaction == "") {
					elem.innerHTML += "<p>Error</p>";
					errorcount++;
				}
				var b = validateMail();
				if (errorcount == 0 && b) return true;
				return false;
			}
			function validateMail() {
				elem = document.getElementById('wptosympa_errortext');
				elem.innerHTML = "";
				if (document.getElementById('wptosympa_mail').value == "") {
					elem.innerHTML += "<p>Error: No mail-adress</p>";
					return false;
				}
				return true;
			}
		</script>

		<?php if($atts['domain'] == "lists.my-company.org" && $atts['list'] == null) { ?> <!--Update this line with the URL of your sympa-->

			<form action="<?php echo wptosympa_getCurrentUrl(); ?>" method="post" onsubmit="return(validate());">
				<button type="button" style="background-color: #006342; border: none; padding: 16px 32px; color: white;" id="btnsubscribe" onclick="subscribe()">subscribe</button>
				<button type="button" style="background-color: #ff007a; border: none; padding: 16px 32px; color: white;" id="btnunsubscribe" onclick="unsubscribeAllOrSpec()">unsubscribe</button>
				<div id="wptosympa_button"></div>
				<div id="wptosympa_selection"></div>
				<div id="wptosympa_mailadress"></div>
				<div id="wptosympa_submit"></div>
			</form>
				
        <?php } elseif($atts['list'] == null) { ?>
				<p style="border: 2px solid red">Error: There is no list. Please use [wptosympa_form domain="lists.your-company.org" list="mylistname"] </p>

        <?php } else { ?>
			<form action="<?php echo wptosympa_getCurrentUrl(); ?>" method="post" onsubmit="return(validate());">

				<button type="button" style="background-color: #006342; border: none; padding: 16px 32px; color: white;" id="btnsubscribe" onclick="subscribeList()">subscribe</button>&nbsp;
				<button type="button" style="background-color: #ff007a; border: none; padding: 16px 32px; color: white;" id="btnunsubscribe" onclick="unsubscribeList()">unsubscribe</button>
				<input type="hidden" name="wptosympa_art" value="<?php echo $atts['list']; ?>">
				<div id="wptosympa_mailadress"></div>
				<div id="wptosympa_submit"></div>
			</form>
		
        <?php 
		} 
    }

    $output = ob_get_clean();
    return $output;
}
add_shortcode("wptosympa_form", "wptosympa_form");

function wptosympa_getCurrentUrl() {
    $current_url  = 'http';

    $server_https = $_SERVER["HTTPS"];
    $server_name  = $_SERVER["SERVER_NAME"];
    $server_port  = $_SERVER["SERVER_PORT"];
    $request_uri  = $_SERVER["REQUEST_URI"];

    return $request_uri;

    if ($server_https == "on") {
        $current_url .= "s";
    }

    $current_url .= "://";

    if ($server_port != "80") {
        $current_url .= $server_name . ":" . $server_port . $request_uri;
    }
    else {
        $current_url .= $server_name . $request_uri;
    }

    return $current_url;
}
