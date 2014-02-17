<?php

namespace Dsc\Minify;

class Helper
{

	/**
	 * Gets list of files of asset to be minified
	 */
	static public function getListAssets($di, $type, $options = array()){
		if( !is_array( $options ) ) {
			$options = array( $options );
		}
		
		// which collection
		if( !isset($options['collection'] ) ) {
			$options['collection'] = '_all';
		}
		
		// whether only files from the collection should be considered
		if( !isset($options['only_collection'] ) ) {
			$options['only_collection'] = false;
		}

		$paths = \Dsc\Minify\Helper::getListPaths($di, $type, $options) ;
		$files = array();
		
		// find all files
		if( count( $paths ) ){
			foreach($paths as $p ){
//				$aux_files = Dsc\Phalcon\Filesystem\Folder::
			}
		}
		return array_unique( $files );
	}	
	
	/**
	 * Gets list of paths to look for assets
	 */
	static public function getListPaths($di, $type, $options = array()) {
		if( !is_array( $options ) ) {
			$options = array( $options );
		}
		
		// which collection
		if( !isset($options['collection'] ) ) {
			$options['collection'] = '_all';
		}
		
		// whether only files from the collection should be considered
		if( !isset($options['only_collection'] ) ) {
			$options['only_collection'] = false;
		}
		
		$all_paths = $di['session']->get( 'minify.paths' );
		$paths = array();
		if( isset( $all_paths[$type][$options['collection']] ) ){
			$aux_paths = $all_paths[$type][$options['collection']];
			ksort($aux_paths); // sort by priority
			// flatten the array - by priorities
			if( count( $aux_paths ) ) {
				foreach( $aux_paths as $p ) {
					$paths = array_merge($paths, $p );
				}
			}
		}

		if( $options['only_collection'] && isset( $all_paths[$type]['_all'] ) ){
			$aux_paths = array_merge( $paths, $all_paths[$type]['_all'] );
			ksort($aux_paths); // sort by priority
			// flatten the array - by priorities
			if( count( $aux_paths ) ) {
				foreach( $aux_paths as $p ) {
					$paths = array_merge($paths, $p );
				}
			}
		}

		return array_unique( $paths );
	}
}
