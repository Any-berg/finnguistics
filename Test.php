<?php
class Language {} # mock parent class to avoid class not found error  
require_once('LanguageFi.php');

$language = new LanguageFi();
$cases = [ 'genitive', 'partitive', 'inessive', 'elative', 'illative' ];
$name = 'inflection.dataset';
$handle = fopen($name, "r");
if ($handle) {
  $words = []; 
  $patterns = [];
  while (($line = fgets($handle)) !== false) {
    $line = trim( $line );
    $len = strlen( $line );
    if ( $len == 0 )
      continue;
    elseif ( $line[0] == '#' ) {
      if ( $len == 1 )
        break;   # if line contains only the initial #, don't read any further
      continue;  # else treat line as comment 
    }
    $data = preg_split( '/,\s*|_/u', trim($line), -1); # PREG_SPLIT_NO_EMPTY);
    if (array_key_exists( $data[0], $words ) && count($words[$data[0]]) != 1)
      continue;
    $_errors = [];
    $suffix = $data[1] ? explode($data[1], $data[0], 2) : [];
    $suffix = count( $suffix ) < 2 || $suffix[1];
    if (count( $data ) == 7) {
      if (strpos( $data[0], $data[1] ) !== 0 ) {
        echo "ERROR 1! line '" . trim( $line ) . "' in $name is not OK\n";
        exit( 1 );
      }
      $patterns = array_slice( $data, 2, 7 );
    }
    elseif ( count( $patterns ) != 5 ) {
      echo "ERROR! 1st line of '$name' is not OK\n";
      exit( 1 );
    }
    elseif ( count( $data ) != 2 || strpos( $data[0], $data[1] ) != 0 ) {
      echo "ERROR 2! line '" . trim( $line ) . "' in $name is not OK\n";
      exit( 1 );
    }
    else
      array_splice( $data, 2, null, $patterns );

    $test = $data[1]; 
    if ( $data[0] == $data[1] && preg_match( '/i$/', $data[1] ) )
      $data[1] = substr( $data[1], 0, -1);
    foreach($cases as $index=>$case) {
      $word = $language->convertGrammar( $data[0], $case, $test );
      if ( $case == 'genitive' && preg_match( '/_/', $word ) ) {
        $_errors[0] = $word;
        break;
      }
      #break;
      $pattern = '/^'.$data[1].$data[$index+2].'$/u';
      if ( !preg_match( $pattern, $word ) ) {
        if (!$_errors)
          $_errors = array_fill(0, count($cases), null);
        $_errors[$index] = $word;
      }
      #break;
    }
    $words[$data[0]] = $_errors;
    #break;
  }
  fclose( $handle );

  $errors = [ 0, 0, 0, 0, 0 ];
  $issues = array_filter($words, function($value) { return !empty($value); });
  $missyllabications = [];
  foreach($issues as $word=>$_errors)
    if (strpos($_errors[0], "_") > -1)
      array_push($missyllabications, $word."->".$_errors[0]);
    else {
      echo $word."->".implode(',',array_filter($_errors))."\n";
      foreach($_errors as $index=>$case)
        if ($case)
          $errors[$index]++;
    }
  if (array_sum( $errors ) > 0) {
    foreach($cases as $index=>$case)
      echo ($index == 0 ? '' : ($index < count( $cases )-1 ? ', ' : ' & ')).
           "$errors[$index] $case";
    echo " errors\n";
  }
  $n = count($missyllabications);
  if ($n > 0)
    echo $n." missyllabication".($n > 1 ? 's' : '').":\n".
      implode(',', $missyllabications)."\n";
  $n = count($words);
  echo ($n-count($issues)).'/'.$n." words passed\n";

  exit(count($issues) > 0 ? 2 : 0);

} else {
  // error opening the file.
}
?>
