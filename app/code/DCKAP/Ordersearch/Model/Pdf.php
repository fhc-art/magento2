<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class Pdf extends \Magento\Framework\Model\AbstractModel
{

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $rootDirectory;

    /**
     * Zend PDF object
     *
     * @var \Zend_Pdf
     */
    protected $pdf;

    /**
     * Core string
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var Zend_Pdf_Color_Rgb
     */
    protected $rgb1;

    /**
     * @var Zend_Pdf_Color_Rgb
     */
    protected $rgb2;

    /**
     * @var Zend_Pdf_Color_GrayScale
     */
    protected $grayscale1;

    /**
     * @var Zend_Pdf_Color_GrayScale
     */
    protected $grayscale2;

    /**
     * @var Zend_Pdf
     */
    protected $zend_pdf;

    /**
     * @var Zend_Pdf_Style
     */
    protected $zend_pdf_style;

    /**
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $dateTime
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Zend_Pdf_Color_Rgb $rgb1
     * @param \Zend_Pdf_Color_Rgb $rgb2
     * @param \Zend_Pdf_Color_GrayScale $grayscale1
     * @param \Zend_Pdf_Color_GrayScale $grayscale2
     * @param \Zend_Pdf $zend_pdf
     * @param \Zend_Pdf_Style $zend_pdf_style
     */

    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Stdlib\DateTime\Timezone $dateTime,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Zend_Pdf_Color_Rgb $rgb1,
        \Zend_Pdf_Color_Rgb $rgb2,
        \Zend_Pdf_Color_GrayScale $grayscale1,
        \Zend_Pdf_Color_GrayScale $grayscale2,
        \Zend_Pdf $zend_pdf,
        \Zend_Pdf_Style $zend_pdf_style
    )
    {
        $this->string = $string;
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->inlineTranslation = $inlineTranslation;
        $this->dateTime = $dateTime;
        $this->priceHelper = $priceHelper;
        $this->rgb1 = $rgb1;
        $this->rgb2 = $rgb2;
        $this->grayscale1 = $grayscale1;
        $this->grayscale2 = $grayscale2;
        $this->zend_pdf = $zend_pdf;
        $this->zend_pdf_style = $zend_pdf_style;
    }

    /**
     * Before getPdf processing
     *
     * @return void
     */
    protected function _beforeGetPdf()
    {
        $this->inlineTranslation->suspend();
    }

    /**
     * After getPdf processing
     *
     * @return void
     */
    protected function _afterGetPdf()
    {
        $this->inlineTranslation->resume();
    }

    /**
     * Set font as regular
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Re-4.4.1.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_It-2.8.2.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }


    /**
     * Set PDF object
     *
     * @param  \Zend_Pdf $pdf
     * @return $this
     */
    protected function _setPdf(\Zend_Pdf $pdf)
    {
        $this->pdf = $pdf;
        return $this;
    }

    /**
     * @param  string $string
     * @param  \Zend_Pdf_Resource_Font $font
     * @param  float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ? iconv(
            'UTF-8',
            'UTF-16BE//IGNORE',
            $string
        ) : iconv(
            'UTF-8',
            'UTF-16BE',
            $string
        );

        $characters = [];
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = ord($drawingString[$i++]) << 8 | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = array_sum($widths) / $font->getUnitsPerEm() * $fontSize;
        return $stringWidth;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  \Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @param  int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, \Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the center
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  \Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @return int
     */
    public function getAlignCenter($string, $x, $columnWidth, \Zend_Pdf_Resource_Font $font, $fontSize)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + round(($columnWidth - $width) / 2);
    }

    /**
     *
     * @param  \Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf_Page
     */
    public function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
        foreach ($draw as $itemsProp) {
            $this->checkLinesdataExist($itemsProp);

            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $itemsProp['shift'] = $this->setShiftifEmpty($lines, $height);
            }


            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = \Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        $font = $this->setFontStyle($fontStyle, $fontSize, $page);
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = [$column['text']];
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        $feed = $this->setFeedbasedonTextalign($part, $feed, $width, $font, $fontSize, $textAlign);
                        $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }

    /**
     * @param $itemsProp
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkLinesdataExist($itemsProp)
    {
        if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We don\'t recognize the draw line data. Please define the "lines" array.')
            );
        }
    }

    /**
     * @param $fontStyle
     * @param $fontSize
     * @param $page
     * @return \Zend_Pdf_Resource_Font
     */
    protected function setFontStyle($fontStyle, $fontSize, $page)
    {
        if ($fontStyle == 'bold') {
            $font = $this->_setFontBold($page, $fontSize);
        } elseif ($fontStyle == 'italic') {
            $font = $this->_setFontItalic($page, $fontSize);
        } else {
            $font = $this->_setFontRegular($page, $fontSize);
        }
        return $font;
    }

    /**
     * @param $lines
     * @param $height
     * @return int
     */
    protected function setShiftifEmpty($lines, $height)
    {
        $shift = 0;
        foreach ($lines as $line) {
            $maxHeight = 0;
            foreach ($line as $column) {
                $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                if (!is_array($column['text'])) {
                    $column['text'] = [$column['text']];
                }
                $top = 0;
                foreach ($column['text'] as $part) {
                    $top += $lineSpacing;
                }

                $maxHeight = $top > $maxHeight ? $top : $maxHeight;
            }
            $shift += $maxHeight;
        }
        return $shift;
    }

    /**
     * @param $part
     * @param $feed
     * @param $width
     * @param $font
     * @param $fontSize
     * @param $textAlign
     * @return int
     */
    protected function setFeedbasedonTextalign($part, $feed, $width, $font, $fontSize, $textAlign)
    {
        if ($textAlign == 'right') {
            if ($width) {
                $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
            } else {
                $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
            }
        } elseif ($textAlign == 'center') {
            if ($width) {
                $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
            }
        }
        return $feed;
    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->y = $this->y ? $this->y : 815;
        $this->_setFontRegular($page, 10);

        $page->setFillColor($this->rgb1);
        $page->setLineColor($this->grayscale1);
//        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor($this->rgb2);

        //columns headers
        $lines[0][] = ['text' => __('Order #'), 'feed' => 35];

        $lines[0][] = ['text' => __('Date'), 'feed' => 150, 'align' => 'center'];

        $lines[0][] = ['text' => __('Ship To'), 'feed' => 270, 'align' => 'center'];

        $lines[0][] = ['text' => __('Order Total'), 'feed' => 410, 'align' => 'center'];

        $lines[0][] = ['text' => __('Status'), 'feed' => 515, 'align' => 'center'];


        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor($this->grayscale2);
//        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Draw item line
     *
     * @return void
     */
    public function _drawItem($item, $page)
    {
        $lines = [];

        // draw Order Id
        $lines[0] = [['text' => $this->string->split($item->getIncrementId(), 35, true, true), 'feed' => 35]];

        // draw Date
        $createdDate = $item->getCreatedAt();
        $createdDate = $this->dateTime->formatDate($item->getCreatedAt());
        $lines[0][] = [
            'text' => $this->string->split($createdDate, 25),
            'feed' => 150,
            'align' => 'left',
        ];

        // draw Ship To
        // $lines[0][] = ['text' => $this->string->split($item->getShippingAddress()->getName(),100), 'feed' => 230, 'align' => 'left'];
        if (!empty($item->getShippingAddress())) {
            $lines[0][] = ['text' => $this->string->split($item->getShippingAddress()->getName(), 100), 'feed' => 230, 'align' => 'left'];
        } else {
            $lines[0][] = ['text' => '', 'feed' => 230, 'align' => 'left'];
        }

        // draw Order Total
        $lines[0][] = ['text' => $this->priceHelper->currency($item->getGrandTotal(), true, false), 'feed' => 460, 'align' => 'right'];

        // draw Status
        $lines[0][] = [
            'text' => $item->getStatusLabel(),
            'feed' => 550,
            'font' => 'bold',
            'align' => 'right',
        ];

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($orders = [])
    {
        $this->_beforeGetPdf();

//        $pdf = new \Zend_Pdf();
        $pdf = $this->zend_pdf;
        $this->_setPdf($pdf);

//        $style = new \Zend_Pdf_Style();
        $style = $this->zend_pdf_style;
        $this->_setFontBold($style, 10);

        $page = $this->newPage();

        $this->_drawHeader($page);
        foreach ($orders as $order) {
            $this->_drawItem($order, $page);
            $page = end($pdf->pages);
        }

        $this->_afterGetPdf();
        return $pdf;

    }

    /**
     * Retrieve PDF object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->pdf instanceof \Zend_Pdf) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please define the PDF object before using.'));
        }

        return $this->pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {

        $pageSize = !empty($settings['page_size']) ? $settings['page_size'] : \Zend_Pdf_Page::SIZE_A4;

        $page = $this->_getPdf()->newPage($pageSize);

        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        return $page;
    }
}