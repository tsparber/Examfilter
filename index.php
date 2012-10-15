<?php
//------------------------------------------------------------------------------
/// Filename:    index.php
/// Description: Examfilter Mainfile
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

  $form = new Form('generatedid');
  $form->addSubmit('submit', 'Generate new personal calendar');

  if($form->isReady())
  {
    //$fields = $form->getFields();
    $ids = CalTools::loadIds();
    $id = 0;

    do
    {
      $id = CalTools::generateId();
    }while(in_array($id, $ids));

    $handle = fopen("storage/cal_$id.txt", 'w') or die("can't open file");
    fclose($handle);

    $template->addContent('<a href="'.CalTools::getCalURL($id).'">' . CalTools::getCalURL($id) . '</a>');
  }
  else
  {
    $template->addContent($form->getFormHTML());
  }

  print $template->out();
}

?>
