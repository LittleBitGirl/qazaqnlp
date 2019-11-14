//dummy data 
var testString:String = "Mary had a little lamb her fleece was white as snow"; 
var testString2:String = "The blue zebra had a rad jacket"; 

var nouns:Array = ['cup', 'Mary', 'phone', 'lamb', 'jacket', 'fleece', 'snow', 'zebra']; 
var verbs:Array = ['had', 'was', 'ran', 'jumped', 'read']; 
var adj:Array = ['awesome', 'rad', 'little', 'tall', 'white', 'blue', 'red']; 

//SETUP 
//Create the dictionaries, in a more complex setting you might load data in from an XML file 
//here I'm just pulling the data from the arrays created above 
var nounDict:Dictionary = createDictionary(nouns); 
var verbDict:Dictionary = createDictionary(verbs); 
var adjDict:Dictionary = createDictionary(adj); 

//Creates a dictionary based on an Array of words 
function createDictionary(wordData:Array):Dictionary { 
    var dict:Dictionary = new Dictionary(true); 

    for(var i:uint = 0; i < wordData.length; i++) { 

     //add the word as a key to the dictionary 
     dict[ wordData[i] ] = wordData[i]; 

    } 

    return dict; 
} 


//SEARCHING 
//str is the string you want to search through 
//dict is the dictionary you want to use to search against the string 
function searchDictionary(str:String, dict:Dictionary):Array { 

    //break up the words by the spaces (you can figure out how to deal with punctuation) 
    var words:Array = str.split(' '); 
    //store the matching words in the matches array 
    var matches:Array = []; 

    for(var i:uint = 0; i < words.length; i++) { 


     //check the dictionary for the word 
     if(dict[ words[i] ]) { 
      matches.push(words[i]); 
     } 

    } 
    return matches; 

} 


//TEST IT OUT 
trace(searchDictionary(testString, nounDict)); 
trace(searchDictionary(testString, verbDict)); 
trace(searchDictionary(testString, adjDict) ); 

trace(searchDictionary(testString2, nounDict)); 
trace(searchDictionary(testString2, verbDict)); 
trace(searchDictionary(testString2, adjDict)); 