<?php
//------------------------------------------------------------------------------
/// Filename:    cal.php
/// Description: Examfilter Calendar file
/// Authors:     Tommy Sparber
///
/// Created:     14.10.2012
//------------------------------------------------------------------------------

error_reporting(E_ALL|E_STRICT);

require_once('./classes/tools.class.php');

require_once('./classes/caltools.class.php');

main();

//------------------------------------------------------------------------------
function main()
{
  $ids = CalTools::loadIds();
  $id = $_GET['id'];

  if(!in_array($id, $ids))
  {
    header('HTTP/1.0 403 Forbidden');
    //die('Not allowed');
    die('403 Forbidden');
  }

  if(!isset($_GET['debug']))
  {
    header("Content-Type: text/calendar; charset=UTF-8");
    header('Content-Disposition: attachment; filename="Pruefungstermine.ics"');
  }

  $data = CalTools::getData();
  $events = CalTools::parseCalendar($data);

  $ignore = file("./storage/cal_$id.txt", FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
  $filter_events = array();

  //replace url
  foreach($events as $key => $event)
  {
    if(!in_array(trim($event['SUMMARY']), $ignore))
    {
      $filter_events[$key] = $event;
      $match = array();
      $eid = 0;

      if(preg_match('/^https:\/\/online.tugraz.at\/tug_online\/wbregisterexam\.lv_termine\?cstp_sp_nr=([0-9]+)&cheader=J&pLvGroupFlag=J$/', $event['URL'], $match))
      {
        $eid = $match[1];
      }

      $filter_events[$key]['URL'] = CalTools::getEventURL($id, $eid);

      if(FALSE == preg_match('/^iOS/', $_SERVER['HTTP_USER_AGENT']) &&
         FALSE == preg_match('/^OS X/', $_SERVER['HTTP_USER_AGENT']))
      {
        $filter_events[$key]['DESCRIPTION'] = CalTools::getEventURL($id, $eid);
      }
      else { /* do not abuse description for url on OSX or iOS */ }
    }
  }

  if(isset($_GET['debug']))
    print '<pre>';

  print CalTools::generateCalendar($filter_events);

  if(isset($_GET['debug']))
    print '</pre>';
}

?>
