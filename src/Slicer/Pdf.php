<?php

namespace Slicer;

/**
 * PDF container
 * 
 * $numPages = count($pdf);
 * foreach ($pdf as $index => $page) {
 *     $page->export("image_{$index}.png");
 * }
 */
class Pdf implements \ArrayAccess, \Countable, \Iterator
{

    /** @var string Path to the PDF file */
    protected $path;

    /** @var int Number of pages in the PDF */
    protected $size;

    /** @var \Slicer\Slicer Configuration */
    protected $config;

    /** @var int position for iterator */
    protected $position;

    /**
     * Instantiate the PDF container
     * 
     * @param \Slicer\Slicer $config The configuration
     * @param string         $path   The path to the PDF file. This is escaped
     *                               for the command line.
     * @throws \RuntimeException Thrown if pdfinfo cannot parse the pdf
     */
    public function __construct(Slicer $config, $path) {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File $path does not exist");
        }

        $this->config = $config;
        $this->path = $path;
        $this->size = $this->count();
        $this->position = 0;
    }

    /**
     * Return the number of pages in the PDF
     * 
     * @return int
     * @throws \RuntimeException
     */
    public function count() {
        if (isset($this->size)) {
            return $this->size;
        }

        $pdfPath = escapeshellarg($this->path);
        exec("{$this->config->pdfinfoPath} $pdfPath 2>&1", $output, $result);
        $text = implode("\n", $output);
        if ($result !== 0) {
            // pdfinfo does not exist or not a pdf
            throw new \RuntimeException("pdfinfo error: $text");
        }
        if (preg_match("/Pages:\s+(\d+)/", $text, $matches)) {
            return (int)$matches[1];
        }

        return 0;
    }

    /**
     * Export a page from a pdf to an image
     * 
     * @param int    $index The page index (zero based)
     * @param string $path  The path and filename of the image to be created
     * @return void
     */
    public function export($index, $path) {
        $pdfPath = escapeshellarg($this->path);
        exec("{$this->config->convertPath} {$pdfPath}[$index] $path 2>&1", $output, $result);
        if ($result !== 0) {
            // not a pdf?
            $text = implode("\n", $output);
            throw new \RuntimeException("pdfinfo error: $text");
        }
    }

    /**
     * Does this page exist
     * 
     * @param int $offset Page offset (zero-based index)
     * @return bool
     */
    public function offsetExists($offset) {
        return $offset >= 0 && $offset < $this->size;
    }

    /**
     * Get the page at a particular index
     * 
     * @param int $offset Page offset (zero-based index)
     * @return \Slicer\Page
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset) {
       if (!$this->offsetExists($offset)) {
           throw new \InvalidArgumentException("The page $offset does not exist");
       }

       return new Page($this, $offset);
    }

    /**
     * Not implemented
     * 
     * @param int  $offset Page offset (zero-based index)
     * @param mixed $value Value
     * @throws \Exception
     */
    public function offsetSet($offset, $value) {
        throw new \Exception("Slicer does not support modifying the PDF");
    }

    /**
     * Not implemented
     * 
     * @param int $offset Page offset (zero-based index)
     * @throws \Exception
     */
    public function offsetUnset($offset) {
        throw new \Exception("Slicer does not support modifying the PDF");
    }

    /**
     * Return the current page
     * 
     * @return \Slicer\Page
     */
    public function current() {
        return $this->offsetGet($this->position);
    }

    /**
     * Return the index of the current page
     * 
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     * Move the iterator to the next element
     * 
     * @return void
     */
    public function next() {
        $this->position++;
    }

    /**
     * Rewind the iterator to the first element
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Checks if the current position of the iterator is valid
     * 
     * @return bool
     */
    public function valid() {
        return $this->offsetExists($this->position);
    }
}
