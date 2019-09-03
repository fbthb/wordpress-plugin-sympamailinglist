<?php

/*
Plugin Name: Sympa-Mailinglist
Description: Creates a form for subscribing to a Sympa-Mailinglist. Use the Shortcode <b>[lf_form]</b> for a defined list of mailinglists. Or use [lf_form domain="" liste=""] for just one mailinglist. Write into the Attribute domain everything behind the "@" (e.g. sympa.company-name.org) and into the attribute liste everything before the "@" (e.g. myListName).
Version: 1.1.5
Author: Madras_101
*/
/* recommanded settings for every Sympa-List (you will find the settings in Admin -> Privileges): subscribe and unsubscribe should have the word "auth" in it.
/* before you upload this plugin: Please change every occurence of "sympa.your-company.org" into your domain (line 62 & 108)*/
/* before you upload this plugin: add your mailinglists (line 138) and the labels (line 141) */
/* if you like: translate it into your own language (or write something better than my bad english) */
/* zip this file und upload it to your WordPress-Site*/

function lf_sendMail($doaction, $listname, $mail, $name, $domain) {
	if ($mail == "") {
		 ?>

        <script type="text/javascript">
            alert("You forgot to write your mail-adress");
        </script>

        <?php
	}
	else {
		if ($doaction == "") {
			 ?>

        <script type="text/javascript">
            alert("Error. Please choose one of the following: subscribe one list, unsubscribe one list or unsubscribe all lists.");
        </script>

        <?php
		}
		else {
			switch($doaction) {
				case "SIGNOFFALL":
					$content = "UNSUBSCRIBE * " . $mail;
					break;
				case "SIGNOFF":
					$content = "UNSUBSCRIBE " . $listname;
					break;
				case "INVITE":
					$content = "SUBSCRIBE " . $listname . " " . $name;
					break;
			}
		
		}		
		mail(
			"sympa@".$domain,
			$content,
			$content,
			'From: ' . $mail
		);
	}
}

function lf_form($atts) {
    ob_start();

    $atts = shortcode_atts( array(
        'domain' => "sympa.your-company.org", /* TODO: insert your domain here! */
        'liste' => null
    ), $atts, 'lf_form' );

    if(isset($_POST['lf_submit'])) {
        lf_sendMail(
			sanitize_text_field($_POST['lf_doaction']),
            sanitize_text_field($_POST['lf_art']),
            sanitize_text_field($_POST['lf_mail']),
            sanitize_text_field($_POST['lf_vorname']),
            $atts['domain']
        );
		if ($_POST['lf_doaction'] == "INVITE") {
        ?>
		
		<p style="border: 2px solid green">We send you an email, that you have to confirm. You can only get messages of this list, if you confirm this mail.<br />If you don´t get a mail, wait a few minutes and/or check your spam-folder</p>
		<a href="<?php echo lf_getCurrentUrl(); ?>">Refresh this page and subscribe to another list</a>

        <?php
		}
		else {
		?>
		
		<p style="border: 2px solid green">We send you an email, that you have to confirm. You can only unsubscribe, if you confirm this mail.<br />If you don´t get a mail, wait a few minutes and/or check your spam-folder</p>
		<a href="<?php echo lf_getCurrentUrl(); ?>">Refresh this page</a>
		
        <?php
		}
    } 
	else {
        ?>

        <form action="<?php echo lf_getCurrentUrl(); ?>" method="post">
			<h3>Your personal data</h3>
            <table border="0" cellpadding="0" cellspacing="3" width="100%" style="border: 0px;">
               <tr style="border: 0px;">
                    <td><label for="lf_vorname">Your Name (optional)</label></td>
                    <td><input type="text" name="lf_vorname" style="width: 100%;" placeholder="Your Name"></td>
                </tr>
                <tr style="border: 0px;">
                    <td><label for="lf_mail">Your Mail-Adress</label></td>
                    <td><input type="email" name="lf_mail" style="width: 100%;" placeholder="mail@example.org"></td>
                </tr>
			</table>
			
			
        <?php if($atts['domain'] == "sympa.your-company.org" && $atts['liste'] == null) { /* TODO: insert your domain here! */ ?> 
			<h3>What do you want to do?</h3>
			    <div style="float: left;padding: 2px;">
                    <input type="radio" name="lf_doaction" id="lf_art_invite" value="INVITE">
                </div>
                <div style="float: left;padding: 2px;">
                    <label for="lf_art_invite">Subscribe a specific mailinglist</label>
                </div>
				<div style="clear: both"></div>
						
				<div style="float: left;padding: 2px;">
					<input type="radio" name="lf_doaction" id="lf_art_signoff" value="SIGNOFF">
                </div>
                <div style="float: left;padding: 2px;">
                    <label for="lf_art_signoff">Unsubscribe a specific mailinglist</label>
                </div>
				<div style="clear: both"></div>
						
				<div style="float: left;padding: 2px;">
                    <input type="radio" name="lf_doaction" id="lf_art_signoffall" value="SIGNOFFALL">
                </div>
                <div style="float: left;padding: 2px;">
                    <label for="lf_art_signoffall">Unsubscribe all mailinglists</label>
                </div>
				<div style="clear: both"></div>

				
				<h3>Which mailinglist?</h3>				
				 <?php 
				 /* TODO: Fill in a list of your mailinglists her. Example is with three mailinglists, but you can add as many as you want */
				 $listarray = array('listname1', 'listname-foo', 'listname-bar');
				 
				 /* TODO: Array of all labels for lists (must be in the same order as the array of lists above) */
				 $listarraylabel = array('My List Number 1', 'List Foo', 'List Bar');
				 
				
				for ($i=0; $i<count($listarray); $i++) {
					echo "<div style=\"float: left;padding: 2px;\"><input type=\"radio\" name=\"lf_art\" id=\"$listarray[$i]\" value=\"$listarray[$i]\" /></div>
                        <div style=\"float: left;padding: 2px;\"><label for=\"$listarray[$i]\">$listarraylabel[$i]</label></div><div style=\"clear: both\"></div>";
				}
				

		?>
				
				

        <?php } elseif($atts['liste'] == null) { ?>
				<p style="border: 2px solid red">Configuration Error: The responsible of this website forgot, to write the name of the list</p>
                
        <?php } else { ?>
		<h3>What do you want to do?</h3>
			    <div style="float: left;padding: 2px;">
                    <input type="radio" name="lf_doaction" id="lf_art_invite" value="INVITE">
                </div>
                <div style="float: left;padding: 2px;">
                    <label for="lf_art_invite">Subscribe the mailinglist "<?php echo $atts['liste']; ?>"</label>
                </div>
				<div style="clear: both"></div>
						
				<div style="float: left;padding: 2px;">
					<input type="radio" name="lf_doaction" id="lf_art_signoff" value="SIGNOFF">
                </div>
                <div style="float: left;padding: 2px;">
                    <label for="lf_art_signoff">Unsubscribe the mailinglist "<?php echo $atts['liste']; ?>"</label>
                </div>
				<div style="clear: both"></div>
                    <input type="hidden" name="lf_art" value="<?php echo $atts['liste']; ?>">
        <?php } ?>
				
				<input type="submit" name="lf_submit" value="Submit">
    
        </form>

        <?php
    }

    $output = ob_get_clean();
    return $output;
}
add_shortcode("lf_form", "lf_form");

function lf_getCurrentUrl() {
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
