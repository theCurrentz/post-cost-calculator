<?php
//abstract class containing post calculator methods and meta box methods for the post cost plugin
abstract class Post_Cost_Calculator
{
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

								if ( $field_object_type == 'wysiwyg' || $field_object_type == 'text' || $field_object_type == 'textarea' || $field_object_type == 'number' ) {

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

    //ACF blacklist array
    if (!empty(get_option('acfblacklist'))) {
      $acf_blacklist = explode(' ', get_option('acfblacklist'));
    } else {
      $acf_blacklist = null;
    }

  	if (is_array($value)) {
  			foreach ($value as $key => $subvalue) {
          if($acf_blacklist !== null) {
            foreach($acf_blacklist as $black_key) {
              if(preg_match('/'.$black_key.'/', $key)) {
                  continue;
              }
              else
              {
                $repeatCount .= Post_Cost_Calculator::repeater_field_capture($subvalue);
              }
            }
          } else {
            $repeatCount .= Post_Cost_Calculator::repeater_field_capture($subvalue);
          }
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
