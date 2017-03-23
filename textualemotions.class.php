<?php
/**
* Codígo que extrae frases celebres de la  pagina www.frasescelebres.com, convirtiendo la clase
* en una API para poder interactuar con el contenido de dicho website.
* -------------------------------------------------------------------------------------
* Librerias Externas : simple html dom  [ http://simplehtmldom.sourceforge.net/ ]
* Lo cual le agradezco de esta genial liberia para poder realizar dicho proyecto :)
* -------------------------------------------------------------------------------------
* Website Developer  : junglacode.org
* Licencia GPLv3 https://www.gnu.org/licenses/gpl-3.0.html
* @autor  Juan Luis Garcia Corrales
* @alias  monolinux
* @email  monolinux@junglacode.org
* @package  Textual Emotions 
*/
include 'HtmlDomParser.php';
use Sunra\PhpSimple\HtmlDomParser;

class Textual_Emotions{

    /*Url para la extracción de datos*/
    const __WEBSERVICE__ = 'http://www.frasescelebres.com';

    
     /**
     * Función que extrae la frase del dia , si avatar se encuentra en TRUE
     * extraera la imagen si es que la tiene, FALSE omite la extracción
     * @param Boolean $avatar , default true
     * @return Array
     */
    static function getPhraseOfTheDay($avatar = true){
        $dom_ = HtmlDomParser::file_get_html(self::__WEBSERVICE__.'/frase-del-dia');
        foreach ($dom_->find("div[class=frase_autor]") as $_layer_): 
            list($pivote,$imagen) = self::existImageOfThePhrase__($_layer_->firstChild());
            $_cita_['avatar'] = $imagen;
            $_cita_['phrase'] = trim($_layer_->childNodes($pivote + 1)->plaintext);
            $_cita_['author'] =  self::prettyNameAutor__(trim($_layer_->childNodes($pivote + 2)->plaintext));      
            break;
            /*solo nos aseguramos que sea el primer nodo*/
        endforeach;
        return  $avatar ? $_cita_ : array_slice($_cita_,1);
    }
    
    /**
    * Función que extrae las frases a partir de una frase, hace peticiones al modulo 
    * buscar.php. si avatar se encuentra en TRUE extraera la imagen si es que la tiene,
    * FALSE omite la extracción
    * @param String $emotion , palabra a buscar
    * @param Boolean $view , default 10 . ya que es lo que cotiene cada pagina
    * @param Boolean $avatar , default true
    */
    static function getPhrasesOfAnEmotion($emotion,$view=10,$avatar = true){
        $request = "/buscar.php?texto='{$emotion}'&pagina=1";
        $dom_ = HtmlDomParser::file_get_html(self::__WEBSERVICE__.$request);
        //echo 'pagination = '.self::__totalOfEmotionPages__($dom_);
        $i=0;
        foreach ($dom_->find("div[class=frase_autor]") as $_layer_): 
             list($pivote,$imagen) = self::existImageOfThePhrase__($_layer_->firstChild());
             $_cita_[$i]['avatar'] = $imagen;
             $_cita_[$i]['phrase'] = trim($_layer_->childNodes($pivote + 1 )->plaintext);
             $_cita_[$i]['author'] =  self::prettyNameAutor__(trim($_layer_->childNodes($pivote + 2)->plaintext));    
            if($i>=$view-1):
                break;
            else:
                $i++;
            endif;  
        endforeach;
        return  $avatar ? $_cita_ : array_slice($_cita_,1);
    }

    /**
    * Función que te genera un template basico en Html para poder visualizar la información
    * extraida de scrappin, la salida es una table;
    *
    *@param Array $struct , array('avatar'=>img,'pharse'=>string ,autor=>array(name,description))
    *@return String 
    */
    static function templateCardHtml($struct){
        return  "<table>
            <thead>
             <tr>
                <th> <img src='{$struct['avatar']}'> </th>
                <th>
                    <h3 style='text-align: left;'>{$struct['phrase']}</h3> 
                    <h4 style='text-align: right;'>{$struct['author']['name']} 
                    - <span style='color: gray;'>{$struct['author']['description']}
                    </span></h4>
                </th>
                </tr>
            <thead>
        </table>";
    }

    private function existImageOfThePhrase__($layer){        
        if(empty($layer->find('a'))):
            $image = 'default.png';
            $pivote = -1;
        else:
            $image=  self::__WEBSERVICE__.$layer->getElementByTagName('img')->src;
            $pivote = 0;
        endif;
          return array($pivote,$image);  
    }

    private function prettyNameAutor__($div){
          $tokens = str_replace(')',',',preg_split("/[\s][(]/", $div));
          if(count($tokens)==2):
            $autor = array('name'=>trim($tokens[0]),'description'=>trim($tokens[1]));
          else:
            $autor = array('name'=>trim($tokens[0]),'description'=>'Desconocido');
          endif;
          return $autor;
    }

    protected function __totalOfEmotionPages__($dom){
        $div = $dom->getElementById("npaginas")->plaintext;
        $tokens = preg_split("/[\s]+[0-9]/", $div);
        /*no se cuenta la pagina de siguiente*/
        return (int) count($tokens) - 1;
    }

}
//echo Textual_Emotions::totalOfEmotionPages('amor');
//echo Textual_Emotions::templateCardHtml(Textual_Emotions::getPhraseOfTheDay());
$frases = Textual_Emotions::getPhrasesOfAnEmotion('derrota');
foreach($frases as $cita):
    echo Textual_Emotions::templateCardHtml($cita);
endforeach;


