<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 4/08/2017
 * Time: 9:14 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\ALPINETREK;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    const LD_JSON_XPATH = '//script[@type="application/ld+json"]';


    private function __getProductInfo()
    {
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::LD_JSON_XPATH);
            $productInfo = null;
            foreach ($xpathNodes as $xpathNode) {
                if ($xpathNode->nodeValue) {
                    $productInfo = $xpathNode->nodeValue;
                } else {
                    $productInfo = $xpathNode->textContent;
                }
                if (!is_null($productInfo) && !empty($productInfo)) {
                    $productInfo = json_decode($productInfo);
                    if (!is_null($productInfo) && json_last_error() === JSON_ERROR_NONE && is_array($productInfo)) {
                        $this->productInfo = $productInfo;
                    }
                }
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
        if (!is_null($this->productInfo)) {
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
                $item = [];
                $item['Variant'] = new \stdClass();
                $item['Variant']->text = $product->name;
                $item['Variant']->value = $product->sku;
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