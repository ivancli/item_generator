<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 14/07/2017
 * Time: 9:16 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\BROKERDENTAL;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const OPTION_XPATH = '//*[@id="super-product-table"]/tbody/tr';
    const OPTION_PRODUCT_NAME_XPATH = '//*[@class="product-item-name"]';
    const OPTION_PRODUCT_ID_XPATH = '//*[@data-product-id]';

    protected $content;
    protected $items;
    protected $options;

    protected $productOptions;

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
            $optionNodes = $crawler->filterXPath(self::OPTION_XPATH);
            return $optionNodes->count() > 0;
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
            $crawler = new Crawler($this->content);
            $optionNodes = $crawler->filterXPath(self::OPTION_XPATH);
            $items = [];
            $optionNodes->each(function (Crawler $optionNode) use(&$items){
                $productNameNodes = $optionNode->filterXPath(self::OPTION_PRODUCT_NAME_XPATH);
                $text = "";
                if ($productNameNodes->count() > 0) {
                    $text = $productNameNodes->first()->text();
                }
                $productIdNodes = $optionNode->filterXPath(self::OPTION_PRODUCT_ID_XPATH);
                $id = "";
                if ($productIdNodes->count() > 0) {
                    $id = $productIdNodes->first()->attr('data-product-id');
                }

                $item = [];
                $item["Variation"] = new \stdClass();
                $item["Variation"]->text = $text;
                $item["Variation"]->value = $id;
                $items[] = $item;
            });
            $this->options = $items;
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
