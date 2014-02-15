<?php 
namespace Minify;

class Controller extends \Dsc\Controller
{
    public function item()
    {
        $resource = \Base::instance()->get('PARAMS.1');
        switch ($resource)
        {
        	case "js":
        	    return $this->js();
        	    break;
        	case "css":
        	    return $this->css();
        	    break;
        	default:
        	    return $this->findAsset($resource);
        	    break;
        }
    }
    
    public function findAsset($resource=null)
    {
        if (empty($resource)) {
            return;
        }
    
        // Loop through all the registered paths and try to find the requested asset
        // If it is found, send it with \Web::instance()->send($file, null, 0, false);
        $paths = (array) \Base::instance()->get('dsc.minify.paths');

        foreach ($paths as $path)
        {
            $file = realpath( $path . $resource );
            if (file_exists($file)) 
            {
                \Base::instance()->set('file', $file);
                $view = new \Dsc\Template;
                echo $view->renderLayout('Minify\Views::asset.php');
                
                return;
            }
        }
        
        // File not found.
        \Base::instance()->error(500);
        return;
    }
    
    public function js()
    {
        $files = array();
        if ($prioritized_files = (array) \Base::instance()->get('dsc.minify.js')) {
            foreach ($prioritized_files as $priority=>$paths) {
                foreach ((array)$paths as $path) {
                    $files[] = $path;
                }
            }
        }
    
        if (!empty($files))
        {
            if (\Base::instance()->get('DEBUG')) {
                $paths = (array) \Base::instance()->get('dsc.minify.paths');
                \Base::instance()->set('CACHE', false);
                header('Content-Type: '.(\Web::instance()->mime('pretty.js')));
                foreach($files as $file) 
                {
                    foreach ($paths as $path)
                    {
                        try {
                            echo \Base::instance()->read( $path . $file );
                            echo "\n";
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            } else {
                $paths_string = implode(",", (array) \Base::instance()->get('dsc.minify.paths'));
                \Base::instance()->set('CACHE', true);
                echo \Web::instance()->minify($files, null, true, $paths_string);
            }
        }
    }
    
    public function css()
    {
        $files = array();
        if ($prioritized_files = (array) \Base::instance()->get('dsc.minify.css')) {
            foreach ($prioritized_files as $priority=>$paths) {
                foreach ((array)$paths as $path) {
                    $files[] = $path;
                }
            }
        }
    
        if (\Base::instance()->get('DEBUG')) {
            $paths = (array) \Base::instance()->get('dsc.minify.paths');
            $files = array_merge( $files, $this->buildLessCss() );
            \Base::instance()->set('CACHE', false);
            header('Content-Type: '.(\Web::instance()->mime('pretty.css')));
            foreach($files as $file) 
            {
                foreach ($paths as $path) 
                {
                    try {
                        echo \Base::instance()->read( $path . $file );
                        echo "\n";
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        } else {
            $paths_string = implode(",", (array) \Base::instance()->get('dsc.minify.paths'));
            \Base::instance()->set('CACHE', true);
            $files = array_merge( $files, $this->getLessCssDestinations() );
            echo \Web::instance()->minify($files, null, true, $paths_string);
        }
    }
    
    /**
     *
     */
    private function buildLessCss()
    {
        $f3 = \Base::instance();
        $source_files = (array) $f3->get('dsc.minify.lesscss.sources');
        $less_files = array();
    
        if (!empty($source_files))
        {
            $less = new \lessc;
            $n=0;
            foreach ($source_files as $source_file) {
                $source = $source_file[0];
                $destination = !empty($source_file[1]) ? $source_file[1] : $f3->get('TEMP') . basename($source) . ".css";
                try {
                    if ($less->compileFile($source, $destination) !== false) {
                        $less_files[] = $destination;
                    }
                } catch (\Exception $e) {
                    // TODO Do something with the error
                }
    
                $n++;
            }
        }
    
        return $less_files;
    }
    
    private function getLessCssDestinations()
    {
        $f3 = \Base::instance();
    
        if (!$f3->get('dsc.minify.lesscss.destinations')) {
            $f3->set('dsc.minify.lesscss.destinations', $this->buildLessCss(), 3600*24);
        }
    
        return $f3->get('dsc.minify.lesscss.destinations');
    }
}