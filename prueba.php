<?php

/*include 'simplehtmldom_1_5/simple_html_dom.php';
$html = str_get_html(file_get_contents('http://www.frasescelebres.com/frases-de-arte'));
$dom = $html->find('a');
var_dump($dom);*/
include 'HtmlDomParser.php';
use Sunra\PhpSimple\HtmlDomParser;

$_URL_= 'http://www.frasescelebres.com';
$_EMOTIONS_ = array('frase-del-dia','frases-de-musica','frases-de-gobierno','frases-de-cobardia');
$dom = HtmlDomParser::file_get_html($_URL_.'/'.$_EMOTIONS_[0]);
//$dom = HtmlDomParser::file_get_html('http://www.frasescelebres.com/frases-de-arte');
$_cita_ = array();
$pivote = 0;
$i=0;
foreach($dom->find("div[class=frase_autor]") as $divs) {
      $img = $divs->childNodes($pivote)->firstChild();
      if (empty($img)):
            $pivote = 0;           
            $_cita_[$i]['avatar'] = $_URL_.'/default.png';
            else:
            $pivote = 0;
             $_cita_[$i]['avatar'] = $_URL_.$img->getElementByTagName('img')->src;            
      endif;
      $_cita_[$i]['phrase'] = trim($divs->childNodes($pivote)->plaintext);
      $_extract_ =  trim($divs->childNodes($pivote + 1 )->plaintext);
      $_cita_[$i]['author'] = str_replace(')',',',preg_split("/[\s][(]/", $_extract_));
      $i++;
}
foreach($_cita_ as $pensamiento):
      $widget = "<blockquote>
      <img src='{$pensamiento['avatar']}'>
      <h2>{$pensamiento['phrase']}</h2><h3>{$pensamiento['author'][0]} - <span>{$pensamiento['author'][1]}</span></h3>
      </blockquote>";
      echo $widget;
endforeach;
/*foreach($dom->find("div[class=frase_autor]") as $divs) {
var_dump($divs->childNodes(1)->firstChild()->plaintext);
}
 /*       
 
 foreach($dom->find("div[class=fb-like]") as $a) {
var_dump($a->attr['addthis:title']);
}
   array(7) {
         ["class"]=>
     string(7) "fb-like"
["data-href"]=>

         string(40) "http://www.frasescelebres.com/frase-3552"
["addthis:title"]=>
      string(114) "Para escribir en prosa es absolutamente indispensable tener algo que decir. Para esc
ribir en verso, no es preciso."*/