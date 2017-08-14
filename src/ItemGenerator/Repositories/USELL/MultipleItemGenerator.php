<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 14/08/2017
 * Time: 10:10 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\USELL;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    const MULTIPLE_ITEMS_DETECT_XPATH = '//*[@class="condition_answer_wrapper"]//*[@data-condition-name]';

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
        if (!is_null($this->content)) {
            $crawler = new Crawler($this->content);
            $filteredConditions = $crawler->filterXPath(self::MULTIPLE_ITEMS_DETECT_XPATH);

            $items = [];

            $filteredConditions->each(function (Crawler $filteredCondition) use (&$items) {
                $id = $filteredCondition->attr('data-condition-id');
                $name = $filteredCondition->attr('data-condition-name');

                $item = [];
                $item["Condition"] = new \stdClass();
                $item["Condition"]->text = $name;
                $item["Condition"]->value = $id;
                $items[] = $item;
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