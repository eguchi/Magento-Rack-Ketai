<?php

class Kdl_Ketai_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    
    
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        
        $error = error_reporting(E_ALL);
        require_once('Net/UserAgent/Mobile.php');

        $agent = Net_UserAgent_Mobile::singleton(); 
        switch( true )
        {
            case ($agent->isDoCoMo()):
            case ($agent->isVodafone()):
            case ($agent->isEZweb()):
                error_reporting($error);
                $ketai = new Kdl_Ketai_Controller_Router();
                $front->addRouter('standard', $ketai);
                break;
            default:
                error_reporting($error);
                break;
         }
        
     }

     public function match(Zend_Controller_Request_Http $request)
    {
        //checkings before even try to findout that current module
        //should use this router
        if (!$this->_beforeModuleMatch()) {
            return false;
        }

        $this->fetchDefault();

        $front = $this->getFront();

        $p = explode('/', trim($request->getPathInfo(), '/'));

        // get module name
        $module = "ketai";
        
        if (!$module) {
            if (Mage::app()->getStore()->isAdmin()) {
                $module = 'admin';
            } else {
                return false;
            }
        }

        /**
         * Searching router args by module name from route using it as key
         */
        $modules = $this->getModuleByFrontName($module);

        /**
         * If we did not found anything  we searching exact this module
         * name in array values
         */
        if ($modules === false) {
            if ($moduleFrontName = $this->getModuleByName($module, $this->_modules)) {
                $modules = array($module);
                $module = $moduleFrontName;
            } else {
                return false;
            }
        }

        //checkings after we foundout that this router should be used for current module
        if (!$this->_afterModuleMatch()) {
            return false;
        }

        /**
         * Going through modules to find appropriate controller
         */
        $found = false;
        foreach ($modules as $realModule) {
            $request->setRouteName($this->getRouteByFrontName($module));

            // get controller name
            if ($request->getControllerName()) {
                $controller = $request->getControllerName();
            } else {
                if (!empty($p[1])) {
                    $controller = $p[0] . "_" . $p[1];
                } else {
                    $controller = $front->getDefault('controller');
                    $request->setAlias(
                        Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                        ltrim($request->getOriginalPathInfo(), '/')
                    );
                }
            }

            // get action name
            if (empty($action)) {
                if ($request->getActionName()) {
                    $action = $request->getActionName();
                } else {
                    $action = !empty($p[2]) ? $p[2] : $front->getDefault('action');
                }
            }

            //checking if this place should be secure
            $this->_checkShouldBeSecure($request, '/'.$module.'/'.$controller.'/'.$action);

            $controllerClassName = $this->_validateControllerClassName($realModule, $controller);
            if (!$controllerClassName) {
                continue;
            }

            // instantiate controller class
            $controllerInstance = new $controllerClassName($request, $front->getResponse());

            if (!$controllerInstance->hasAction($action)) {
                continue;
            }

            $found = true;
            break;
        }

        /**
         * if we did not found any siutibul
         */
        if (!$found) {
            if ($this->_noRouteShouldBeApplied()) {
                $controller = 'index';
                $action = 'noroute';

                $controllerClassName = $this->_validateControllerClassName($realModule, $controller);
                if (!$controllerClassName) {
                    return false;
                }

                // instantiate controller class
                $controllerInstance = new $controllerClassName($request, $front->getResponse());

                if (!$controllerInstance->hasAction($action)) {
                    return false;
                }
            } else {
                return false;
            }
        }

        // set values only after all the checks are done
        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);
        $request->setControllerModule($realModule);

        // set parameters from pathinfo
        for ($i=3, $l=sizeof($p); $i<$l; $i+=2) {
            $request->setParam($p[$i], isset($p[$i+1]) ? $p[$i+1] : '');
        }

        // dispatch action
        $request->setDispatched(true);
        $controllerInstance->dispatch($action);

        return true;
    }
    
}