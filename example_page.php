<?php
include "bmsparser_class.php";

error_reporting(E_ALL ^ E_NOTICE);

$bmsclass=new BMS_Parser("example-files/example.bme");

$metadata=$bmsclass->parseMetadata();
$numnotes=$bmsclass->numNotes();
$usedkeys=$bmsclass->keysUsed();
$datafiles=$bmsclass->listDatafiles();

$gaugepernote=sprintf("%0.2f",$metadata["total"] / $numnotes);

$rawoutput=$_GET["rawoutput"];

if($rawoutput != 1){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
<title>BMS_Reader Example</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>

<div style="font-size: 20px; font-weight: bold;">Basic File Information</div>

<table width="573" style="border: 1px solid; font-size: 12px;">
 <tr>
  <td width="30%">Full Path</td>
  <td width="70%"><?=$metadata["filepath"]?></td>
 </tr>
 <tr>
  <td width="30%">Filename</td>
  <td width="70%"><?=$metadata["filename"]?></td>
 </tr>
 <tr>
  <td width="30%">File size</td>
  <td width="70%"><?=$metadata["filesize"]?> Bytes</td>
 </tr>
 <tr>
  <td width="30%">MD5</td>
  <td width="70%"><?=$metadata["filehash"]?> (<a href="http://www.dream-pro.info/~lavalse/LR2IR/search.cgi?mode=ranking&amp;bmsmd5=<?=$metadata["filehash"];?>">Info on LR2IR</a>)</td>
 </tr>
 <tr>
  <td width="30%">File Type</td>
  <td width="70%"><?=$metadata["fileex"]?></td>
 </tr>
</table>


<div style="font-size: 20px; font-weight: bold;">Metadatas</div>

<table width="573" style="border: 1px solid; font-size: 12px;">
 <tr>
  <td width="30%">Play Type</td>
  <td width="70%"><?=$metadata["playtype"]["name"]?> (<?=$usedkeys?>keys, raw=<?=$metadata["playtype"]["id"]?>)</td>
 </tr>
 <tr>
  <td width="30%">Music Genre</td>
  <td width="70%"><?=$metadata["genre"]?></td>
 </tr>
 <tr>
  <td width="30%">Music Title</td>
  <td width="70%"><?=$metadata["title"]?></td>
 </tr>
 <tr>
  <td width="30%">Music Artist</td>
  <td width="70%"><?=$metadata["artist"]?></td>
 </tr>
 <tr>
  <td width="30%">BPM (Tempo)</td>
  <td width="70%"><?=$metadata["bpm"]?></td>
 </tr>
 <tr>
  <td width="30%">Playlevel</td>
  <td width="70%"><?=$metadata["plevel"]?></td>
 </tr>
 <tr>
  <td width="30%">Judge Rank</td>
  <td width="70%"><?=$metadata["rank"]["name"]?> (raw=<?=$metadata["rank"]["id"]?>)</td>
 </tr>
 <tr>
  <td width="30%">Gauge Totals</td>
  <td width="70%"><?=$metadata["total"]?> (<?=$gaugepernote?> per a note)</td>
 </tr>
 <tr>
  <td width="30%">Number of Keysounds</td>
  <td width="70%"><?=$datafiles["number_keysounds"]?></td>
 </tr>
 <tr>
  <td width="30%">Number of Images</td>
  <td width="70%"><?=$datafiles["number_images"]?></td>
 </tr>
 <tr>
  <td width="30%">Number of Notes</td>
  <td width="70%"><?=$numnotes?></td>
 </tr>
</table>
</body>
</html>
<? }elseif($rawoutput == 1){ ?>
<pre>
<?php
$overallinfo=$bmsclass->buildOverallInfo();
print_r($overallinfo);
?>
</pre>
<? } ?>
