<?php
//------------------------------------------------------------------------------
/// Filename:    caltools.class.php
/// Description: Some calender related tools, every function should be static
/// Authors:     Tommy Sparber
///
/// Created:     15.10.2012
//------------------------------------------------------------------------------

class CalTools
{
  public static function getWebData($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $cal_input = curl_exec($ch);
    curl_close($ch);

    return $cal_input;
  }

  public static function parseCalendar($data)
  {
    $lines = explode("\n", $data);

    $events = array();
    $event_id = -1;
    $event_start = false;

    foreach($lines as $line)
    {
      if($line == trim('BEGIN:VEVENT'))
      {
        $event_id++;
        $event_start = true;
      }
      elseif($line == trim('END:VEVENT'))
      {
        $event_start = false;
      }
      elseif($event_id > -1 && trim($line) != '' && $event_start == true)
      {
        $event_item = explode(':', $line, 2);
        $events[$event_id][$event_item[0]] = $event_item[1];
      }
    }

    return $events;
  }

  public static function generateCalendar($events)
  {
    $data = "BEGIN:VCALENDAR\n".
            "METHOD:PUBLISH\n".
            "VERSION:2.0\n".
            "PRODID:-//Pruefungstermine Bakk Telematik Filterd by Bagru//DE\n".
            "X-WR-TIMEZONE:Europe/Vienna\n\n";

    foreach($events as $event)
    {
      $data .= "BEGIN:VEVENT\n";

      foreach($event as $key => $value)
      {
        $data .= $key . ':' . $value . "\n";
      }

      $data .= "END:VEVENT\n\n";
    }

    $data .= "END:VCALENDAR\n";

    return $data;
  }

  public static function generateId()
  {
    $id = '';

    for ($i = 0; $i < 6; $i++)
    {
      $id .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
    }

    return $id;
  }

  public static function loadIds()
  {
    $ids = array();

    $handle = opendir('storage');

    if($handle)
    {
      while(false !== ($entry = readdir($handle)))
      {
        $match = array();

        if(preg_match('/^cal_([a-z0-9]{6})\.txt$/', $entry, $match))
        {
          $ids[] = $match[1];
        }
      }
      closedir($handle);
    }

    return $ids;
  }

  public static function getCalURL($id)
  {
    return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'cal.php?id='.$id;
  }

  public static function getEventURL($id, $eid)
  {
    return 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).(substr(dirname($_SERVER['REQUEST_URI']), -1) != '/' ? '/' : '').'e.php?id='.$id.'&eid='.$eid;
  }
}
?>
