<?php
//------------------------------------------------------------------------------
/// Filename:    e.php
/// Description: Examfilter edit event file
/// Authors:     Tommy Sparber
///
/// Created:     14.10.2012
//------------------------------------------------------------------------------

error_reporting(E_ALL|E_STRICT);

require_once('./classes/template.class.php');
require_once('./classes/tools.class.php');
require_once('./classes/form.class.php');

require_once('./classes/caltools.class.php');

main();

//------------------------------------------------------------------------------
function main()
{
  $template = new Template();

  $template->setTitle("ExamFilter - TUGRAZ");

  $ids = CalTools::loadIds();
  $id = $_GET['id'];
  $eid = intval(isset($_GET['eid'])?$_GET['eid']:0);

  if(!in_array($id, $ids))
  {
    header('HTTP/1.0 403 Forbidden');
    $template->addContent('403 Forbidden');
  }
  else
  {
    $url = "http://telematik.edu/bakk_exams.ics";
    $data = CalTools::getWebData($url);
    $events = CalTools::parseCalendar($data);
    $sevent = 0;

    foreach($events as $key => $event)
    {
      $match = array();

      if(preg_match('/^https:\/\/online.tugraz.at\/tug_online\/wbregisterexam\.lv_termine\?cstp_sp_nr=([0-9]+)&cheader=J&pLvGroupFlag=J$/', $event['URL'], $match))
      {
        if($eid == $match[1])
        {
          $sevent = $event;
          break;
        }
      }
    }

    $form = new Form('filter');
    $ignore = file("./storage/cal_$id.txt", FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

    if($sevent != 0)
    {
      $template->addContent('<h2>'.$sevent['SUMMARY'].'</h2>');

      $form->addSubmit('submit_filter', 'Diese LV filtern');
      $form->addSubmit('submit_register', 'Zur Prüfungsanmeldung');
    }

    buildSelect($ignore, $form);
    $form->addSubmit('submit_rem_filter', 'Filter löschen');

    if($form->isReady())
    {
      $fields = $form->getFields();

      if(isset($fields['submit_register']) && $fields['submit_register']['clicked'] != 0)
      {
        header("Location: https://online.tugraz.at/tug_online/wbregisterexam.lv_termine?cstp_sp_nr=$eid&cheader=J&pLvGroupFlag=J", true, 303);
        exit;
      }
      else if(isset($fields['submit_filter']) && $fields['submit_filter']['clicked'] != 0)
      {
        if(!in_array(trim($sevent['SUMMARY']), $ignore))
        {
          $ignore[] = trim($sevent['SUMMARY']);
        }

        Tools::arrayToFile($ignore, $id);
        buildSelect($ignore, $form);
        $template->addMsg('ok', 'LV eingetragen');
        $template->addContent($form->getFormHTML());
      }
      else if($fields['submit_rem_filter']['clicked'] != 0)
      {
        if($fields['rem_filter']['value'] != '-')
        {
          foreach($ignore as $key => $value)
          {
            if($value == $fields['rem_filter']['value'])
            {
              $template->addMsg('ok', 'LV ausgetragen');
              unset($ignore[$key]);
              break;
            }
          }

          Tools::arrayToFile($ignore, $id);
        }

        buildSelect($ignore, $form);
        $template->addContent($form->getFormHTML());
      }
    }
    else
    {
      $template->addContent($form->getFormHTML());
    }
  }

  print $template->out();
}

function buildSelect($ignore, $form)
{
  $options = array();
  $options[] = array('desc' => '-', 'value' => '-');

  foreach($ignore as $line)
  {
    $options[] = array('desc' => $line, 'value' => $line);
  }
  $form->addSelect('rem_filter', 'Filter löschen: ', $options, '-', false);
}

?>
