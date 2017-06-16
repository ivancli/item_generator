<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 16/06/2017
 * Time: 3:53 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\PETCIRCLE;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const MULTIPLE_ITEMS_DETECT_XPATH = '//*[@data-standardprice]';

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
        if (!is_null($this->content) && !empty($this->content)) {
            $crawler = new Crawler($this->content);
            $xpathNodes = $crawler->filterXPath(self::MULTIPLE_ITEMS_DETECT_XPATH);
            if ($xpathNodes->count() > 1) {
                return true;
            }
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
            if (!is_null($this->content) && !empty($this->content)) {
                $crawler = new Crawler($this->content);
                $xpathNodes = $crawler->filterXPath(self::MULTIPLE_ITEMS_DETECT_XPATH);
                $items = [];
                foreach ($xpathNodes as $xpathNode) {
                    $sku = $xpathNode->getAttribute('id');
                    if (!is_null($sku)) {
                        $optionNodes = $crawler->filterXPath("//label[@for='{$sku}']/*[@class='sku-data-size']");
                        if ($optionNodes->count() == 1) {
                            $label = null;
                            foreach ($optionNodes as $optionNode) {
                                if ($optionNode->nodeValue) {
                                    $label = $optionNode->nodeValue;
                                } else {
                                    $label = $optionNode->textContent;
                                }
                            }

                            $item = [];
                            $item["Variation"] = new \stdClass();
                            $item["Variation"]->text = $label;
                            $item["Variation"]->value = $sku;
                            $items[] = $item;
                        }
                    }
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
}