<?php
namespace Application\Theme\bootstrap; //this must match your theme directory name
use Concrete\Core\Area\Layout\Preset\Provider\ThemeProviderInterface;
class PageTheme extends \Concrete\Core\Page\Theme\Theme implements ThemeProviderInterface
{
    public function registerAssets()
    {
        $this->providesAsset('javascript', 'bootstrap/*'); //we are loading bootstrap js, so the CMS should not load its own.
        $this->providesAsset('css', 'bootstrap/*'); //we are loading bootstrap css, so the CMS should not load its own.
       
        $this->requireAsset('css', 'font-awesome'); //we would like the CMS to provide the theme with the font awesome css
        $this->requireAsset('javascript', 'jquery'); //we would like the CMS to provide the theme with the jquery
        $this->requireAsset('javascript', 'picturefill'); //we would like the CMS to provide the theme with the picturefill
        $this->requireAsset('javascript-conditional', 'html5-shiv');
        $this->requireAsset('javascript-conditional', 'respond');
        
        $this->requireAsset('bootstrap3');// requires bootstrap js and css as defined in /application/bootstrap/app.php
        $this->requireAsset('defaults'); // requires application level js and css as defined in /application/bootstrap/app.php
    }

    protected $pThemeGridFrameworkHandle = 'bootstrap3'; //we identify the framework so the CMS can use baked in responsive features

    public function getThemeName()
    {
        return t('Bootstrap');
    }

    public function getThemeDescription()
    {
        return t('Bootstrap Based.');
    }

    public function getThemeBlockClasses()
    { // Not required. Sets the classes available to the custom design toolbar on a block basis
        return array(
            'image' => array(
                'img-thumbnail'
            ),
            '*' => array(
                'highlighted'
            ) //The astrick matches all blocks
        );
    }

    public function getThemeAreaClasses()
    { //Not Required. Sets the classes available in the custom design toolbar based on area name like "Main" or "Footer"
        return array(
            'Main' => array('main-class'),
            'Footer' => array('footer-class')
        );
    }

    public function getThemeDefaultBlockTemplates()
    { //sets the default custom template when adding blocks of a specific type
        return array(
            'file' => 'with_icon', //will attempt to load template /application/blocks/file/templates/with_icon/view.php when adding a "file" block
            'autonav' => 'sitemap.php' //will attempt to load template /application/blocks/autonav/templates/sitemap.php when adding an "autonav" block
        );
    }

    public function getThemeResponsiveImageMap()
    { //Concrete5 automatically creates thumbnails when an image is added via the file manager or the redactor plugin.
      // The settings below dictate which thumbnail size to render based on minumum viewport width
      // http://documentation.concrete5.org/developers/designing-for-concrete5/supporting-responsive-images-in-your-concrete5-theme
      return array(
            'large' => '900px', //use the large thumbnail on screens 900px or wider
            'medium' => '768px', // use medium on screens 768px and wider
            'small' => '0',
        );
    }

    public function getThemeEditorClasses()
    { // Not Required, but Helpful. Adds classes to the Redactor WYSIWYG editor used in the Content Block
      // title = name shown in redactor
      // menuClass = the class which wraps the title to act as a preview
      // spanClass = the classes you actully want applied by the WYSIWYG
      // forceBlock = a -1 value will add a <span> element with the classes you indicated. a 1 value will add the classes to the block level element selected, such as p, h1, etc.
      // http://documentation.concrete5.org/tutorials/adding-redactor-custom-styles-in-a-theme-content-block
    return array(
            array('title' => t('Primary Background'), 'menuClass' => 'bg-primary', 'spanClass' => 'bg-primary', 'forceBlock' => 1),
            array('title' => t('Success Background'), 'menuClass' => 'bg-success', 'spanClass' => 'bg-success', 'forceBlock' => 1),
            array('title' => t('Info Background'), 'menuClass' => 'bg-info', 'spanClass' => 'bg-info', 'forceBlock' => 1),
            array('title' => t('Warning Background'), 'menuClass' => 'bg-warning', 'spanClass' => 'bg-warning', 'forceBlock' => 1),
            array('title' => t('Danger Background'), 'menuClass' => 'bg-danger', 'spanClass' => 'bg-danger', 'forceBlock' => 1),
            array('title' => t('Default Button'), 'menuClass' => 'btn btn-default', 'spanClass' => 'btn btn-default', 'forceBlock' => '1'),
            array('title' => t('Success Button'), 'menuClass' => 'btn btn-success', 'spanClass' => 'btn btn-success', 'forceBlock' => '1'),
            array('title' => t('Primary Button'), 'menuClass' => 'btn btn-primary', 'spanClass' => 'btn btn-primary', 'forceBlock' => '1')
            );
    }

    public function getThemeAreaLayoutPresets()
    { //sets up layout presets for use within your areas
      //http://documentation.concrete5.org/developers/designing-for-concrete5/adding-complex-custom-layout-presets-in-your-theme
        $presets = array(
            array(
                'handle' => 'left_sidebar',
                'name' => 'Left Sidebar',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<aside class="col-xs-12 col-sm-4"></aside>',
                    '<main class="col-xs-12 col-sm-8"></main>'
                ),
            ),
            array(
                'handle' => 'right_sidebar',
                'name' => 'Right Sidebar',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<main class="col-xs-12 col-sm-8"></main>',
                    '<aside class="col-xs-12 col-sm-4"></aside>'
                ),
            ),
            array(
                'handle' => 'full_width',
                'name' => 'Full Width',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-xs-12"></div>'
                ),
            ),
            array(
                'handle' => 'three_col',
                'name' => '3 Column',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-xs-12 col-sm-4"></div>',
                    '<div class="col-xs-12 col-sm-4"></div>',
                    '<div class="col-xs-12 col-sm-4"></div>'
                ),
            ),
            array(
                'handle' => 'sixty_40',
                'name' => '2 Column 60/40',
                'container' => '<div class="row"></div>',
                'columns' => array(
                    '<div class="col-xs-12 col-sm-8"></div>',
                    '<div class="col-xs-12 col-sm-4"></div>'
                ),
            )
        );
        return $presets;
    }
}
