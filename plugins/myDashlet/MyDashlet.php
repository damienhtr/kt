<?

require_once(KT_LIB_DIR . '/dashboard/dashlet.inc.php');

class MyDashlet extends KTBaseDashlet 
{
       function MyDashlet ()
       {
               // set the title
               $this->sTitle = _kt('My Dashlet Title');
      }

	  function is_active($oUser) 
      {
      		  $this->oUser=$oUser;
      			
              // some boolean expression to decide if the dashlet should be displayed to the current user.
              // The root user has an id of 1.
              return ($oUser->getId() == 1) || ($oUser->getUsername() == 'myname');        
      }
      
      function render() 
      {
              // we must now render our dashlet.
              $oTemplating =& KTTemplating::getSingleton();
              $oTemplate = $oTemplating->loadTemplate('MyDashlet');
              $aTemplateData = array
              (
                      'is_root' => ($this->oUser->getId() == 1),
                      'user' => $this->oUser->getUsername()
              );

              return $oTemplate->render($aTemplateData);                
      }
}