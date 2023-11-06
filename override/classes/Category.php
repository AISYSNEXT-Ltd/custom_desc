<?php
 
 
class Category extends CategoryCore
{
     
    public $shortdesc;
 
    public function __construct($id_category = null, $id_lang = null, $id_shop = null){
         
        self::$definition['fields']['shortdesc'] = array('type' => self::TYPE_HTML, 'lang' => true ,'validate' => 'isCleanHtml');
        parent::__construct($id_category, $id_lang, $id_shop);
    }
         
     
}