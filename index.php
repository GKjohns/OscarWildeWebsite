<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Wilde Style</title>
		<meta name="description" content="An algorithm that creates short blurbs in the style of Oscar Wilde">

		<!-- Style imports, Jquery, typed.js, etc. -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Playfair+Display' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="style/style.css">
		<script src="js/typed_file.js" type="text/javascript"></script>
	</head>
	<body>
		<?php

			$open_file = fopen("dorian_gray.txt", "r");
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

			// Triples Database is loaded

			function makeBlurb($triples_database) {

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
		?>
		<div id="banner">
			<h1>Wilde Style</h1>
			<br>
			<p>An algorithm that creates unique blurbs in the style of Oscar Wilde.</p>
		</div>

		<div id="blurb_container">
			<div id="generated_text">
					<?php makeBlurb($triples_database); ?>
			</div>
		</div>

		<div id="button">
			<a id="generate_button" class="button" href="#">
				<span id="button_text" class="button">Generate Text</span>
			</a>
		</div>

		<div id="footer">
			<span id="name">GKJ</span>
			<span id="copywrite">&copy;2015</span>
		</div>

		<script type="text/javascript">

			var button_clicked = false;

			// Hides the blurb
			$("#blurb_container").hide();
			$("#generated_text").hide();

			// Slides button down, makes the blurb appear
			$("#generate_button").click(function(event){

				event.preventDefault();

				if (!button_clicked) {
					$("#blurb_container").slideDown(1500);
					$("#generated_text").delay(1600).fadeIn(1200);
					button_clicked = true;
				}

				else {
					$("#generated_text").fadeOut(600);
					$.ajax({
				        type: "POST",
				        url: 'php_functions/funcs.php',
				        data: {functionname: 'makeBlurb'},
				        success: function(data) {
				        	//$("#generated_text").text(data);
				        	$("#generated_text").text(data);
				        }
					});
					$("#generated_text").fadeIn(1200);
				}
			});
		</script>

		<!-- HTML, CSS, and Js from scratch, biatch -->
	</body>
</html>
