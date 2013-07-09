<?php
// A Be-Music Source(BMS) File Parser for PHP by nandarous (themunyang21 at nate dot com)
// Version 0.2 (2013.1.2). Last Changed: 2013.7.9
// This code is licensed under GNU Lesser General Public License (GNU LGPL) or a BSD-style licenses.
// for texts of the license, please see http://www.gnu.org/licenses/lgpl.html
// This code requires that your webhosting provider must support PHP Version 5.
// if the host server is what you managing now, please see http://php.net/manual/en/install.php
// this code supports all variant of Be-Music Source File formats: BMS (the standard), BME (for 7/14 keys like Beatmania IIDX), BML (for 'long-notes' like Ez2DJ), PMS (for Pop'n Music like games).
// for the original format specification of BMS files, see http://bm98.yaneu.com/bm98/bmsformat.html

class BMS_Parser{
 const BP_VERSION="0.2.4.5";

 // Directives for basic information (metadatas)
 const B_PLAYTYPE="PLAYER"; // Play mode
 const B_MUSIC_GENRE="GENRE"; // Music genre
 const B_SCORE_TITLE="TITLE"; // Music title
 const B_MUSIC_ARTIST="ARTIST"; // Music Artist
 const B_MUSIC_COARTIST="SUBARTIST"; // Music co-artist
 const B_MUSIC_BPM="BPM"; // BPM (Beats Per Minute)
 const B_SCORE_LEVEL="PLAYLEVEL"; // Playing Difficulty
 const B_BRIEF_RANK="RANK"; // Judge Level
 const B_PARAM_TOTAL="TOTAL"; // Gauge Totals
 const B_MIXLEVEL="DIFFICULTY"; // Display difficulty name
 const B_DET_RANK="DEFEXRANK"; // Judge Rank (detailed)
 const B_BGMFILE="MIDIFILE"; // Name of Background Music file
 const B_JACKETFILE="STAGEFILE"; // Name of Title Image file.
 const B_BGAFILE="VIDEOFILE"; // Name of BGA video file.
 const B_VOLUME="VOLWAV"; // Sound Volume

 const S_RANDOMIZED="RANDOM"; // main directive for randomized score
 
 // Maximum channel numbers
 const SP5KEYS=16; // Single Play (5 keys) / 11~15: Keys 1~5, 16: SC
 const SP7KEYS=19; // Single Play (7 keys) / 11~15: Keys 1~5, 16: SC, 18~19: Keys 6~7
 const DP9KEYS=25; // Double Play (9 keys) / 11~15: Keys 1~5, 22~25: Keys 6~9
 const DP10KEYS=26; // Double Play (10 keys) / 11~15: 1P Keys 1~5, 16: 1P SC, 21~25: 2P Keys 1~5, 26: 2P SC
 const DP14KEYS=29; // Double Play (14 keys) / 11~15: 1P Keys 1~5, 16: 1P SC, 18~19: 1P Keys 6~7, 21~25: 2P Keys 1~5, 26: 2P SC, 28~29: 2P Keys 6~7
 // for Long-notes (standard notes + 40)
 const LN_SP5KEYS=56;
 const LN_SP7KEYS=59;
 const LN_DP9KEYS=65;
 const LN_DP10KEYS=66;
 const LN_DP14KEYS=69;
 // For BM98
 const APP1_SP5KEYS=15;
 const APP2_SP5KEYS=17; // Pedal-1
 // For right-side Play
 const RP_SINGLEPLAY=21;
 const RP_SP5KEYS=26; // Single Play (5 keys) / 21~25: Keys 1~5, 26: SC
 const RP_SP7KEYS=29; // Single Play (7 keys) / 21~25: Keys 1~5, 26: SC, 28~29: Keys 6~7
 // Channel number for scratch notes
 const SCR_LEFT=16;
 const SCR_RIGHT=26;
 const LN_SCR_LEFT=56;
 const LN_SCR_RIGHT=66;

 var $mixlevels=array(1 => "BASIC",2 => "NORMAL",3 => "HYPER",4 => "ANOTHER",5 => "INSANE");
 var $play_types=array(1 => "Single",2 => "Two",3 => "Double");
 var $brief_ranks=array(0 => "Very Hard",1 => "Hard",2 => "Normal",3 => "Easy");
 
 // the constructor
 /**
  * @param $path
  */
 function __construct($path){
  if(version_compare(PHP_VERSION,"5.0.0") < 0){
   print "The ".__CLASS__." class requires PHP 5.0.x or higher. you are running version ".PHP_VERSION." of PHP.";
   return false;
  }
  $this->path=$path;
  if(!file_exists($this->path)){throw new Exception("File not exists!");}
  $this->handle=@fopen($this->path,"r");
  // If the file is not accessible, then throws an exception.
  if(!is_resource($this->handle)){throw new Exception("Cannot open the file.");}
 }
 
