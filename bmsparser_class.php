<?php
// A Be-Music Source(BMS) File Parser for PHP by desirous.Blue (http://anoldstory.egloos.com)
// Version 0.1 (2012.2.9). Last Changed: 2012.12.30
// This code is licensed under GNU Lesser General Public License (GNU LGPL) or a BSD-style licenses.
// for texts of the license, please see http://www.gnu.org/licenses/lgpl.html
// This code requires that PHP Version 5 is configured on your host webserver. (in other words, your webhosting provider must support PHP Version 5)
// if the host server is what you managing now, please see http://php.net/manual/en/install.php
// this code supports all variant of Be-Music Source File formats: BMS (the standard), BME (for 7/14 keys like Beatmania IIDX), BML (for 'long-notes' like Ez2DJ), PMS (for Pop'n Music like games).
// for the original format specification of BMS files, see http://bm98.yaneu.com/bm98/bmsformat.html

class BMS_Reader{
 const BR_VERSION="0.1.9.7";

 // Directives for basic information (metadatas)
 const B_PLAYTYPE="PLAYER"; // Play mode
 const B_MUSIC_GENRE="GENRE"; // Music genre
 const B_SCORE_TITLE="TITLE"; // Music title
 const B_MUSIC_ARTIST="ARTIST"; // Music Artist
 const B_MUSIC_BPM="BPM"; // BPM (Beats Per Minute)
 const B_SCORE_LEVEL="PLAYLEVEL"; // Playing Difficulty
 const B_BRIEF_RANK="RANK"; // Judge Level
 const B_PARAM_TOTAL="TOTAL"; // Gauge Totals
 const B_MIXLEVEL="DIFFICULTY"; // Display difficulty name
 const B_DET_RANK="DEFEXRANK"; // Judge Rank (detailed)
 const B_BGMFILE="MIDIFILE"; // Name of Background Music file
 const B_VOLUME="VOLWAV"; // Sound Volume
 
 // Maximum channel numbers
 const SP5KEYS=16; // Single Play (5 keys) / 11~15: Keys 1~5, 16: SC
 const SP7KEYS=19; // Single Play (7 keys) / 11~15: Keys 1~5, 16: SC, 18~19: Keys 6~7
 const DP9KEYS=25; // Double Play (9 keys) / 11~15: Keys 1~5, 22~25: Keys 6~9
 const DP10KEYS=26; // Double Play (10 keys) / 11~15: 1P Keys 1~5, 16: 1P SC, 21~25: 2P Keys 1~5, 26: 2P SC
 const DP14KEYS=29; // Double Play (14 keys) / 11~15: 1P Keys 1~5, 16: 1P SC, 18~19: 1P Keys 6~7, 21~25: 2P Keys 1~5, 26: 2P SC, 28~29: 2P Keys 6~7
 // for Long-notes
 const LN_SP5KEYS=56;
 const LN_SP7KEYS=59;
 const LN_DP9KEYS=65;
 const LN_DP10KEYS=66;
 const LN_DP14KEYS=69;
 
 var $mixlevels=array(1 => "BASIC",2 => "NORMAL",3 => "HYPER",4 => "ANOTHER",5 => "INSANE");
 var $play_types=array(1 => "Single",2 => "Two",3 => "Double / 9 keys");
 var $brief_ranks=array(0 => "Very Hard",1 => "Hard",2 => "Normal",3 => "Easy");
 
 // the constructor
 /**
  * @param $path
  */
 function __construct($path){
  $this->path=$path;
  $this->handle=fopen($this->path,"r");
 }
 
 // parseMetadata(): reads a metadata from the file.
 /**
  * @return array $data
  */
 function parseMetadata(){
  $data=array();
  $flagsfound=0;
  $playtype_def=false;

  $data["BMS_Reader_Version"]=self::BR_VERSION;
  $data["filepath"]=str_replace(DIRECTORY_SEPARATOR,"/",realpath($this->path));
  $data["path"]=dirname($data["filepath"]);
  $data["filename"]=basename($this->path);
  $data["mime"]="text/x-bemusic";
  $data["filesize"]=filesize(realpath($this->path));
  $data["filehash"]=md5_file(realpath($this->path));
  $data["fileex"]=end(explode(".",basename($this->path)));
  rewind($this->handle);
  while(($parsing=fgets($this->handle)) !== false){
   $parameter=strstr($parsing," ",true);
   $parameter=str_replace("#","",$parameter);
   $parameter=strtoupper($parameter);
   
   $value=strstr($parsing," ");
   $value=trim($value);

   switch($parameter){
   case self::B_PLAYTYPE:
   $data["playtype"]["id"]=(int)$value;
   $data["playtype"]["name"]=$this->play_types[$value];
   $playtype_def=true;
   break;
   case self::B_MUSIC_GENRE:
   $data["genre"]=$value;
   $flagsfound++;
   break;
   case self::B_SCORE_TITLE:
   $data["title"]=$value; 
   $flagsfound++;
   break;
   case self::B_BGMFILE:
   $data["bgmfile"]=$value; 
   $flagsfound++;
   break;
   case self::B_VOLUME:
   if(!is_numeric($value) || empty($value)){
   $data["volume"]=100;
   $data["warning"][++$i]="Volume is not set. set to 100.";
   }else{$data["volume"]=(float)$value;}
   $flagsfound++;
   break;
   case self::B_MUSIC_ARTIST:
   $data["artist"]=$value; 
   $flagsfound++;
   break;
   case self::B_MUSIC_BPM:
   $data["bpm"]=(float)$value;
   if(!is_numeric($value)){
   $data["bpm"]=130;
   $data["warning"][++$i]="BPM is not set. Set to default BPM of 130.";
   }
   $flagsfound++;
   break;
   case self::B_SCORE_LEVEL:
   $data["plevel"]=(int)$value; 
   $flagsfound++;
   break;
   case self::B_BRIEF_RANK:
   if(empty($value) || !is_numeric($value)){
   $data["rank"]["id"]=0;
   $data["rank"]["name"]="Unknown";
   $data["warning"][++$i]="Judge Level is not set.";
   }else{
   $data["rank"]["id"]=(int)$value;
   $data["rank"]["name"]=$this->brief_ranks[$value];
   }
   $flagsfound++;
   break;
   case self::B_PARAM_TOTAL:
   $data["total"]=(int)$value; 
   $flagsfound++;
   break;
   case self::B_MIXLEVEL:
   $data["mixlevel"]=$this->mixlevels[$value]; 
   $flagsfound++;
   break;
   case self::B_DET_RANK:
   $data["decrank"]=$value; 
   $flagsfound++;
   break;
   default:
   break;
   }
  }
  if($playtype_def != true){
   unset($data);
   $data["error"][0]["code"]=0x0F;
   $data["error"][0]["desc"]="This file is not a valid BMS file!";
   return $data;
  }
  return $data;
 }

