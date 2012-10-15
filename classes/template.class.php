<?php
//------------------------------------------------------------------------------
/// Filename:    template.class.php
/// Description: This class provides a basic html template to be filled
/// Authors:     Tommy Sparber
///
/// Created:     15.10.2012
//------------------------------------------------------------------------------

if(!class_exists('Template'))
{
  //----------------------------------------------------------------------------
  class Template
  {
    //--------------------------------------------------------------------------
    private $m_title = '';

    //--------------------------------------------------------------------------
    private $m_content = '';

    //--------------------------------------------------------------------------
    private $m_msg = array();

    //--------------------------------------------------------------------------
    public function setTitle($title)
    {
      $this->m_title = $title;
    }

    //--------------------------------------------------------------------------
    public function addContent($content)
    {
      $this->m_content .= $content;
    }

    //--------------------------------------------------------------------------
    private function getMsgItems()
    {
      if(count($this->m_msg) == 0)
      {
        return '';
      }

      $str = '<div class="messages">'."\n";

      foreach($this->m_msg as $item)
      {
        $str .= '<div class="msg msg'.$item['type'].'">'.htmlspecialchars($item['desc']).'</div>'."\n";
      }

      $str .= "</div>\n";

      return $str;
    }

    //--------------------------------------------------------------------------
    public function addMsg($type, $desc)
    {
      $this->m_msg[] = array('type' => $type, 'desc' => $desc);
    }

    //--------------------------------------------------------------------------
    private function header()
    {
      return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="content-language" content="de-DE" />
    <meta name="author" content="Tommy Sparber" />
    <meta name="publisher" content="Tommy Sparber" />
    <meta name="copyright" content="by Tommy Sparber 2012" />
    <link type="text/css" rel="stylesheet" href="style.css" />
    <!--<script type="text/javascript" src="script.js"></script>-->
    <title>'.$this->m_title.'</title>
  </head>

  <body>';
    }

    //--------------------------------------------------------------------------
    private function body()
    {
      return '
  <div id="content">
'.$this->m_content.'
  </div>';
    }

    //--------------------------------------------------------------------------
    private function footer()
    {
      return '
  </body>
</html>';
    }

    //--------------------------------------------------------------------------
    public function out()
    {
      return $this->header().$this->getMsgItems().$this->body().$this->footer();
    }
  }
}

?>