<?php
if(!empty($_SERVER["REQUEST_METHOD"])){
?>
<pre>
This script must be run from a command line.
Usage: php <?=basename($_SERVER["SCRIPT_NAME"])?> &lt;filepath&gt;
</pre>
<?php
 exit;
}

require "bmsparser_class.php";

if(empty($argv) || !file_exists($argv[1])){exit;}

$bmsclass=new BMS_Parser($argv[1]);

$metadatas=$bmsclass->parseMetadata();
$numnotes=$bmsclass->numNotes();
$usedkeys=$bmsclass->keysUsed();
$datafiles=$bmsclass->listDatafiles();

$gaugepernote=sprintf("%0.2f",$metadatas["total"] / $numnotes);

print <<<END
======= Basic Info =======
Full Path: {$metadatas["filepath"]}
Size: {$metadatas["filesize"]} Bytes
MD5: {$metadatas["filehash"]}
Type: {$metadatas["fileex"]}

======= Score Metadatas =======
Play type: {$metadatas["playtype"]["name"]} ({$usedkeys} keys)
Music Genre: {$metadatas["genre"]}
Music Title: {$metadatas["title"]}
Music Artist: {$metadatas["artist"]}
BPM (Tempo): {$metadatas["bpm"]}
Playlevel: {$metadatas["plevel"]}
Judge Rank: {$metadatas["rank"]["name"]} (raw={$metadatas["rank"]["id"]})
Number of notes: {$numnotes}
Gauge Total: {$metadatas["total"]} ({$gaugepernote}% per a note)
Number of Sound datas: {$datafiles["number_keysounds"]}
Number of Images: {$datafiles["number_images"]}
END;
?>