 // numNotes(): calculate number of notes from the file. 
 /**
  * @return int $notes
  */
 function numNotes(){
  $data=array();
  $notes=0;
  rewind($this->handle);
  while(($parsing=fgets($this->handle)) !== false && !feof($this->handle)){
   $param_id=strstr($parsing,":",true);
   $param_id=str_replace("#","",$param_id);
   if(preg_match("/^([0-9]{5})$/",$param_id)){
    $track=substr($param_id,0,3);
    $channel=substr($param_id,3,2);
    $messages=trim(strstr($parsing,":"),":");
    $eachmsg=str_split($messages,2);
    $size=count($eachmsg);
    $normalnotes=(intval($channel) >= 11 && intval($channel) <= 29);
    $longnotes=(intval($channel) >= 51 && intval($channel) <= 69);
    if($normalnotes || $longnotes){
     $i=0;
     for($i=1;$i<=$size;$i++){
      if(intval(base_convert($eachmsg[$i-1],36,10)) >= 1){
       if($normalnotes){$notes++;}
       elseif($longnotes){$notes+=0.5;} // for long-notes
      }
     }
    }
   }
  }
  return $notes;
 }

 // keysUsed(): gets number of keys used in the file
 /**
  * @return int|string $keystype
  */
 function keysUsed(){
  rewind($this->handle);
  $buffers=array();
  while(($lines=fgets($this->handle)) !== false){
   $ch_params=str_replace("#","",strstr($lines,":",true));
   if(preg_match("/^([0-9]{5})$/",$ch_params)){
    $channel_id=substr($ch_params,3,2);
    $standardnotes=($channel_id >= 11 && $channel_id < 30);
    $longnotes=($channel_id >= 51 && $channel_id < 70);
    if($standardnotes){$buffers[]=(int)$channel_id;}
   }
  }
  $chnum_max=max($buffers);
  switch($chnum_max){
   case self::SP5KEYS:
   case self::LN_SP5KEYS:
   $keystype=5; break;
   case self::SP7KEYS: 
   case self::LN_SP7KEYS:
   $keystype=7; break;
   case self::DP9KEYS:
   case self::LN_DP9KEYS:
   $keystype=9; break;
   case self::DP10KEYS:
   case self::LN_DP10KEYS:
   $keystype=10; break;
   case self::DP14KEYS: 
   case self::LN_DP14KEYS:
   $keystype=14; break;
   default: $keystype="?"; break;
  }
  unset($buffers);
  return $keystype;
 }

 // array listDatafiles(): gets name of data files used in the file
 /**
  * @return array $data
  */
 function listDatafiles(){
  rewind($this->handle);
  $data=array();
  while(($parsing=fgets($this->handle)) !== false){
   $param=str_replace("#","",strstr($parsing," ",true));
   if(preg_match("/^(WAV)([A-Za-z0-9]{2})$/i",$param)){
    $soundid=substr($param,3,2);
    $soundname=trim(strstr($parsing," "));
    $data["keysounds"][$soundid]=$soundname;
   }elseif(preg_match("/^(BMP)([A-Za-z0-9]{2})$/i",$param)){
    $imageid=substr($param,3,2);
    $imagename=trim(strstr($parsing," "));
    $data["images"][$imageid]=$imagename;
   }
  }
  return $data;
 }
 // array buildOverallInfo(): gets merged result of remaining methods in this class.
 function buildOverallInfo(){
  $metadata=$this->parseMetadata();
  $numnotes=$this->numNotes();
  $keysused=$this->keysUsed();
  $list_datas=$this->listDatafiles();

  $keysnotes=array("used_keys" => $keysused,"num_notes" => $numnotes);

  return array_merge($metadata,$keysnotes,$list_datas);
 }
}
?>