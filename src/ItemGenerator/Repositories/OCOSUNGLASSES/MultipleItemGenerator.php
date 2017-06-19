<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 16/06/2017
 * Time: 4:55 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\OCOSUNGLASSES;


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
                        $listOfProductInfo = array_first($productInfo);
                        if (is_array($listOfProductInfo)) {
                            $firstProductInfo = array_first($listOfProductInfo);
                            if (isset($firstProductInfo->{'@type'}) && $firstProductInfo->{'@type'} == 'Product') {
                                $this->productInfo = $listOfProductInfo;
                                return true;
                            }
                        }
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
//        if (!is_null($this->productInfo) && count($this->productInfo) > 1) {
//            return true;
//        }
//        return false;
    }

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions()
    {

        $this->__getProductInfo();
        $items = [];
        foreach ($this->productInfo as $product) {
            $item = [];
            $item['Color'] = new \stdClass();
            $item['Color']->text = $product->color;
            $item['Color']->value = $product->mpn;
            $items[] = $item;
        }
        $this->options = $items;
        return true;
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