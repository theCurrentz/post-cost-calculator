<?php
//abstract class containing post calculator methods and meta box methods for the post cost plugin
abstract class Post_Cost_Calculator
{
  //add meta box to post backend
  public static function add( $post ) {
  	add_meta_box( 'post_cost_meta_box', 'Post Info:', [self::class, 'build'], 'post', 'side', 'core' );
  }

  //build html for metabox
  public static function build( $post ) {
    //query google search with content concatenated.
    $wordConcat = $post->post_content . self::acf_word_count($post);
    $wordConcat = preg_replace("/(?![.=$'€%-])\p{P}/u", "",$wordConcat);
    $wordConcat = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $wordConcat);
    //replace spaces with +
    $wordConcat = preg_replace("/\s+/", "+", $wordConcat);
    //replace ' apostraphes with %27
    $wordConcat = preg_replace("/'/", "%27", $wordConcat);
    $wordConcat = trim($wordConcat);
    $wordConcat = html_entity_decode($wordConcat);
    $wordConcat = strip_tags($wordConcat);
   ?>
  	<p><strong>Word Count:</strong> <?php echo self::calculate_word_count($post); ?></p>
    <div class="button button-primary button-large" onclick="checkPlag('<?php echo $wordConcat;?>')">
      Check Plagiarization
    </div>
    <script>
      function checkPlag(content) {
        var splitArray = content.split("+");
        var currentString = "";
        for (var i = 0, len = splitArray.length; i < len; i++) {
            currentString += splitArray[i] + "+";
            if (i != 0 && i % 32 == 0) {
              window.open('https://google.com/search?q=' + currentString + '');
              currentString = "";
            }
        }
        if (!currentString === "") {
          window.open('https://google.com/search?q=' + currentString + '');
        }
        return;
      }
    </script>
  <?php
  }

  //functions to calculate ACF word count
  public static function acf_word_count($post) {

	//ACF whitelist array
		$acf_whitelist = explode(' ', get_option('acfwhitelist'));

		if (function_exists ('get_fields') ) {

				$acf_count= "";
				$fields = get_fields($post);

				if ($fields) {

						foreach($fields as $name => $value) {

							if (in_array($name, $acf_whitelist) ) {

								$field_object = get_field_object($name);
								$field_object_type = $field_object['type'];


								if ( $field_object_type == 'wysiwyg' || $field_object_type == 'text' || $field_object_type == 'number' ) {

										$acf_count .= " " . $value;

								} elseif ( $field_object_type == 'repeater' ) {

										$acf_count .= " " . self::repeater_field_capture($field_object['value']);

											}
									 }
								}
						}

						return $acf_count;
				}
				 return "";
		}

  public static function repeater_field_capture($value) {
  	$repeatCount = "";
  	if (is_array($value)) {
  			foreach ($value as $key => $subvalue) {
  					$repeatCount .= Post_Cost_Calculator::repeater_field_capture($subvalue);
  			}
  	} else {
  			$repeatCount .= " " . $value;
  	}
  	return $repeatCount;
  }


//Calculate word count
public static function calculate_word_count($post) {
  	$wordCount = 0;
  	$wordCount = preg_replace("/(?![.=$'€%-])\p{P}/u", "",$post->post_content);
  	$wordCount = preg_replace("#<(.*)/(.*)>#iUs", "", $wordCount);
  	$wordCount = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $wordCount);
  	$wordCount = trim($wordCount);
  	$wordCount = html_entity_decode($wordCount);
  	$wordCount = strip_tags($wordCount);

  	$wordCount = str_word_count($wordCount);

  	$repeatCount = 0;
  	$repeatCount = Post_Cost_Calculator::acf_word_count($post);
  	$repeatCount = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $repeatCount);
  	$repeatCount = str_word_count(strip_tags(html_entity_decode(trim($repeatCount))));

  	return $wordCount + $repeatCount;
  }
}

//invoke class and methods for adding meta box
add_action('add_meta_boxes', ['Post_Cost_Calculator', 'add']);
