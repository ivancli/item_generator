<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 19/06/2017
 * Time: 11:21 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\REVOLVECLOTHING;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const MULTIPLE_ITEMS_DETECT_XPATH = '//*[@itemtype="http://schema.org/Product"]';

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
    }

    /**
     * collect label and options from content
     * @return mixed
     */
    public function extractOptions()
    {
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::MULTIPLE_ITEMS_DETECT_XPATH);
            $items = [];
            $xpathNodes->each(function (Crawler $xpathNode) use (&$items) {
                $skuNodes = $xpathNode->filterXPath('//*[@itemprop="sku"]/@content');
                if (!is_null($skuNodes)) {
                    if ($skuNodes->count() == 1) {
                        $label = null;
                        foreach ($skuNodes as $skuNode) {
                            if ($skuNode->nodeValue) {
                                $sku = $skuNode->nodeValue;
                            } else {
                                $sku = $skuNode->textContent;
                            }
                        }
                        $item = [];
                        $item["SKU"] = new \stdClass();
                        $item["SKU"]->text = $sku;
                        $item["SKU"]->value = $sku;
                        $items[] = $item;
                    }
                }
            });
            $this->options = $items;
        }
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