 // parseMetadata(): reads a metadata from the file.
 /**
  * @return array $data
  */
 function parseMetadata(){
  $data=array();
  $flagsfound=0;
  $playtype_def=false;

  $data["BMS_Parser_Version"]=self::BP_VERSION;
  $data["filepath"]=str_replace(DIRECTORY_SEPARATOR,"/",realpath($this->path));
  $data["path"]=dirname($data["filepath"]);
  $data["filename"]=basename($this->path);
  $data["mime"]="text/x-bemusic";
  $data["filesize"]=filesize(realpath($this->path));
  $data["filehash"]=md5_file(realpath($this->path));
  $data["fileex"]=end(explode(".",basename($this->path)));
  $data["randomized"]=false;
  rewind($this->handle);
  while(($parsing=fgets($this->handle)) !== false){
   $parameter=strstr($parsing," ",true);
   $parameter=ltrim($parameter,"#");
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
   case self::B_JACKETFILE:
   $data["titleimage"]=$value;
   $flagsfound++;
   break;
   case self::B_BGAFILE:
   $data["bgafile"]=$value;
   $flagsfound++;
   break;
   case self::B_VOLUME:
   if(!is_numeric($value) || empty($value)){
   $data["volume"]=100;
   $data["warning"][++$i]="Volume is not set. set to 100.";
   }else{$data["volume"]=(float)$value;}
   $flagsfound++;
   break;
   case self::B_MUSIC_ARTIST: // main artist(s)
   $data["artist"]=$value; 
   $flagsfound++;
   break;
   case self::B_MUSIC_COARTIST: // co-artist(s)
   $data["artist"].=" ".$value;
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
   if(!is_numeric($value)){
   $data["rank"]["id"]=-1;
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
   if(!is_numeric($value)){
    $data["total"]=-1;
    $data["warning"][++$i]="Gauge Total is not set.";
   }
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
   case self::S_RANDOMIZED: // case of randomized score
   $data["randomized"]=true;
   $data["additional_infos"][0x80]="This score is randomized.";
   break;
   default:
   break;
   }
   if(preg_match("/^(STOP)([A-Za-z0-9]{2})$/i",$parameter)){
   $data["contains_stop"]=true;
   $data["additional_infos"][0x40]="This score contains stop sequence";
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

 // numNotes( bool $scrnotes ): calculate number of notes from the file. 
 /**
  * @param bool $scrnotes
  * @return int $notes
  */
 function numNotes($scrnotes=false){
  $data=array();
  $notes=0;
  rewind($this->handle);
  while(($parsing=fgets($this->handle)) !== false && !feof($this->handle)){
   $lparameter=strstr($parsing," ",true);
   $lparameter=ltrim($lparameter,"#");
   $lparameter=strtoupper($lparameter);
   $lvalue=strstr($parsing," ");
   $lvalue=trim($lvalue);
   // check whether the file uses "RDM Type-2" long-note scheme
   if(($lparameter == "LNOBJ") && (base_convert($lvalue,36,10) >= 1)){
    $isrdm2=true;
    $lnmessage=$lvalue;
   }
   $param_id=strstr($parsing,":",true);
   $param_id=ltrim($param_id,"#");
   if(preg_match("/^([0-9]{5})$/",$param_id)){
    $track=substr($param_id,0,3);
    $channel=substr($param_id,3,2);
    $messages=trim(strstr($parsing,":"),":");
    $messages=trim($messages);
    $normalnotes=(intval($channel) >= 11 && intval($channel) < 30);
    $longnotes=(intval($channel) >= 51 && intval($channel) < 70) ;
    if((strlen($messages) % 2 == 1) && ($normalnotes || $longnotes)){
     trigger_error("Illegal message length was detected at channel #${channel} in track #${track}. The message will be excepted from calculation.",E_USER_NOTICE);
     continue;
    }
    $eachmsg=str_split($messages,2);
    $size=count($eachmsg);
    if($normalnotes || $longnotes){
     $i=0;
     for($i=0;$i<=$size;$i++){
      if(intval(base_convert($eachmsg[$i],36,10)) >= 1){
      if($scrnotes == true){ // Scratch Notes
       $is_scrchannel=($channel == self::SCR_LEFT || $channel == self::SCR_RIGHT);
       $is_lnscrchannel=($channel == self::LN_SCR_LEFT || $channel == self::LN_SCR_RIGHT);
        if($normalnotes && $is_scrchannel){
         if($isrdm2 == true && $eachmsg[$i] == $lnmessage){$notes+=0;}
         else{$notes++;}
        }elseif($longnotes && $is_lnscrchannel){ // for long-notes
         if($isrdm2 == true && $eachmsg[$i] == $lnmessage){$notes+=0;}
         else{$notes+=0.5;}
        }
       }else{ // All Notes
        if($normalnotes){
         if($isrdm2 == true && $eachmsg[$i] == $lnmessage){$notes+=0;}
         else{$notes++;}
        }elseif($longnotes){ // for long-notes
         if($isrdm2 == true && $eachmsg[$i] == $lnmessage){$notes+=0;}
         else{$notes+=0.5;}
        }
       }
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
   $ch_params=ltrim(strstr($lines,":",true),"#");
   if(preg_match("/^([0-9]{5})$/",$ch_params)){
    $channel_id=substr($ch_params,3,2);
    $standardnotes=($channel_id >= 11 && $channel_id < 30);
    $longnotes=($channel_id >= 51 && $channel_id < 70);
    if($standardnotes || $longnotes){$buffers[]=(int)$channel_id;}
   }
  }
  $chnum_min=min($buffers);
  $chnum_max=max($buffers);
  switch($chnum_max){
   case self::SP5KEYS:
   case self::LN_SP5KEYS:
   case self::APP1_SP5KEYS: // For Free Zone and FP
   case self::APP2_SP5KEYS:
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
  $_metadatas=$this->parseMetadata();
  if($_metadatas["playtype"]["id"] == 2){ // right-side play
   if($chnum_min == self::RP_SINGLEPLAY){
    switch($chnum_max){
     case self::RP_SP5KEYS:
      $keystype=5; break;
     case self::RP_SP7KEYS:
      $keystype=7; break;
     default: $keystype="?"; break;
    }
   }
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
  $data["number_keysounds"]=0;
  $data["number_images"]=0;
  while(($parsing=fgets($this->handle)) !== false){
   $param=ltrim(strstr($parsing," ",true),"#");
   if(preg_match("/^(WAV)([A-Za-z0-9]{2})$/i",$param)){
    $soundid=substr($param,3,2);
    $soundname=trim(strstr($parsing," "));
    $data["keysounds"][$soundid]=$soundname;
    $data["number_keysounds"]++;
   }elseif(preg_match("/^(BMP)([A-Za-z0-9]{2})$/i",$param)){
    $imageid=substr($param,3,2);
    $imagename=trim(strstr($parsing," "));
    $data["images"][$imageid]=$imagename;
    $data["number_images"]++;
   }
  }
  return $data;
 }
 // array listBPMs(): gets values of all BPMs defined in the file.
 /**
  * @return array $data
  */
 function listBPMs(){
  rewind($this->handle);
  $data=array();
  $data["number_bpms"]=0;
  $metadata=$this->parseMetadata();
  $data["bpms"]["basebpm"]=$metadata["bpm"];
  while(($parsing=fgets($this->handle)) !== false){
   $param=ltrim(strstr($parsing," ",true),"#");
   if(preg_match("/^(BPM)([A-Za-z0-9]{2})$/i",$param)){
    $bpmid=substr($param,3,2);
    $bpmvalue=trim(strstr($parsing," "));
    $data["bpms"][$bpmid]=$bpmvalue;
    $data["number_bpms"]++;
   }
  }
  rewind($this->handle);
  while(($lines=fgets($this->handle)) !== false){
   $param_ch=ltrim(strstr($lines,":",true),"#");
   $channel_id=substr($param_ch,3,2);
   if(preg_match("/^([0-9]{5})$/",$param_ch)){
    $messages=trim(strstr($lines,":"),":");
    $rawbpms=str_split($messages,2);
    $size=count($rawbpms);
    if(intval($channel_id) == 3 || intval($channel_id) == 9){
     for($i=0;$i<=$size;$i++){
      $rawbpm=hexdec($rawbpms[$i]);
      if($rawbpm > 0){
       $data["seqbpms"][$i]=$rawbpm;
       $data["number_bpms"]++;
      }else{continue;}
     }
    }else{continue;}
   }
  }
  if(count($data["bpms"]) > 0 && count($data["seqbpms"]) > 0){
  $temp=array_merge($data["bpms"],$data["seqbpms"]);
  $data["maxbpm"]=max($temp);
  $data["minbpm"]=min($temp);
  }
  return $data;
 }

 // array buildOverallInfo(): gets merged result of remaining methods in this class.
 function buildOverallInfo(){
  $metadata=$this->parseMetadata();
  $numnotes=$this->numNotes();
  $keysused=$this->keysUsed();
  $list_datas=$this->listDatafiles();
  $list_bpms=$this->listBPMs();

  $keysnotes=array("used_keys" => $keysused,"num_notes" => $numnotes);

  return array_merge($metadata,$keysnotes,$list_bpms,$list_datas);
 }
}
?>
