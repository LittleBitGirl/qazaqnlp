<?php

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

$text = "";


$spell = new LevenshteinCorrector();
echo $spell->match("көөөөөөл");
?>