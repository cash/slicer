<?php

namespace Slicer;

/**
 * PDF Page
 * 
 * $page->export("image.jpg");
 */
class Page
{
    protected $pdf;
    protected $index;

    /**
     * Constructor
     * 
     * @param \Slicer\Pdf $pdf   PDF object
     * @param int         $index Zero-based index of this page in the PDF 
     */
    public function __construct(Pdf $pdf, $index) {
        $this->pdf = $pdf;
        $this->index = $index;
    }

    public function export($path) {
        $this->pdf->export($this->index, $path);
    }
}
