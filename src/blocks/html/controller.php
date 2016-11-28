<?php
namespace Application\Block\Html;

use Loader;
use Page;
use Config;
use \Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btTable = 'btContentLocal';
    protected $btInterfaceWidth = "600";
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceHeight = "500";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    public $content = "";

    public function getBlockTypeDescription()
    {
        return t("For adding HTML by hand.");
    }

    public function getBlockTypeName()
    {
        return t("HTML");
    }

    public function view()
    {
        $content = $this->content;
        if(strpos($content,"{")){
            $this->parseContent();
        }
        $this->set('content', $this->content);
    }
    
    public function parseContent(){
        $newContent =  preg_replace_callback( "/{([^:}]*):?([^}]*)}/", array( &$this, "parseVar" ), $this->content );    
        $this->content = $newContent;
    }

    public function add()
    {
        $this->edit();
    }

    public function edit()
    {
        $this->requireAsset('ace');
    }

    public function getSearchableContent()
    {
        return $this->content;
    }

    public function save($data)
    {
        $args['content'] = isset($data['content']) ? $data['content'] : '';
        parent::save($args);
    }

    public static function xml_highlight($s)
    {
        $s = htmlspecialchars($s);
        $s = preg_replace(
            "#&lt;([/]*?)(.*)([\s]*?)&gt;#sU",
            "<font color=\"#0000FF\">&lt;\\1\\2\\3&gt;</font>",
            $s
        );
        $s = preg_replace(
            "#&lt;([\?])(.*)([\?])&gt;#sU",
            "<font color=\"#800000\">&lt;\\1\\2\\3&gt;</font>",
            $s
        );
        $s = preg_replace(
            "#&lt;([^\s\?/=])(.*)([\[\s/]|&gt;)#iU",
            "&lt;<font color=\"#808000\">\\1\\2</font>\\3",
            $s
        );
        $s = preg_replace(
            "#&lt;([/])([^\s]*?)([\s\]]*?)&gt;#iU",
            "&lt;\\1<font color=\"#808000\">\\2</font>\\3&gt;",
            $s
        );
        $s = preg_replace(
            "#([^\s]*?)\=(&quot;|')(.*)(&quot;|')#isU",
            "<font color=\"#800080\">\\1</font>=<font color=\"#FF00FF\">\\2\\3\\4</font>",
            $s
        );
        $s = preg_replace(
            "#&lt;(.*)(\[)(.*)(\])&gt;#isU",
            "&lt;\\1<font color=\"#800080\">\\2\\3\\4</font>&gt;",
            $s
        );

        return nl2br($s);
    }
    
    public function parseVar( Array $match )
{
    switch($match[1]){
        case "config":
        return Config::get($match[2]);
        break;
        case "training":
        $parts = explode(":",$match[2]);
            return self::training($parts);
        break;
        case "att":
        $c = Page::getCurrentPage();
        return $c->getAttribute($match[2]);
        break;
    }
}

public function training($parts){
    $db = \Database::connection();
    switch($parts[0]){
        case "type": //type searches are formatted column:tablePrefix:typeID
        if(!$parts[2]){
            $c = Page::getCurrentPage();
            $tablePrefix = $c->getAttribute('tablePrefix');
        } else {
            $tablePrefix = $parts[2];
        }
        
        if(!$parts[3]){
            $c = Page::getCurrentPage();
            $typeID = $c->getAttribute('typeID');
        } else {
            $typeID = $parts[3];
        }
        
        
        $table = "C5CBT_{$tablePrefix}_types";
        return $db->fetchColumn("SELECT {$parts[1]} FROM $table WHERE typeID = ?",array($typeID));
        break;
        
    }
    }
}
