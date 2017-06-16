<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 15/06/2017
 * Time: 5:22 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\SEPHORA;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const SKU_SWATCHES_XPATH = '//script[@skuswatches]';

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    /**
     * Set content, HTML most of the time
     * @param $content
     * @return mixed
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * check if content has multiple items
     * @return bool
     */
    public function hasMultipleItems()
    {
        $this->__getProductInfo();
        if (!is_null($this->productInfo)) {
            return count($this->productInfo) > 1;
        }
        return false;
    }

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions()
    {
        if ($this->hasMultipleItems()) {
            if (!is_null($this->productInfo)) {
                $items = [];
                foreach ($this->productInfo as $productInfo) {
                    $variationType = $productInfo->variation_type;
                    if (!is_null($variationType) && !empty($variationType)) {
                        $element = $variationType;
                    } else {
                        $element = "Variation";
                    }
                    $value = $productInfo->variation_value;

                    $item = [];
                    $item[$element] = new \stdClass();
                    $item[$element]->text = $value;
                    $item[$element]->value = $productInfo->sku_number;
                    $items[] = $item;
                }
                $this->options = $items;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * returning structural options
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * returning a list of items need to be generated
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * combine multiple arrays
     * @param array $data
     * @param array $all
     * @param array $group
     * @param null $value
     * @param int $i
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
    {
        $this->items = $this->options;
        return $this->items;
    }

    private function __getProductInfo()
    {
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::SKU_SWATCHES_XPATH);
            $productInfo = null;
            foreach ($xpathNodes as $xpathNode) {
                if ($xpathNode->nodeValue) {
                    $productInfo = $xpathNode->nodeValue;
                } else {
                    $productInfo = $xpathNode->textContent;
                }
                if (!is_null($productInfo) && !empty($productInfo)) {
                    $productInfo = json_decode($productInfo);
                    if (!is_null($productInfo) && json_last_error() === JSON_ERROR_NONE) {
                        $this->productInfo = $productInfo;
                        return true;
                    }
                }
            }
        }
        return false;
    }
}