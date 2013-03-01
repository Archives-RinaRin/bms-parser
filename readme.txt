- BMS_Parser Class for PHP - 

Made by nandarous aka. Ceruria*

이 파일은 BMS(Be-Music Script) 파일의 정보를 읽을수 있게 해주는 PHP용 클래스입니다.
BMS가 뭔지 알고싶다면 위키피디아 항목( http://ko.wikipedia.org/wiki/Be-Music_Script )과 KBSP 2011 공식 사이트( http://bmpolaris.xo.st/ )의 메인페이지에 적혀져 있는 설명을 읽어주시기 바랍니다.

이 클래스는 다음과 같은 형식을 지원합니다:
BMS(Be-Music Script) - 예전 BM98(BMS 파일 형식을 최초로 사용한 프로그램)에서 사용되던, 비트매니아 클래식과 같이 5/10키를 사용하는 데이터
BME(Be-Music Extended) - 비트매니아 IIDX와 같이 7/14키를 지원하는 데이터
BML(Be-Music Longnote) - 국산 리듬게임인 Ez2Dj와 같이 롱노트를 지원하는 데이터
PMS(Pop'n Music Script) - 코나미의 팝픈뮤직(Pop'n Music)과 같이 9키를 사용하는 데이터


BM98의 제작자인 Urao Yane 님이 작성한 BMS 파일 포맷에 대한 원본 명세를 보시려면 http://bm98.yaneu.com/bm98/bmsformat.html (영어)를 참고하세요.


이 파일을 사용하기 위해서는:

- 호스팅 서버에 PHP 5.x 버전이 세팅되어 있어야 합니다.
- 사용하고자 하는 홈페이지에 자신이 저작권을 소유하고 있는 BMS나 다른 저작권 문제가 없는 BMS 파일이 올려져 있어야 합니다.

들어있는 함수(메서드):

parseMetadata(): BMS 파일의 메타데이터(기본적인 파일정보 및 작곡자/장르/제목/표기난이도/기본설정[BPM/TOTAL 수치] 등)를 읽어들입니다.
반환값은 읽어들인 데이터가 포함되어 있는 배열입니다.

numNotes(): BMS 파일의 노트 수를 계산합니다. 노트 수는 롱노트 콤보가 적용되지 않을 때(가장 많이 사용되는 구동기인 LunaticRave 2 등)를 기준으로 삼습니다.
반환값은 그 파일의 노트 수입니다.

keysUsed(): BMS 파일이 사용하는 건반 수를 알아냅니다.
반환값은 그 파일이 사용하는 건반 수입니다. 건반 수를 알 수 없을 경우 물음표를 반환합니다.

listDatafiles(): BMS 파일에서 사용하는 데이터(키음/BGA) 파일의 이름을 읽어들입니다.
반환값은 해당 파일들의 이름이 들어있는 배열입니다.

listBPMs(): BMS 파일에서 BPM 수치들을 읽어들입니다.
반환값은 해당 수치들과 그 수치들의 최대/최솟값이 들어있는 배열입니다.

buildOverallInfo(): 위 함수들의 반환값들을 모두 합쳐서 반환합니다.
성능을 위해서 이 함수는 단독으로 사용할 것을 추천합니다.

사용 방법의 예:

$class=new BMS_Parser("[열고자 하는 파일이름]");
$[VAR_NAME]=$class->[METHOD_NAME](); 의 형식으로 호출한다.

[VAR_NAME]: 변수 이름. 아무 이름이나 배정해도 됩니다. 단, PHP에서 사용가능한 변수 이름이어야 합니다.
[METHOD_NAME]: 위의 메서드 이름들 중 하나.


이 클래스에 대한 문의사항이 있으면 themunyang21@nate.com로 메일을 보내거나 트위터 @nandarous로 멘션을 보내주시기 바랍니다.

참고사항:

- 아직까지 압축파일 안에 담겨져 있는 BMS는 읽지 못합니다. 차후 읽을 수 있도록 수정해 나가겠습니다.
- 아직 초기버전이라 일부 파일(특히 루브잇 전용으로 만들어진 BMS)의 정보를 제대로 읽지를 못합니다. 이 점은 차후 수정해 나가겠습니다.
- 이 클래스는 Urao Yane 님과 BMS 구동기 제작자들과 직접적인 관련이 없습니다.


저작권 정책:

- 이 클래스는 GNU 약소 공중 사용 허가서(GNU Lesser General Public License, GNU LGPL, 2.0버전 이상) 혹은 BSD 허가서(BSD License)를 따릅니다.
GNU LGPL 라이센스 전문(3.0버전 기준)은 http://www.gnu.org/licenses/lgpl.html 문서 (영어)를 참고해 주세요.


Future plans:
- BMS file highlighting
- Visualization of BMS file's object arrangement
