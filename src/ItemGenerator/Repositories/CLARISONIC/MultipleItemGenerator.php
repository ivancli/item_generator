<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 16/06/2017
 * Time: 1:14 PM
 */

namespace IvanCLI\ItemGenerator\Repositories\CLARISONIC;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const DATA_PID_XPATH = '//a[@class="swatchanchor"][@data-pid]';
    const PRODUCT_INFO_REGEX = '#app.page.setEeProductsOnPage\((.*?)\)\;#';

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
        $xpathNodes = $crawler->filterXPath(self::DATA_PID_XPATH);
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
        if ($this->hasMultipleItems()) {
            $this->__getProductInfo();
            if (!is_null($this->content) && !empty($this->content)) {
                $crawler = new Crawler($this->content);
                $xpathNodes = $crawler->filterXPath(self::DATA_PID_XPATH);
                $items = [];
                foreach ($xpathNodes as $xpathNode) {
                    $pid = $xpathNode->getAttribute('data-pid');
                    $matchedProductInfo = array_first(array_filter($this->productInfo, function ($pInfo) use ($pid) {
                        return isset($pInfo->$pid) && !is_null($pInfo->$pid);
                    }));
                    $productInfo = $matchedProductInfo->$pid;
                    if (!is_null($productInfo)) {
                        $element = "Variation";
                        $value = $productInfo->variant;

                        $item = [];
                        $item[$element] = new \stdClass();
                        $item[$element]->text = $value;
                        $item[$element]->value = $productInfo->variantID;
                        $items[] = $item;
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

    private function __getProductInfo()
    {
        $this->productInfo = [];
        if (!is_null($this->content) && !empty($this->content)) {
            preg_match_all(self::PRODUCT_INFO_REGEX, $this->content, $matches);
            if (isset($matches[1])) {
                $productInfo = $matches[1];
                foreach ($productInfo as $pInfo) {
                    $formattedInfo = json_decode($pInfo);
                    if (!is_null($formattedInfo) && json_last_error() === JSON_ERROR_NONE) {
                        $this->productInfo[] = $formattedInfo;
                    }
                }
                return $this->productInfo;
            }
        }
        return false;
    }
}