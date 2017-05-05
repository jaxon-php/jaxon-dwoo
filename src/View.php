<?php

namespace Jaxon\Dwoo;

use Jaxon\Sentry\Interfaces\View as ViewInterface;
use Jaxon\Sentry\View\Store;

class View implements ViewInterface
{
    /**
     * The Dwoo template renderer
     *
     * @var Dwoo\Core
     */
    protected $xRenderer = null;

    /**
     * The template directories
     *
     * @var array
     */
    protected $aDirectories = array();

    /**
     * The view constructor
     * 
     * @return
     */
    public function __construct()
    {
        $this->xRenderer = new \Dwoo\Core();
        $this->xRenderer->setCacheDir(__DIR__ . '../cache');
    }

    /**
     * Add a namespace to this view renderer
     *
     * @param string        $sNamespace         The namespace name
     * @param string        $sDirectory         The namespace directory
     * @param string        $sExtension         The extension to append to template names
     *
     * @return void
     */
    public function addNamespace($sNamespace, $sDirectory, $sExtension = '')
    {
        $this->aDirectories[$sNamespace] = array('path' => $sDirectory, 'ext' => $sExtension);
    }

    /**
     * Render a view
     * 
     * @param Store         $store        A store populated with the view data
     * 
     * @return string        The string representation of the view
     */
    public function render(Store $store)
    {
        $sViewName = $store->getViewName();
        $sNamespace = $store->getNamespace();
        // For this view renderer, the view name doesn't need to be prepended with the namespace.
        $nNsLen = strlen($sNamespace) + 2;
        if(substr($sViewName, 0, $nNsLen) == $sNamespace . '::')
        {
            $sViewName = substr($sViewName, $nNsLen);
        }
        // View data
        $data = new \Dwoo\Data();
        foreach($store->getViewData() as $sName => $xValue)
        {
            $data->assign($sName, $xValue);
        }
        // View extension
        $sDirectory = '';
        $sExtension = '';
        if(key_exists($sNamespace, $this->aDirectories))
        {
            $sDirectory = $this->aDirectories[$sNamespace]['path'];
            $sExtension = $this->aDirectories[$sNamespace]['ext'];
        }
        // Render the template
        $template = new \Dwoo\Template\File($sDirectory . '/' . $sViewName . $sExtension);
        return trim($this->xRenderer->get($template, $data), " \t\n");
    }
}
