<?php
//------------------------------------------------------------------------------
/// Filename:    tools.class.php
/// Description: Some tools, every function should be static
/// Authors:     Tommy Sparber
///
/// Created:     15.10.2012
//------------------------------------------------------------------------------

class Tools
{
  public static function debugArray($array)
  {
    return '<pre>'.print_r($array, true).'</pre>';
  }

  public static function cleanInput($data)
  {
    return htmlspecialchars(trim($data));
  }

  public static function arrayToFile($array, $id)
  {
    $handle = fopen("./storage/cal_$id.txt", 'w');
    $data = '';
    foreach($array as $line)
    {
      $data .= $line . "\n";
    }

    fwrite($handle, $data);
    fclose($handle);
  }
}

?>
