
<?php

	function makeTriples(){

		$open_file = fopen("../dorian_gray.txt", "r");
		$corpus = "";
		for ($i=0; $i < 50000; $i++) { 
			$corpus .= fgets($open_file);
		}
		// Splits things the correct way, i have not idea how or why it works
		
		$words = preg_split('#\s+#', $corpus, null, PREG_SPLIT_NO_EMPTY);
		$seeds = array();

		$next_is_seed = False;
		for ($i=0; $i < count($words); $i++) {
			$words[$i] = str_replace("\"", "", strtolower($words[$i]));
		}
		
		// Corpus loaded, all strings lowercase and in an array $words

		$triples_database = array();
		$temp_key = ""; 

		for ($i=0; $i < count($words) - 2; $i++) { 
			$temp_key = $words[$i] . " " . $words[$i+1];
			
			if (array_key_exists($temp_key, $triples_database)) {
				array_push($triples_database[$temp_key], $words[$i+2]);
			}
			else {
				$triples_database[$temp_key] = array($words[$i+2]);
			}
			
		}

		return $triples_database;
	}

		// Triples Database is loaded
	function makeBlurb() {

		$triples_database = makeTriples();

		$n_of_words = 50;

		// Starts with a random word.
		$out_string = array_keys($triples_database)[rand(0, count($triples_database))];

		$temp_words = array();

		for ($i=0; $i < $n_of_words; $i++) { 
			
			$temp_words = preg_split('#\s+#', $out_string, null, PREG_SPLIT_NO_EMPTY);

			$temp_key = $temp_words[count($temp_words) - 2] . " " . $temp_words[count($temp_words) - 1]; 			$out_string .= " ";
			$out_string .= $triples_database[$temp_key][rand(0, count($triples_database[$temp_key]))];
		}

		// Ensures the last word ends with punctuation, naturally.

		$temp_words = preg_split('#\s+#', $out_string, null, PREG_SPLIT_NO_EMPTY);
		
		while (substr($temp_words[count($temp_words)-1], -1, 1) != "." && 
				substr($temp_words[count($temp_words)-1], -1, 1) != "!" &&
				substr($temp_words[count($temp_words)-1], -1, 1) != "?")
		{
			$temp_key = $temp_words[count($temp_words) - 2] . " " . $temp_words[count($temp_words) - 1]; 			$out_string .= " ";
			$out_string .= $triples_database[$temp_key][rand(0, count($triples_database[$temp_key]))];
			$temp_words = preg_split('#\s+#', $out_string, null, PREG_SPLIT_NO_EMPTY);
		}


		echo caps($out_string);
	}

	function caps($in_string) {

		$words = preg_split('#\s+#', $in_string, null, PREG_SPLIT_NO_EMPTY);

		$words[0] = ucFirst($words[0]);

		for ($i=1; $i < count($words); $i++) { 
			// If the previous word's last character is "!, ., or ?", capitalize this word
			
			if (substr($words[$i-1], -1, 1) == "." || 
				substr($words[$i-1], -1, 1) == "!" ||
				substr($words[$i-1], -1, 1) == "?") 
			{
				$words[$i] = ucFirst($words[$i]);
			}
		}

		$out_string = "";

		foreach ($words as $word) {
			$out_string .= $word;
			$out_string .= " ";
		}

		return $out_string;
	}

	if ($_POST["functionname"] == "makeBlurb") {
		echo makeBlurb();
	}
	