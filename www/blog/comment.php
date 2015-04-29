<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/2002/REC-xhtml1-20020801/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en_US.UTF-8" lang="en_US.UTF-8">
	<head>
		<title>Walled Garden Near Ghat - Comment</title>
		<meta name="Author" content="Anupam Srivastava, webmaster@familyknow.in" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="Date" content="Unix time for last update: 1212836207" />
		<meta http-equiv="Cache-Control" content="No-Cache" />
		<meta http-equiv="Pragma" content="No-Cache" />
		<meta http-equiv="Expires" content="Mon, 01 Jan 2002 00:00:00 GMT" />
		<meta name="Copyright" content="&copy; 2008 Anupam Srivastava, All Rights Reserved" />
		<meta name="Robots" content="noindex,follow,noarchive" />
		<meta name="Googlebot" content="noarchive" />
		<link rel="icon" type="image/png" href="favicon.ico" />
		<link rel="stylesheet" href="css/stylesheet.css" type="text/css" />
	</head>
	<body>
		<div style="text-align: center; font-weight: bold;">&copy; 2008 Anupam Srivastava, All Rights Reserved</div>
<?php

$url='thewholeblog.xml.gz';

/* Set the timezone to UTC.
 */
putenv("TZ=Europe/London");

include 'xml_read.php';

$xml_parser = new sxml;
$src=implode ('', gzfile ($url));
$xml_parser->parse($src);
$blogs=$xml_parser->data;

$submission = FALSE;
/* Check if a blog-id has been given */
$getvar = $_GET['name'];
if (isset($getvar) && strlen(trim($getvar)) != 0) {
	/* See if it exists */
	$exists = FALSE;
	foreach($blogs['BLOGS'][0]['child']['BLOG'] as $blog) {
		if ($_GET['name'] === $blog['child']['TIME'][0]['data']) {
			/* If exists, show it... */
			echo '		<p><a href="/blog/">Go Back</a></p>
		<noscript><div style="text-align: center;"><strong>JavaScript is disabled in your browser. You should enable it.</strong></div></noscript>
';
			echo '
		<div class="container">
			<a name="'.$blog['child']['TIME'][0]['data'].'"></a>
			<div class="title"><h3>'.$blog['child']['TITLE'][0]['data'].'</h3>'.date('h:i a, j F Y', $blog['child']['TIME'][0]['data']).'<h3></h3></div>
			<div class="textbody">
				'.$blog['child']['TEXT'][0]['data'].'
			</div>
		</div>';

			/* ... and proceed to show comments */
			$submission = FALSE;
			$exists = TRUE;
			break;
		}
	}
	/* if doesn't exist, exit */
	if ($exists == FALSE) {
		die('<p style="text-align: center;"><strong>Blog ID not found.</strong></p></body></html>');
	}
} else {
	/* See if this is because we are submitting a comment */
	if (strlen(trim($_POST['enteredblogid'])) == 0) {
		/* If not submitting a comment, exit */
		die('<p style="text-align: center;"><strong>No Blog ID specified.</strong></p></body></html>');
	} else {
		/* See if everything has been "POST"ed or not */
		if (strlen(trim($_POST['enteredipaddress'])) == 0 || strlen(trim($_POST['enteredname'])) == 0 || strlen(trim($_POST['enteredemail'])) == 0 || strlen(trim($_POST['enteredcomment'])) == 0) {
			/* if not, exit */
			die('<p style="text-align: center;"><strong>Please specifiy all input fields.</strong></p></body></html>');
		} else {
			/* If everything has been "POST"ed, proceed... */
			$submission = TRUE;
			$blog['child']['TIME'][0]['data'] = $_POST['enteredblogid'];
		}
	}
}

/* ... proceeding...
 * ... to handle comments
 */

/* Create database if doesn't exist already */
$username="walledga_usr";
$password="UsoMango10(~";
$database="walledga_com";

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

/* Rudementary check for table name */
if (((int) $blog['child']['TIME'][0]['data']) < 10000) {
	echo $blog['child']['TIME'][0]['data'];
	echo "<br />\n";
	echo ((int) $blog['child']['TIME'][0]['data']);
	echo "<br />\n";
	die ('Serious error in table creation detected. Please mail webmaster@familyknow.in the current URL and this error.');
}

