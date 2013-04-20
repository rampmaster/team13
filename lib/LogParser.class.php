<?php
/**
 * LogParser
 * 
 * @author alvaroveliz
 */

class LogParser
{
  private $filename;
  private $data;

  public function __construct($filename)
  {
    $this->filename = $filename;
  }

  public function parse()
  {
    $fp = fopen($this->filename, 'r');
    $iterations = 0;
    $max_iterations = 100;

    while( $line = fgets($fp) ) 
    {
      $line = trim($line);

      if ($iterations > 3) {
        $this->parser = xml_parser_create('UTF-8');

        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, true);
        xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'startElement', 'endElement');
        xml_set_character_data_handler($this->parser, 'characterData');

        $this->parseLine($line, $iterations);

        xml_parser_free($this->parser);
      }  

      $iterations++;

      // if ($iterations == $max_iterations) {
      //   break;
      // }
    }
    fclose($fp);
  }

  public function startElement($parser, $element_name, $element_attrs)
  {
    echo 'START: '.$element_name.'<br />';
    echo '<pre>';
    echo 'START ATTRS: ';
    print_r($element_attrs);
    echo 'END ATTRS: ';
    echo '</pre>';
  }

  public function endElement($parser, $element_name)
  {
    echo 'END: ' .$element_name.'<br />';
  }

  public function characterData($parser, $data)
  {
    echo 'DATA: '.$data.'<br />';
  }

  function parseLine( $line , $iteration = 0 )
  {
    echo htmlentities($line) . '<br />'; 
    xml_parse($this->parser, $line) or die(sprintf('XML ERROR: %s at line %d column %d (Code : %d)',
         xml_error_string(xml_get_error_code($this->parser)),
         xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser), xml_get_error_code($this->parser)));
    echo '<hr />';
  }
}