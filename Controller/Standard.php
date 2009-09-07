<?php
class Kdl_Ketai_Controller_Standard extends Mage_Core_Controller_Varien_Router_Standard
{
    protected $_modules = array();
    protected $_routes = array();
    protected $_dispatchData = array();

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
        if ($request->getModuleName()) {
            $module = $request->getModuleName();
        } else {
            if(!empty($p[0])) {
                $module = $p[0];
            } else {
                $module = $this->getFront()->getDefault('module');
                $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,	'');
            }
        }
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
                    $controller = $p[1];
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
    
    public function collectRoutes($configArea, $useRouterName)
    {
        $routers = array();
        $routersConfigNode = Mage::getConfig()->getNode($configArea.'/routers');
        if($routersConfigNode) {
            $routers = $routersConfigNode->children();
        }
        
        foreach ($routers as $routerName=>$routerConfig) {
            $use = (string)$routerConfig->use;
        
            if ($use == $useRouterName) {
                $modules = array((string)$routerConfig->args->module);
                
                if ($routerConfig->args->modules) {
                    foreach ($routerConfig->args->modules->children() as $customModule) {
                        if ($customModule) {
                            if ($before = $customModule->getAttribute('before')) {
                                $position = array_search($before, $modules);
                                if ($position === false) {
                                    $position = 0;
                                }
                                array_splice($modules, $position, 0, (string)$customModule);
                            } elseif ($after = $customModule->getAttribute('after')) {
                                $position = array_search($after, $modules);
                                if ($position === false) {
                                    $position = count($modules);
                                }
                                array_splice($modules, $position+1, 0, (string)$customModule);
                            } else {
                                $modules[] = (string)$customModule;
                            }
                        }
                    }
                }

                $frontName = (string)$routerConfig->args->frontName;
                $this->addModule($frontName, $modules, $routerName);
            }
        }
    }
    

}