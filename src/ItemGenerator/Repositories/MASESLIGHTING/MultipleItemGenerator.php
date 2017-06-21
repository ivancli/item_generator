<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 21/06/2017
 * Time: 4:50 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\MASESLIGHTING;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    const DATA_PRODUCT_VARIATIONS = '//*[@data-product_variations]/@data-product_variations';


    private function __getProductInfo()
    {
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::DATA_PRODUCT_VARIATIONS);
            $productInfo = null;
            foreach ($xpathNodes as $xpathNode) {
                if ($xpathNode->nodeValue) {
                    $productInfo = $xpathNode->nodeValue;
                } else {
                    $productInfo = $xpathNode->textContent;
                }
            }
            if (!is_null($productInfo)) {
                $this->productInfo = json_decode($productInfo);
            }
        }
        return false;
    }

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
        if (!is_null($this->productInfo) && count($this->productInfo) > 1) {
            return true;
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
            $items = [];
            foreach ($this->productInfo as $product) {
                $attributes = collect($product->attributes);
                $labelText = "";
                $attributes->each(function ($attribute, $label) use (&$labelText) {
                    $label = str_replace('attribute_', '', $label);
                    $labelText .= "{$label}: {$attribute}; ";
                });

                $item = [];
                $item['Variant'] = new \stdClass();
                $item['Variant']->text = $labelText;
                $item['Variant']->value = $product->variation_id;
                $items[] = $item;
            }
            $this->options = $items;
            return true;
        }
        return false;
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
}