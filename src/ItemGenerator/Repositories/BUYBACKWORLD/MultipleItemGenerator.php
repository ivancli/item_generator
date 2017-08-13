<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 14/08/2017
 * Time: 9:34 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\BUYBACKWORLD;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;

class MultipleItemGenerator extends ItemGenerator
{

    protected $content;
    protected $items;
    protected $options;

    protected $products;

    const LD_JSON_XPATH = '//script[@type="application/ld+json"]';

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
        $items = [];

        $new = [];
        $new['Condition'] = new \stdClass();
        $new['Condition']->text = 'New';
        $new['Condition']->value = 'price_new';

        $excellent = [];
        $excellent['Condition'] = new \stdClass();
        $excellent['Condition']->text = 'Excellent';
        $excellent['Condition']->value = 'price_excellent';

        $average = [];
        $average['Condition'] = new \stdClass();
        $average['Condition']->text = 'Average';
        $average['Condition']->value = 'price_average';

        $poor = [];
        $poor['Condition'] = new \stdClass();
        $poor['Condition']->text = 'Poor';
        $poor['Condition']->value = 'price_poor';

        $broken = [];
        $broken['Condition'] = new \stdClass();
        $broken['Condition']->text = 'Broken';
        $broken['Condition']->value = 'price_dead';

        $items[] = $new;
        $items[] = $excellent;
        $items[] = $average;
        $items[] = $poor;
        $items[] = $broken;

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