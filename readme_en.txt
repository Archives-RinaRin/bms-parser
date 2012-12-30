- BMS_Parser Class for PHP - 

Made by nandarous (themunyang21 at nate.com)

This file is an PHP class for parsing the BMS (Be-Music Source) files.

[What is BMS?]

BMS is a file format for PC-based rhythm-action simulation games. it was developed by Urao Yane, a Japanese software developer, in 1998.

For more details, please see the Wikipedia article ( http://en.wikipedia.org/wiki/Be-Music_Source ) and the entry on File Formats Wiki ( http://fileformats.wikia.com/wiki/Be-Music_Script )

[Features]

This class supports following variety of BMS Formats:

BMS(Be-Music Script) is the original format that used since BM98(the oldest software that uses BMS format). it uses five keys like Beatmania(a arcade rhythm-action game by Konami, a Japanese company).

BME(Be-Music Extended) is an extended format that uses seven keys like Beatmania IIDX series.

BML(Be-Music Longnote) is an extended format that uses "Long-notes" like Ez2Dj (a Korean rhythm-action game developed since 1999).

PMS(Pop'n Music Script) is the format that uses nine keys like Pop'n Music (a arcade rhythm-action game by Konami).


To use this code, your webhosting provider must support PHP version 5.x or newer.

For the original format specification of BMS files, see http://bm98.yaneu.com/bm98/bmsformat.html


[Methods]

parseMetadata(): Reads metadata(such as basic file information and Artist/Genre/Title/Difficulty/BPM/Totals) from the file. it returns an array containing such informations.

numNotes(): Calculates number of notes in the file. the scheme of note is based on LunaticRave 2, an popular BMS player software that developed in Japan.

keysUsed(): Determines what number of keys used in the file. it returns number of keys used in the file(one of 5/7/9/10/13/14). if not specified, it returns a question mark string.

listDatafiles(): Lists the name of Data Files (key sounds and BGA). it returns a array containing name of such files.

buildOverallInfo(): Builds overall results of the Methods above.


[Usage]

$class=new BMS_Parser("[Target Filename]");
$[VAR_NAME]=$class->[METHOD_NAME]();

[VAR_NAME]: any variable names that can be used in PHP.
[METHOD_NAME]: name of one of the methods described above.


This code is licensed under the GNU Lesser General Public License (GNU LGPL) or a BSD-style licenses.

For texts of the license, please see http://www.gnu.org/licenses/lgpl.html
