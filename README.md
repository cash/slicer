slicer
======

PHP library for converting a pdf to a set of images inspired by the grim ruby gem.

Example
------
```
require_once __DIR__ . '/slicer/vendor/autoload.php';

use Slicer\Slicer;
use Slicer\Pdf;
use Slicer\Page;

$slicer = new Slicer();
$pdf = $slicer->create("");

echo count($pdf) . "\n";

foreach ($pdf as $index => $page) {
    $page->export("image_{$index}.png");
}
```
