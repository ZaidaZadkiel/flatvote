<?php

/**
 * Return the slug of a string to be used in a URL.
 * https://gist.github.com/lucasmezencio/15d23207834a3eade40c5aeec7c1fc5e
 *
 * @return String
 */
function slugify($text){
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicated - symbols
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
}

if( isset($_GET['str']) ) {
  echo slugify(urldecode($_GET['str']));
} 
?>
