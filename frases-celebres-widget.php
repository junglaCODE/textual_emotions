<?php

include_once 'HtmlDomParser.php';
use Sunra\PhpSimple\HtmlDomParser;
class Frases_Widgets extends WP_Widget { 

        function __construct() {
        $widget_ops = array('classname' => 'Frases_Widget', 
        'description' => "Este widget genera dinámicamente frases aleatorias, haciendo Scrapping a la pagina http://www.frasescelebres.com/" );
        $this->WP_Widget('mpw_widget', "RandomTextualEmotions", $widget_ops);
        }
 
    function widget($args,$instance){
             echo $before_widget;    
        ?>
        <aside id='mpw_widget' class='widget mpw_widget'>
            <h3 class='widget-title'>Oh My Little Shit!!!</h3>
            <p><?php echo $this->logicaOfWidget() ?></p>
        </aside>
        <?php
        echo $after_widget;
    }

    function logicaOfWidget(){
        $dom = HtmlDomParser::file_get_html('http://www.frasescelebres.com/frase-del-dia');
        $_cita_ = array();
        foreach($dom->find("div[class=frase_autor]") as $divs) {
        $_cita_['phrase'] = trim($divs->childNodes(0)->plaintext);
        $_extract_ =  trim($divs->childNodes(1)->plaintext);
        $_cita_['author'] = str_replace(')',',',preg_split("/[\s][(]/", $_extract_));
        }
        $widget = "<blockquote>
        <h2>{$_cita_['phrase']}</h2><h3>{$_cita_['author'][0]} - <span>{$_cita_['author'][1]}</span></h3>
        </blockquote>";
        return  $widget;
    }
 
    function update($new_instance, $old_instance){
        // Función de guardado de opciones   
    }
 
    function form($instance){
        // Formulario de opciones del Widget, que aparece cuando añadimos el Widget a una Sidebar
    }   
}

