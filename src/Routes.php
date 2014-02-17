<?php

namespace Dsc\Minify;

class Routes extends \Phalcon\Mvc\Router\Group
{
    public function initialize()
    {
        //Default paths
        $this->setPaths(array(
            'module' => 'minify',
            'namespace' => 'Dsc\Minify\Controllers\\'
        ));

        //All the routes start with /minify
        $this->setPrefix('/minify');

        $this->add('{aux:/?}', array(
        	'controller' => 'Minify',
            'action' => 'default'
        ));

        $this->add('/{id:[0-9a-zA-Z\-]+/?}', array(
        	'controller' => 'Minify',
            'action' => 'item'
        ));
		
        $this->add('/js', array(
        	'controller' => 'Minify',
            'action' => 'js'
        ));
        $this->add('/css', array(
        	'controller' => 'Minify',
            'action' => 'css'
        ));		
	}
}