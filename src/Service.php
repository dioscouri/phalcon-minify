<?php
namespace Dsc\Minify;

class Service implements \Phalcon\DI\InjectionAwareInterface
{

    protected $_di;

    public function setDi($di)
    {
        $this->_di = $di;
    }

    public function getDi()
    {
        return $this->_di;
    }
	
	/**
	 *  Registers paths to minify resources
	 */
	public function registerPaths($paths, $type, $options = array() ){
		if( !is_array( $options ) ) {
			$options = array( $options );
		}
		
		if( !isset($options['priority'] ) ) {
			$options['priority'] = 1;
		}
		if( !isset($options['collection'] ) ) {
			$options['collection'] = '_all';
		}
		
		$current_paths = $this->_di['session']->get( 'minify.paths' );
		$type = strtolower($type);
		if( !isset( $current_paths[$type] ) ) {
			$current_paths[$type] = array( '_all' => array() );
		}

		if( !isset( $current_paths[$type][$options['collection']] ) ) {
			$current_paths[$type][$options['collection']] = array();
		}
		
		if( !is_array($paths)) {
			$paths = array($paths);
		}
		$cleared_paths = array();
		foreach( $paths as $p ){
			if( file_exists(PATH_ROOT.$p)){ // check if the directory exists			
				if( array_search($p, $current_paths[$type][$options['collection']]) === false ){ // keep the paths unique
					$cleared_paths []= $p;
				}
			}
		}

		$current_paths[$type][$options['collection']] = array_merge($current_paths[$type][$options['collection']], $cleared_paths );
		$this->_di['session']->set( 'minify.paths', $current_paths );
		print_r( $current_paths );
	}
	
	/**
	 *  Unregisters paths to minify resources
	 */
	public function unregisterPaths($paths, $type, $options = array() ){
		if( !is_array( $options ) ) {
			$options = array( $options );
		}

		if( !isset($options['collection'] ) ) {
			$options['collection'] = '_all';
		}
		
		$current_paths = $this->_di['session']->get( 'minify.paths' );
		$type = strtolower($type);
		if( !isset( $current_paths[$type] ) || !isset( $current_paths[$type][$options['collection']] )) {
			$current_paths[$type] = array();
			$current_paths[$type][$options['collection']] = array();
			return;
		}
		
		if( !is_array($paths)) {
			$paths = array($paths);
		}
		$cleared_paths = array();
		foreach( $paths as $p ){
			if( ($key = array_search($p, $current_paths[$type][$options['collection']]) ) !== false ){
				unset( $current_paths[$type][$options['collection']][$key]);
			}
		}
		$this->_di['session']->set( 'minify.paths', $current_paths );
		print_r( $current_paths );
	}
	
}