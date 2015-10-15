<?php

class PronunciationTestController extends Controller
{

	static $allPinyins=array(
	'zhi', 	'chi', 	'shi', 	'ri', 	'zi', 	'ci', 	'si', 	
 	'a', 	'ba', 	'pa', 	'ma', 	'fa', 	'da', 	'ta', 	'na', 	'la', 	'ga', 	'ka', 	'ha', 				'zha', 	'cha', 	'sha', 		'za', 	'ca', 	'sa', 	
 	'o', 	'bo', 	'po', 	'mo', 	'fo', 				'lo', 														
 	'e', 			'me', 		'de', 	'te', 	'ne', 	'le', 	'ge', 	'ke', 	'he', 				'zhe', 	'che', 	'she', 	're', 	'ze', 	'ce', 	'se', 	
 	'ê', 																						
 	'ai', 	'bai', 	'pai', 	'mai', 		'dai', 	'tai', 	'nai', 	'lai', 	'gai', 	'kai', 	'hai', 				'zhai', 	'chai', 	'shai', 		'zai', 	'cai', 	'sai', 	
 	'ei', 	'bei', 	'pei', 	'mei', 	'fei', 	'dei', 	'tei', 	'nei', 	'lei', 	'gei', 	'kei', 	'hei', 				'zhei', 		'shei', 		'zei', 		'sei', 	
 	'ao', 	'bao', 	'pao', 	'mao', 		'dao', 	'tao', 	'nao', 	'lao', 	'gao', 	'kao', 	'hao', 				'zhao', 	'chao', 	'shao', 	'rao', 	'zao', 	'cao', 	'sao', 	
 	'ou', 		'pou', 	'mou', 	'fou', 	'dou', 	'tou', 	'nou', 	'lou', 	'gou', 	'kou', 	'hou', 				'zhou', 	'chou', 	'shou', 	'rou', 	'zou', 	'cou', 	'sou', 	
 	'an', 	'ban', 	'pan', 	'man', 	'fan', 	'dan', 	'tan', 	'nan', 	'lan', 	'gan', 	'kan', 	'han', 				'zhan', 	'chan', 	'shan', 	'ran', 	'zan', 	'can', 	'san', 	
 	'en', 	'ben', 	'pen', 	'men', 	'fen', 	'den', 		'nen', 		'gen', 	'ken', 	'hen', 				'zhen', 	'chen', 	'shen', 	'ren', 	'zen', 	'cen', 	'sen', 	
 	'ang', 	'bang', 	'pang', 	'mang', 	'fang', 	'dang', 	'tang', 	'nang', 	'lang', 	'gang', 	'kang', 	'hang', 				'zhang', 	'chang', 	'shang', 	'rang', 	'zang', 	'cang', 	'sang', 	
 	'eng', 	'beng', 	'peng', 	'meng', 	'feng', 	'deng', 	'teng', 	'neng', 	'leng', 	'geng', 	'keng', 	'heng', 				'zheng', 	'cheng', 	'sheng', 	'reng', 	'zeng', 	'ceng', 	'seng', 	
 	'er', 																						

 	'ya', 					'dia', 		'nia', 	'lia', 				'jia', 	'qia', 	'xia', 								
 	'yo', 																						
 	'ye', 	'bie', 	'pie', 	'mie', 		'die', 	'tie', 	'nie', 	'lie', 				'jie', 	'qie', 	'xie', 								
 	'yai', 																						
 	'yao', 	'biao', 	'piao', 	'miao', 	'fiao', 	'diao', 	'tiao', 	'niao', 	'liao', 				'jiao', 	'qiao', 	'xiao', 								
 	'you', 			'miu', 		'diu', 		'niu', 	'liu', 				'jiu', 	'qiu', 	'xiu', 								
 	'yan', 	'bian', 	'pian', 	'mian', 		'dian', 	'tian', 	'nian', 	'lian', 				'jian', 	'qian', 	'xian', 								
 	'yin', 	'bin', 	'pin', 	'min', 				'nin', 	'lin', 				'jin', 	'qin', 	'xin', 								
 	'yang', 	'biang', 				'diang', 		'niang', 	'liang', 				'jiang', 	'qiang', 	'xiang', 								
 	'ying', 	'bing', 	'ping', 	'ming', 		'ding', 	'ting', 	'ning', 	'ling', 				'jing', 	'qing', 	'xing', 								

 	'wa', 									'gua', 	'kua', 	'hua', 				'zhua', 	'chua', 	'shua', 	'rua', 				
 	'wo', 					'duo', 	'tuo', 	'nuo', 	'luo', 	'guo', 	'kuo', 	'huo', 				'zhuo', 	'chuo', 	'shuo', 	'ruo', 	'zuo', 	'cuo', 	'suo', 	
 	'wai', 									'guai', 	'kuai', 	'huai', 				'zhuai', 	'chuai', 	'shuai', 					
 	'wei', 					'dui', 	'tui', 			'gui', 	'kui', 	'hui', 				'zhui', 	'chui', 	'shui', 	'rui', 	'zui', 	'cui', 	'sui', 	
 	'wan', 					'duan', 	'tuan', 	'nuan', 	'luan', 	'guan', 	'kuan', 	'huan', 				'zhuan', 	'chuan', 	'shuan', 	'ruan', 	'zuan', 	'cuan', 	'suan', 	
 	'wen', 					'dun', 	'tun', 	'nun', 	'lun', 	'gun', 	'kun', 	'hun', 				'zhun', 	'chun', 	'shun', 	'run', 	'zun', 	'cun', 	'sun', 	
 	'wang', 									'guang', 	'kuang', 	'huang', 				'zhuang', 	'chuang', 	'shuang', 					
 	'weng', 					'dong', 	'tong', 	'nong', 	'long', 	'gong', 	'kong', 	'hong', 				'zhong', 	'chong', 	'shong', 	'rong', 	'zong', 	'cong', 	'song', 	

 	'yue', 							'nve', 	'lve', 				'jue', 	'que', 	'xue', 								
 	'yuan', 												'juan', 	'quan', 	'xuan', 								
 	'yun', 								'lvn', 				'jun', 	'qun', 	'xun', 								
 	'yong', 												'jiong', 	'qiong', 	'xiong'		
	);
	
	
	function getAllForCombination($firstLeft, $firstRight, $secondLeft, $secondRight) {
		foreach(self::$allPinyins as $leftTranscription)
			foreach(self::$allPinyins as $rightTranscription) {
				$queryFirst="$leftTranscription$firstLeft $rightTranscription$firstRight";
				$querySecond="$leftTranscription$secondLeft $rightTranscription$secondRight";
				
				$firstResult=DictEntryPhrase::model()->findAll("transcription='$queryFirst'");
				$secondResult=DictEntryPhrase::model()->findAll("transcription='$querySecond'");
				
				if(!empty($firstResult) && !empty($secondResult)) {
				
					echo "<h2>3, 4</h2>";
					echo "<ul>";
					foreach($firstResult as $r) {
						echo "<li>";
						echo $r->simplified;
						echo "\n";
					}
					echo "</ul>";
					
					echo "<h2>4, 4</h2>";
					echo "<ul>";
					foreach($secondResult as $r) {
						echo "<li>";
						echo $r->simplified;
						echo "\n";
					}
					echo "</ul>";				
				}
			}
		return array($firstResult, $secondResult);
	}
	
	public function actionGenerateTones() {
		$results=$this->getAllForCombination(3, 4, 4, 4);
		$firstResult=$results[0]; 
		$secondResult=$results[1];
		

		
		//$allDict=DictEntryPhrase::model()->findAll();

		
		//foreach tone combinations
		//foreach pinyin*2
		//get various
	}
}