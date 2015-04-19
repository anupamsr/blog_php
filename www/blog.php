<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en_US.UTF-8" lang="en_US.UTF-8">
	<head>
		<title>Walled Garden Near Ghat - Blog</title>
		<meta name="Author" content="Anupam Srivastava, webmaster@familyknow.in" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="Date" content="Unix time for last update: 1212829250" />
		<meta http-equiv="Cache-Control" content="No-Cache" />
		<meta http-equiv="Pragma" content="No-Cache" />
		<meta http-equiv="Expires" content="Mon, 01 Jan 2002 00:00:00 GMT" />
		<meta name="Copyright" content="&copy; 2008 Anupam Srivastava, All Rights Reserved" />
		<meta name="Robots" content="index,follow,noarchive" />
		<meta name="Googlebot" content="noarchive" />
		<link rel="icon" type="image/png" href="favicon.ico" />
		<link rel="stylesheet" href="css/stylesheet.css" type="text/css" />
		<link rel="alternate" title="Blog RSS" href="http://www.familyknow.in/rss.php" type="application/rss+xml" />
	</head>
	<body>
		<div style="text-align: center; font-weight: bold;">&copy; 2008 Anupam Srivastava, All Rights Reserved</div>
		<p><a href="main.html">Go Back</a></p>
		<noscript><div style="text-align: center;"><strong>JavaScript is disabled in your browser. You should enable it.</strong></div></noscript>
		<a name="TOC"></a>
		<div style="padding: 10px;">
			Table of contents:
			<div style="font-size: small;">
				<ol type="i">
<?php

$url='thewholeblog.xml.gz';

putenv("TZ=Europe/London");

include 'xml_read.php';

$xml_parser = new sxml;
$src=implode ('', gzfile ($url));
$xml_parser->parse($src);
$blogs=$xml_parser->data;

function myIsInt ($x) {
    return (is_numeric($x) ? intval($x) == $x : false);
}

$more_counter_default = 10;

if (isset($_GET['start']) && strlen(trim($_GET['start'])) != 0 && myIsInt($_GET['start'])) {
	$start_counter = $_GET['start'];
} else {
	$start_counter = 0;
}
if (isset($_GET['more']) && strlen(trim($_GET['more'])) != 0 && myIsInt($_GET['more'])) {
	$more_counter = $_GET['more'];
} else {
	$more_counter = $more_counter_default;
}

if ($more_counter < 1) {
	$more_counter = $more_counter_default;
}

$counter = 0;
$shown = 0;
$more_showed = 0;
foreach($blogs['BLOGS'][0]['child']['BLOG'] as $blog) {
	$counter = $counter + 1;
	if ($counter > $start_counter) {
		$shown = $shown + 1;
		if ($shown <= $more_counter) {
			$entry[$shown] = $blog['child'];
			echo "\t\t\t\t\t".'<li><a href="#'.$entry[$shown]['TIME'][0]['data'].'">'.$entry[$shown]['TITLE'][0]['data'].'</a></li>'."\n";
		} else {
			echo "\t\t\t\t\t".'<li><a href="#more">» More</a></li>'."\n";
			$more_showed = 1;
			break;
		}
	}
}
if ($shown == 0) {
	echo "\t\t\t\t\t<li>Really, this should not have happened :)</li>";
}
echo "\t\t\t\t</ol>\n";
echo "\t\t\t</div>\n";
echo "\t\t</div>";

echo '<a href="rss.php" style="text-decoration: none;"><img src="images/rss.jpg" alt="RSS feed" style="border: 0;" /> Subscribe to RSS feed.</a>';

$username="walledga_usr";
$password="UsoMango10(~";
$database="walledga_com";

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$num_blogs = $shown - 1;
if ($more_showed == 0) {
	$num_blogs = $shown;
}

for ($i = 1; $i <= $num_blogs; ++$i) {
	$query = sprintf("SELECT * FROM t%d", $entry[$i]['TIME'][0]['data']);
	$num = 0;
	$result = mysql_query($query);
	if ($result) {
		$num = mysql_num_rows($result);
	}
	echo '
		<div class="container">
			<a name="'.$entry[$i]['TIME'][0]['data'].'"></a>
			<div class="title"><h3>'.$entry[$i]['TITLE'][0]['data'].'</h3>'.date('h:i a, j F Y', $entry[$i]['TIME'][0]['data']).'<h3></h3></div>
			<div class="textbody">
				'.$entry[$i]['TEXT'][0]['data'].'
				<p><br /></p>
				<p style="text-align: left; font-size: small;"><em><a href="comment.php?name='.$entry[$i]['TIME'][0]['data'].'" style="text-decoration: none;">Comments ('.$num.') / Permanent link</a></em> &mdash; <a href="#TOC" style="text-decoration: none;"><tt>GO TO TOC</tt></a></p>
			</div>
		</div>';
}

if ($more_showed == 1) {
	/* Go back by 1, so that we don't miss the current blog */
	$counter = $counter - 1;
	
	echo '
		<div class="pane" style="text-align: center;">
			<a name="more"></a><h3>
				<a href="?start='.$counter.'&amp;more='.$more_counter.'">More: Click here for older posts</a> &mdash;
				<a href="?start='.$start_counter.'&amp;more=10">';
	if ($more_counter == 10) {
		echo '<em>(10)</em>';
	} else {
		echo '(10)';
	}
	echo '</a>
				<a href="?start='.$start_counter.'&amp;more=20">';
	if ($more_counter == 20) {
		echo '<em>(20)</em>';
	} else {
		echo '(20)';
	}
	echo '</a>
				<a href="?start='.$start_counter.'&amp;more=50">';
	if ($more_counter == 50) {
		echo '<em>(50)</em>';
	} else {
		echo '(50)';
	}
	echo '</a>
			</h3>
		</div>
		<div class="textbody">Use your browser’s <q>Back</q> or <q>History</q> button for navigation.</div>
';
}

mysql_close();
?>
		<p style="text-align: right;">
		<a href="http://jigsaw.w3.org/css-validator/validator?uri=http%3A%2F%2Fwww.familyknow.in%2Fcss%2Fstylesheet.css&amp;profile=css21&amp;usermedium=all&amp;warning=1"><img style="border:0;width:88px;height:31px" src="images/vcss" alt="Valid CSS!" /></a>
		<a href="http://validator.w3.org/check?uri=referer"><img style="border:0;width:88px;height:31px" src="images/valid-xhtml10" alt="Valid HTML 4.01 Transitional" /></a>
		</p>
		<div style="text-align: center;">
			<tt>Contact at webmaster @ familyknow.in for more information.</tt>
		</div>
	</body>
</html>

