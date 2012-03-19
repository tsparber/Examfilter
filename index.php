<?php
  // Tommy Sparber - fast hack to filter the ics file

  $key = file_get_contents("key.txt"); //-> store any key, which needs to be added

  if($_GET["key"] != trim($key))
  {
    die("Not allowed!");
  }

  $url = "http://telematik.edu/bakk_exams.ics";

  $ignore = file("ignore.txt"); //Line by line: The summary to be ignored

  header("Content-Type: text/calendar; charset=UTF-8");
  header('Content-Disposition: attachment; filename="Pruefungstermine.ics"');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $cal_input = curl_exec($ch);
  curl_close($ch);

  $cal_line = explode("\n", $cal_input);

  $cal_output = "BEGIN:VCALENDAR
METHOD:PUBLISH
VERSION:2.0
PRODID:-//Pruefungstermine Bakk Telematik Filterd by Bagru//DE
X-WR-TIMEZONE:Europe/Vienna\n\n";

  for($i = 0; $i < count($cal_line); $i++)
  {
    if($cal_line[$i] == "BEGIN:VEVENT")
    {
      if(!in_array(htmlentities(utf8_decode(substr($cal_line[$i+3], 8)))."\n", $ignore))
      {
        $cal_output .= $cal_line[$i++] . "\n";
        $cal_output .= $cal_line[$i++] . "\n";
        $cal_output .= $cal_line[$i++] . "\n";
        $cal_output .= $cal_line[$i++] . "\n";
        $cal_output .= $cal_line[$i++] . "\n";
        $cal_output .= $cal_line[$i++] . "\n";
        $cal_output .= $cal_line[$i++] . "\n";
        $cal_output .= $cal_line[$i++] . "\n";
      }
    }
  }

  $cal_output .= "END:VCALENDAR\n";

  print $cal_output;
?>