$query=sprintf("CREATE TABLE IF NOT EXISTS t%d (id int(6) NOT NULL auto_increment, ipaddress varchar(15) NOT NULL, date int(11) NOT NULL, comname varchar(30) NOT NULL, email varchar(50) NOT NULL, comment text(1000) NOT NULL, PRIMARY KEY (id), UNIQUE id (id), KEY id_2 (id))", mysql_real_escape_string($blog['child']['TIME'][0]['data']));
$result=mysql_query($query);

if (!$result) {
	$message  = 'Invalid query: '.mysql_error()."<br />\n";
	$message .= 'Whole query: ' . $query;
	die($message);
}

/* Load reCAPTCHA */
/*
 * This is necessary HERE, because if there is a problem with loading recapthalib, comments will not be shown at all (which I think is a good thing).
 */
require_once('recaptchalib.php');

/* Check if we are submitting a comment or not */
if ($submission == TRUE) {
	/* Check if it was human */
	$privatekey = "6LddVQEAAAAAAOXtxO_IsUH04E-HMFtJjtAVi2Xx";
	$resp = recaptcha_check_answer ($privatekey,
		$_SERVER["REMOTE_ADDR"],
		$_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);
	if ($resp->is_valid) {
		/* If reCAPTCHA works, update the table */
		
		/* Sanitize */
		$string_ipaddress = mysql_real_escape_string($_POST['enteredipaddress']);
		$string_name = mysql_real_escape_string($_POST['enteredname']);
		$string_email = mysql_real_escape_string($_POST['enteredemail']);
		$string_comment = mysql_real_escape_string($_POST['enteredcomment']);
		$pattern[0] = '/(\\\\)/';
		$pattern[1] = "/\"/";
		$pattern[2] = "/'/";
		$replacement[0] = '\\\\\\';
		$replacement[1] = '\"';
		$replacement[2] = "\\'";
		$len = strlen($string);
		$string_ipaddress = strip_tags(preg_replace($pattern, $replacement, $string_ipaddress));
		$string_name = strip_tags(preg_replace($pattern, $replacement, $string_name));
		$string_email = strip_tags(preg_replace($pattern, $replacement, $string_email));
		$string_comment = strip_tags(preg_replace($pattern, $replacement, $string_comment));
		$int_date = time();
		$string_email = preg_replace('/(;|\||`|>|<|&|^|"|'."\n|\r|'".'|{|}|[|]|\)|\()/i', "", $string_email);
		$query = sprintf("INSERT INTO t%d VALUES('', '%s', '%d', '%s', '%s', '%s')", mysql_real_escape_string($blog['child']['TIME'][0]['data']), $string_ipaddress, $int_date, $string_name, $string_email, $string_comment);
		$result = mysql_query($query);
		if ($result == FALSE) {
			die('<p><strong>Error submitting comment.</strong></p><p>This should not happen. Please inform at webmaster@familyknow.in.</p></body></html>');
		} else {
			$msg_mail = wordwrap('
A new message is waiting for you.
Blog-id: '.$blog['child']['TIME'][0]['data'].'
Author: '.$string_name.' ('.$string_email.') from '.$string_ipaddress.'
Time: '.date('m-d-Y', $int_date).'
Comment:
'.$string_comment.'

-------END-------');
			mail('webmaster@familyknow.in','You have a New Comment on your blog',$msg_mail);
		}
	} else {
		/* If reCAPTCHA failed, exit */
		$error = $resp->error;
		die('<p><strong>The reCAPTCHA was not entered correctly. Go back and try it again.</strong></p><p>Error: '.$error.'</p></body></html>');
	}
	die('<p style="text-align: center;">Comment succesfully submitted.</p><p style="text-align: center;"><a href="/blog/">Go Back</a></p></body></html>');
} else {
	/* If we are not submitting, show the already present comments */
	$query = sprintf("SELECT * FROM t%d ORDER BY date DESC", mysql_real_escape_string($blog['child']['TIME'][0]['data']));
	$result = mysql_query($query);
	$num = mysql_num_rows($result);
	echo '
		<div style="text-align: center; font-weight: bold;">Comments are properties of whoever wrote them</div>';
	/* Check if there are any comments present or not */
	$i = 0;
	while ($i < $num) {
		$name = mysql_result($result, $i, "comname");
		$date_of_commenting = mysql_result($result, $i, "date");
		$diff = mktime(date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")) - $date_of_commenting;
		$unit = "second";
		if ($diff > 60) {
			$diff = floor($diff/60);
			$unit = "minute";
			if ($diff > 60) {
				$diff = floor($diff/60);
				$unit = "hour";
				if ($diff > 24) {
					$diff = floor($diff/24);
					$unit = "day";
					// if more than a week old, we report actual time
					if ($diff > 7) {
						$diff = floor($diff/7);
						$unit = "week";
						if ($diff > 3) {
							// if more than 3 weeks old, we report actual time
							$diff = "on ";
							$unit = date('l dS \of F Y h:i:s A', $date_of_commenting);
						}
					}
				}
			}
		}
		if (floor($diff) > 1) {
			$unit = $unit."s";
		}
		if (floor($diff) != 0) {
			$unit = $unit." ago";
		}
		$comment = mysql_result($result, $i, "comment");
		$pattern[0] = '/(\\\)r/';
		$pattern[1] = '/(\\\)n/';
		$pattern[2] = '/(\\\)(\\\)(\\\)/';
		$replacement[0] = '';
		$replacement[1] = '<br/>';
		$replacement[2] = '';
		$comment = preg_replace($pattern, $replacement, $comment);
		echo '
		<div class="comment">
			<p><tt><strong>'.$name.'</strong> said '."$diff $unit".':</tt></p>
			<p>'.$comment.'</p>
		</div>';
		$i = $i + 1;
	}

	/* Present the form to submit new comment */
	
	// XXX: "date" is used for the time when the form was submitted and not reloaded. Thus, it is calculated after the form has been submitted but before it has been written to the table.
	echo '
		<form action="comment.php" method="post">
			<fieldset style="border-width: 0px;">
				<h4>Leave a Reply</h4>
				<input type="hidden" name="enteredipaddress" value="'.$_SERVER['REMOTE_ADDR'].'" />
				<input type="hidden" name="enteredblogid" value="'.$blog['child']['TIME'][0]['data'].'" />
				<p>
				<input type="text" name="enteredname" id="enteredname" maxlength="30" /> <label for="enteredname">Name<sup>*</sup></label><br />
				<input type="text" name="enteredemail" id="enteredemail" maxlength="50" /> <label for="enteredemail">E-mail<sup>*</sup></label><br />
				<label for="enteredcomment">Comment:<sup>*</sup></label><br />
				<textarea cols="60" rows="10" name="enteredcomment" id="enteredcomment"></textarea>
				</p>
				<p style="font-size: small;">* All input fields are necessary. No HTML allowed.</p>
				';
	
	/* Show the re-captcha */
	$publickey = "6LddVQEAAAAAAN3b8rE2YE7eb3I8SAXQ1N0C8y3i";
	echo recaptcha_get_html($publickey, $error);
	
	echo '
				<p><input type="submit" value="Submit comment" /></p>
			</fieldset>
		</form>';
}

/* close database */
mysql_close();
?>
		<p style="text-align: right;">
		<a href="http://jigsaw.w3.org/css-validator/validator?uri=http%3A%2F%2Fwww.familyknow.in%2Fcss%2Fstylesheet.css&amp;profile=css21&amp;usermedium=all&amp;warning=1"><img
			src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
			style="border:0;width:88px;height:31px"
			alt="Valid CSS!" /></a>
		<a href="http://validator.w3.org/check?uri=referer"><img
			src="http://www.w3.org/Icons/valid-xhtml10"
			style="border:0;width:88px;height:31px"
			alt="Valid XHTML 1.0 Transitional" /></a>
		</p>
		<div style="text-align: center;">
			<tt>Contact at webmaster @ familyknow.in for more information.</tt>
		</div>
	</body>
</html>

