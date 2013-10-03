<?php

namespace Slicer;

/**
 * Slicer configuration and factory class
 * 
 * $slicer = new Slicer();
 * $pdf = $slicer->create("document.pdf");
 */
class Slicer
{

    /** @var string ImageMagick convert executable path */
    protected $convertPath = "/usr/bin/convert";
    /** @var string pdfinfo executable path */
    protected $pdfinfoPath = "/usr/bin/pdfinfo";

    /**
     * Constructor
     * 
     * @param array $config Array of configuration parameter key/values.
     *                      Supported keys: 'convertPath' and 'pdfinfoPath'
     */
    public function __construct(array $config = array()) {
        $options = array('convertPath', 'pdfinfoPath');
        foreach ($options as $option) {
            if (isset($config[$option])) {
                $this->$option = $config[$option];
            }
        }
    }

    /**
     * Create a PDF object
     * 
     * @param string $pdfPath Path for the PDF file
     * @return \Slicer\Pdf
     * @throws \RuntimeException Thrown if pdfinfo cannot parse the pdf
     */
    public function create($pdfPath) {
        return new Pdf($this, $pdfPath);
    }

    /**
     * Get a configuration value
     * 
     * @param string $name Configuration parameter name
     * @return mixed
     */
    public function __get($name) {
        return $this->$name;
    }
}
