<?php
header("Content-Type: application/rdf+xml");
$url='thewholeblog.xml.gz';

putenv("TZ=Europe/London");

include 'xml_read.php';

$xml_parser = new sxml;
$src=implode ('', gzfile ($url));
$xml_parser->parse($src);
$blogs=$xml_parser->data;

$i = 0;
foreach($blogs['BLOGS'][0]['child']['BLOG'] as $blog) {
	$entry[$i] = $blog['child'];
	++$i;
}

echo '<?xml version="1.0" encoding="utf-8"?>

<!DOCTYPE rdf:RDF PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/2002/REC-xhtml1-20020801/DTD/xhtml1-transitional.dtd">

<rdf:RDF
 xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
 xmlns="http://purl.org/rss/1.0/"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:ag="http://purl.org/rss/1.0/modules/aggregation/"
 xmlns:admin="http://webns.net/mvcb/"
 xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
 xmlns:content="http://purl.org/rss/1.0/modules/content/"
>
	<channel rdf:about="http://www.familyknow.in/blog.php">
		<title>Walled Garden Near Ghat - Blog</title>
		<link>http://www.familyknow.in/rss.php</link>
		<description>RSS feed for Anupam\'s personal blog</description>
		<dc:rights>Copyright 2015, Anupam Srivastava</dc:rights>
		<dc:language>en-us</dc:language>
		<sy:updatePeriod>hourly</sy:updatePeriod>
		<sy:updateFrequency>1</sy:updateFrequency>
		<sy:updateBase>';
	   echo '2008-01-01T00:00:00+00:00';
echo '</sy:updateBase>
		<items>
			<rdf:Seq>';
for ($j = 0; $j < $i; ++$j) {
	echo '
				<rdf:li rdf:resource="http://www.familyknow.in/comment.php?name='.$entry[$j]['TIME'][0]['data'].'"/>';
}

echo '
			</rdf:Seq>
		</items>
	</channel>';

for ($j = 0; $j < $i; ++$j) {
	echo '
	<item rdf:about="http://www.familyknow.in/comment.php?name='.$entry[$j]['TIME'][0]['data'].'">
		<title>'.$entry[$j]['TITLE'][0]['data'].'</title>
		<link>http://www.familyknow.in/comment.php?name='.$entry[$j]['TIME'][0]['data'].'</link>
		<dc:date>';
	$myDate = $entry[$j]['TIME'][0]['data'];
	$W3CDTFdate = preg_replace(
       "/(\+|\-)([0-9]{2})([0-9]{2})/"
       ,"$1$2:$3"
       , date("O",$myDate));
printf("%sT%s%s"
       ,date("Y-m-d",$myDate)
       ,date("H:i:s",$myDate)
	   ,$W3CDTFdate);
	echo '</dc:date>
		<dc:creator>Anupam Srivastava</dc:creator>
		<dc:subject>Blog</dc:subject>
		<description>'.strip_tags($entry[$j]['TEXT'][0]['data']).'</description>
		<content:encoded><![CDATA['.$entry[$j]['TEXT'][0]['data'].']]></content:encoded>
	</item>';
}

echo '
</rdf:RDF>';
?>

