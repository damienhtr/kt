<?

require_once(KT_LIB_DIR . '/plugins/plugin.inc.php');
require_once(KT_LIB_DIR . '/plugins/pluginregistry.inc.php'); 

class MyPlugin extends KTPlugin
{
        var $sNamespace = 'myplugin.plugin';
        
        function MyPlugin($sFilename = null) 
        {
               $res = parent::KTPlugin($sFilename);
               $this->sFriendlyName = "MyPluginDamien";
               return $res;
        }

        function setup() 
        {
               $this->registerDashlet('MyDashlet', 'mydashlet.dashlet', 'MyDashlet.php');

               require_once(KT_LIB_DIR . "/templating/templating.inc.php");
               $oTemplating =& KTTemplating::getSingleton();
               $oTemplating->addLocation('My Plugin', '/plugins/myDashlet/templates');
       }
}

$oPluginRegistry =& KTPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin('MyPlugin', 'myplugin.plugin', __FILE__);