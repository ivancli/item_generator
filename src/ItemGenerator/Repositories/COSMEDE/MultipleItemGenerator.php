<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 3/08/2017
 * Time: 9:13 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\COSMEDE;

use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const PRODUCT_NAME_XPATH = '//tr[*[@class="price_txt"]]/*[@class="product_name"][2]';

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
        $crawler = new Crawler($this->content);
        $xpathNodes = $crawler->filterXPath(self::PRODUCT_NAME_XPATH);
        if ($xpathNodes->count() > 1) {
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
//        if ($this->hasMultipleItems()) {
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::PRODUCT_NAME_XPATH);
            $items = [];
            $xpathNodes->each(function (Crawler $xpathNode) use (&$items) {
                $productName = $xpathNode->text();

                $item = [];
                $item["Variant"] = new \stdClass();
                $item["Variant"]->text = $productName;
                $item["Variant"]->value = $productName;
                $items[] = $item;
            });
            $this->options = $items;
        }
        return true;
//        } else {
//            return false;
//        }
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