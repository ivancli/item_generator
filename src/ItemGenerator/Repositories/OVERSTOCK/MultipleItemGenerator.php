<?php

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 13/07/2017
 * Time: 9:17 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\OVERSTOCK;

use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const OPTION_TYPE_1_REGEX = '#os.optionBreakout.options = (.*?);#';
    const OPTION_TYPE_2_REGEX = "#dropDownOptions: (.*?) ,#si";

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
        $this->__getProductOptions();
        if (!is_null($this->productOptions) && count($this->productOptions) > 0) {
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
            foreach ($this->productOptions as $productOption) {
                $id = $productOption->id;
                $text = $productOption->description;

                $item = [];
                $item["Variation"] = new \stdClass();
                $item["Variation"]->text = $text;
                $item["Variation"]->value = $id;
                $items[] = $item;
            }
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

    private function __getProductOptions()
    {

        if (!is_null($this->content)) {
            preg_match(self::OPTION_TYPE_1_REGEX, $this->content, $matches);
            if (isset($matches[1])) {
                $matchOptions = $matches[1];
                $matchOptions = trim($matchOptions);
            } else {
                preg_match(self::OPTION_TYPE_2_REGEX, $this->content, $matches);
                if (isset($matches[1])) {
                    $matchOptions = $matches[1];
                    $matchOptions = trim($matchOptions);
                }
            }
            if (isset($matchOptions) && !is_null($matchOptions)) {
                $productOptions = json_decode($matchOptions);
                if (!is_null($productOptions) && json_last_error() === JSON_ERROR_NONE) {
                    $this->productOptions = $productOptions;
                    return true;
                }
            }
        }
    }
}
