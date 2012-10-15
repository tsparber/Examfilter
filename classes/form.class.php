<?php
//------------------------------------------------------------------------------
/// Filename:    from.class.php
/// Description: A class so fast generate input forms
/// Authors:     Tommy Sparber
///
/// Created:     15.10.2012
//------------------------------------------------------------------------------

class Form
{
  private $m_form = array();
  private $m_action_id;
  private $m_method;
  private $m_missing = array();

  public function __construct($action_id, $method = 'post')
  {
    $this->m_action_id = $action_id;
    $this->m_method = $method;
  }

  private function getItemHTML($id, $item)
  {
    switch($item['type'])
    {
      case 'text':
        return '<label for="'.$id.'">'.$item['desc'].'</label><input type="text" name="'.$id.'" id="'.$id.'" value="'.$item['value'].'" /><br />'."\n";
        break;

      case 'radio':
        $str = '<br />';

        foreach($item['items'] as $itemid => $radioitem)
        {
          $checked = '';

          if(self::dataInput($id, $item['value']) == $radioitem['value'])
          {
            $checked = 'checked="checked" ';
          }

          $str .= '<label class="radio" for="'.$id.$itemid.'">'.$radioitem['desc'].'</label><input type="radio" name="'.$id.'" id="'.$id.$itemid.'" value="'.$radioitem['value'].'" '.$checked.'/>'."\n";
        }

        $str .= "<br /><br />";

        return $str;
        break;

      case 'select':
        $str = '<br />';
        $str .= '<label class="select" for="'.$id.'">'.$item['desc'].'</label><select id="'.$id.'" name="'.$id.'">';

        foreach($item['options'] as $option)
        {
          $selected = '';

          if(self::dataInput($id, $item['value']) == $option['value'])
          {
            $selected = ' selected="selected"';
          }

          $str .= '<option value="'.$option['value'].'"'.$selected.'>'.$option['desc'].'</option>'."\n";
        }

        $str .= "</select>";

        return $str;
        break;

      case 'password':
        return '<label for="'.$id.'">'.$item['desc'].'</label><input type="password" name="'.$id.'" id="'.$id.'" value="'.$item['value'].'" /><br />'."\n";
        break;

      case 'textarea':
        return '<label for="'.$id.'">'.$item['desc'].'</label><textarea name="'.$id.'" id="'.$id.'">'.$item['value'].'</textarea><br />'."\n";
        break;

      case 'checkbox':
        $checked = '';

        if(self::dataInput($id, $item['checkedvalue']) == $item['checkvalue'])
        {
          $checked = 'checked="checked" ';
        }

        return '<input class="checkbox" type="checkbox" name="'.$id.'" id="'.$id.'" value="'.$item['checkvalue'].'"  '.$checked.'/><label class="checkbox" for="'.$id.'">'.$item['desc'].'</label><br />'."\n";
        break;

      case 'submit':
        return '<input type="submit" name="'.$this->m_action_id.'_'.$id.'" value="'.$item['value'].'" />';
        break;
    }

  }

  private static function dataInput($id, $default_value)
  {
    if(isset($_REQUEST[$id]))
    {
      return Tools::cleanInput($_REQUEST[$id]);
    }
    else
    {
      return $default_value;
    }
  }

  private function checkRequiredFields()
  {
    $this->m_missing = array();

    foreach($this->m_form as $id => $item)
    {
      if($item['required'] && empty($item['value']))
      {
        $this->m_missing[$id] = $item;
      }
    }

    if(count($this->m_missing) > 0)
    {
      return false;
    }

    return true;
  }

  public function addTextInput($id, $desc, $value, $required)
  {
    $this->m_form[$id] = array('desc' => $desc,
                               'value' => self::dataInput($id, $value),
                               'type' => 'text',
                               'required' => $required);
  }

  public function addCheckBoxInput($id, $desc, $value, $checkedvalue, $required)
  {
    $this->m_form[$id] = array('desc' => $desc,
                               'value' => self::dataInput($id, ''),
                               'checkvalue' => $value,
                               'checkedvalue' => $checkedvalue,
                               'type' => 'checkbox',
                               'required' => $required);
  }

  public function addPasswordInput($id, $desc, $value, $required)
  {
    $this->m_form[$id] = array('desc' => $desc,
                               'value' => self::dataInput($id, $value),
                               'type' => 'password',
                               'required' => $required);
  }

  public function addTextareaInput($id, $desc, $value, $required)
  {
    $this->m_form[$id] = array('desc' => $desc,
                               'value' => self::dataInput($id, $value),
                               'type' => 'textarea',
                               'required' => $required);
  }

  public function addRadioInput($id, $desc, $items, $checkedvalue, $required)
  {
    $this->m_form[$id] = array('desc' => $desc,
                               'value' => self::dataInput($id, $checkedvalue),
                               'type' => 'radio',
                               'required' => $required);

    foreach($items as $item)
    {
      $this->m_form[$id]['items'][] = array('desc' => $item['desc'],
                                            'value' => $item['value']);
    }
  }

  public function addSelect($id, $desc, $options, $selectedvalue, $required)
  {
    $this->m_form[$id] = array('desc' => $desc,
                               'value' => self::dataInput($id, $selectedvalue),
                               'type' => 'select',
                               'required' => $required);

    foreach($options as $option)
    {
      $this->m_form[$id]['options'][] = array('desc' => $option['desc'],
                                            'value' => $option['value']);
    }
  }

  public function addSubmit($id, $value)
  {
    $this->m_form[$id] = array('value' => $value,
                               'type' => 'submit',
                               'clicked' => self::dataInput($this->m_action_id.'_'.$id, false) ? 1 : 0,
                               'required' => false);
  }

  public function isReady()
  {
    foreach($this->m_form as $id => $item)
    {
      if($item['type'] == 'submit')
      {
        if(isset($_REQUEST[$this->m_action_id.'_'.$id]))
        {
          return $this->checkRequiredFields();
        }
      }
    }

    return false;
  }

  public function getMissingFields()
  {
    return $this->m_missing;
  }

  public function getValue($id)
  {
    return $this->m_form[$id]['value'];
  }

  public function getFormHTML()
  {
    $str = '';

    $str = '<form action="'.$_SERVER['REQUEST_URI'].'" method="'.$this->m_method.'">'."\n";

    foreach($this->m_form as $id => $item)
    {
      $str .= $this->getItemHTML($id, $item);
    }

    $str .= '</form>'."\n";

    return $str;
  }

  public function getDebugForm()
  {
    return Tools::debugArray($this->m_form) . Tools::debugArray($_REQUEST);
  }

  public function getFields()
  {
    return $this->m_form;
  }
}

?>
