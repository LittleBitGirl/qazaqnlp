<?php
class Normalization{
	public $problemsList = [];
	public function Classificator($word){
		global $problemsList;
		$this->caseProblem($word);
		$this->isVowelFrequency($word);
		$this->isConsonantFrequency($word);
		$this->noVowels($word);
		$abbr = 0;
		if($this->isNotWord($word) == true){
			var_dump($problemsList);
			foreach ($problemsList as $key => $value){
				switch ($problemsList[$key]) {
					case 'mixed':
						if($this->isCapitalized($word)){
							$word = mb_strtolower($word);
							$word = ucfirst($word);
							break;
						} else {
							$word = mb_strtolower($word);
							break;
						}
					case 'upper':
						if($this->isAbbreviation($word) == false){
							$word = mb_strtolower($word);
						}
						else{
							$abbr = 1;
						}
						break;
					case 'vowelsRepeat':
						preg_match('([АаӘәІіҮүҰұӨөОоЕеИиЯяЫыЭэЮюИиУуЁё]{2,})', $word, $matches);
						$matchV = array_shift($matches);
						$charset = mb_detect_encoding($matchV,  mb_detect_order(), false);
						if($charset == "UTF-8"){
					        $matchV = mb_convert_encoding($matchV, 'UTF-8', 'UTF-8');    
					    }
					    $matchV = iconv(mb_detect_encoding($matchV, mb_detect_order(), false), "UTF-8//IGNORE", $matchV);
					    $matchV = mb_substr($matchV, 0, -1);
						$word = $this->str_replace_first($matchV, mb_substr($matchV, 0, 1), $word);
						break;
					case 'consonantRepeat':
						preg_match('([БбВвГгҒғДдЖжЗзКкҚқЛлМмНнҢңПпРрСсТтФфХхҺһЦцЧчШшЩщЪъЬь]{3,})', $word, $matches);
						$match = array_shift($matches);
						$charset = mb_detect_encoding($match,  mb_detect_order(), false);
						if($charset == "UTF-8"){
					        $match = mb_convert_encoding($match, 'UTF-8', 'UTF-8');    
					    }
					    $match = iconv(mb_detect_encoding($match, mb_detect_order(), false), "UTF-8//IGNORE", $match);
					    $match = mb_substr($match, 0, -1);
						$word = $this->str_replace_first($match, mb_substr($match, 0, 1), $word);
						break;
					case 'noVowels':
						//array_push($problemsList, 'noVowels');
					default:
						// echo $word.' is normal';
						
						break;
				}
			}
		}
		// if($abbr == 0){
		// 	$spell = new LevenshteinCorrector();
		// 	echo $spell->match($word);
		// } else{
			echo $word;
		// }	
	}

	public function caseProblem($word){
		global $problemsList;
		if(mb_strtolower($word) === $word)
			$m=1;
		else if (mb_strtoupper($word) === $word)
			$problemsList[] = 'upper';
		else
			$problemsList[] = 'mixed';
	}
	public function isNotWord($word){
		if(($word))
			return false;
		else
			return true;
	}
	public function isEmoticon($word){
		global $problemsList;
		$emoticons = file('emoticons.txt', FILE_IGNORE_NEW_LINES);
		foreach ($emoticons as $key => $value) {
			if($emoticons[$key] = $word)
				$problemsList[] = 'emoticon';
			else{

			}
		}
	}

	public function hashOrMention($word){
		global $problemsList;
		if(strpos($word,'#') !== false)
			$problemsList[] = 'hashtag';
		else if(strpos($word, '@') !== false)
			$problemsList[] = 'mention';
		else{

		}
	}
	public function isVowelFrequency($word){
		global $problemsList;
		preg_match('([АаӘәІіҮүҰұӨөОоЕеИиЯяЫыЭэЮюИиУуЁё]{2,})', $word, $matches);
		if(count($matches) >= 1)
			$problemsList[] = 'vowelsRepeat';
		else{

		}
	}
	public function isConsonantFrequency($word){
		global $problemsList;
		preg_match('([БбВвГгҒғДдЖжЗзКкҚқЛлМмНнҢңПпРрСсТтФфХхҺһЦцЧчШшЩщЪъЬь]{3,})', $word, $matches);
		var_dump($matches);
		die(strlen(array_shift($matches)));
		if(count($matches) >= 1 && mb_strlen(array_shift($matches)) >= 2)
			$problemsList[] = 'consonantRepeat';
		else{

		}
	}
	public function noVowels($word){
		global $problemsList;
		if(substr_count($word, '[АаӘәІіҮүҰұӨөОоЕеИиЯяЫыЭэЮюИиУуЁё]') !== 0)
			$problemsList[] = 'noVowels';
		else{
			return;
		}
	}
	public function isCapitalized($word) {
    	$string = mb_substr ($word, 0, 1, "UTF-8");
    	return mb_strtolower($string, "UTF-8") != $string;
	}
	public function isAbbreviation($word) {
		$abbreviations = file('abbreviation.txt', FILE_IGNORE_NEW_LINES);
		foreach ($abbreviations as $key => $value) {
			if($abbreviations[$key] = $word)
				return true;
			else
				return false;
		}
	}
	
	public function str_replace_first($from, $to, $content){
	    $from = '/'.preg_quote($from, '/').'/';

	    return preg_replace($from, $to, $content, 1);
	}
}
class Corrector
{
    protected $possibleTokens = array();
    
    public function __construct(array $possibleTokens = array())
    {
        $this->possibleTokens = $possibleTokens;
    }

    public function correct($input) {
        $guess = $this->match($input);
        if ($guess === $input)
            return $guess;
        else
            return $this->askForGuess($guess) ? $guess : $input;
    }

    public function match($input)
    {
        if (empty($this->possibleTokens))
            return $input;
        $bestSimilarity = -1;
        $bestGuess = $input;
        foreach ($this->possibleTokens as $possibleToken) {
            similar_text($input, $possibleToken, $similarity);
            if ($similarity > $bestSimilarity) {
                $bestSimilarity = $similarity;
                $bestGuess = $possibleToken;
            }
        }
        return $bestGuess;
    }
    private function askForGuess($guess)
    {
        $prompter = new Prompter;
        $answer = $prompter->ask("Did you mean '$guess'?", array('Y','n'), 'Y');
        return !$answer || strtolower($answer) == 'y';
    }
}

class LevenshteinCorrector extends Corrector
{
    
    public function match($input)
    {

        $sozdik_words = file('all_words.txt', FILE_IGNORE_NEW_LINES);
        foreach ($sozdik_words as $key => $value) {
            $sozdik_words[$key] = str_replace('N;', '', $sozdik_words[$key]);
            $sozdik_words[$key] = str_replace('V;', '', $sozdik_words[$key]);
            $sozdik_words[$key] = str_replace('A;', '', $sozdik_words[$key]);
        }
        $shortest = -1;
       
        //$sozdik = $sozdik_words;
        $queue = new SplPriorityQueue();

                
        foreach ($sozdik_words as &$word) {             
            $lev = levenshtein($input, $word);
            $str = '';                      
            if ($lev == 0){               
                $closest = $word;                 
                $shortest = 0;
                break;             
            }
            if($lev <= $shortest || $shortest < 0) { 
                $closest = $word;
                $shortest = $lev;                 
                $queue->insert($word, $lev);           
            }
 
        }
        return $closest;     
    } 
}


$normalizer = new Normalization();
// $normalizer -> Classificator("КӨӨӨӨккктЕМ");
// $normalizer -> Classificator("КИМЭБИ");
$normalizer -> Classificator("маааал");
?>