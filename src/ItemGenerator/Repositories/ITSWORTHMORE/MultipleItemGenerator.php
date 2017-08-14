<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 14/08/2017
 * Time: 11:08 AM
 */

namespace IvanCLI\ItemGenerator\Repositories\ITSWORTHMORE;


use IvanCLI\ItemGenerator\Contracts\ItemGenerator;
use Symfony\Component\DomCrawler\Crawler;

class MultipleItemGenerator extends ItemGenerator
{
    protected $content;
    protected $items;
    protected $options;

    protected $productInfo;

    protected $radioAnswers;
    protected $checkboxAnswers;

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

            $answersNodes = $crawler->filterXPath('//*[@class="answer-item"]//label');

            $radioAnswers = [];
            $checkboxAnswers = [];
            $answersNodes->each(function (Crawler $answerNode) use (&$radioAnswers, &$checkboxAnswers) {
                $text = trim($answerNode->text());


                /*radio*/
                $radioNodes = $answerNode->filterXPath('//input[@type="radio"]');

                if ($radioNodes->count() > 0) {
                    $answerName = $radioNodes->first()->attr('name');
                    $answerValue = $radioNodes->first()->attr('value');
                    if (!isset($radioAnswers[$answerName])) {
                        $radioAnswers[$answerName] = new \stdClass();
                        $radioAnswers[$answerName]->options = [];
                        $radioAnswers[$answerName]->label = $answerName;

                    }

                    $newOption = new \stdClass();
                    $newOption->value = $answerName . ':' . $answerValue;
                    $newOption->text = $text;
                    array_push($radioAnswers[$answerName]->options, $newOption);
                }


                /*checkbox*/
                $checkboxNodes = $answerNode->filterXPath('//input[@type="checkbox"]');

                if ($checkboxNodes->count() > 0) {
                    $answerName = $checkboxNodes->first()->attr('name');
                    $answerValue = $checkboxNodes->first()->attr('value');

                    if (!isset($checkboxAnswers[$answerName])) {
                        $checkboxAnswers[$answerName] = new \stdClass();
                        $checkboxAnswers[$answerName]->options = [];
                        $checkboxAnswers[$answerName]->label = $answerName;
                    }
                    $newOption = new \stdClass();
                    $newOption->value = $answerName . ':' . $answerValue;
                    $newOption->text = $text;
                    array_push($checkboxAnswers[$answerName]->options, $newOption);
                }
            });
            $this->radioAnswers = $radioAnswers;
            $this->checkboxAnswers = $checkboxAnswers;
            $radio = [];
            foreach ($this->radioAnswers as $radioAnswer) {
                $radio[] = $radioAnswer;
            }
            $this->radioAnswers = $radio;
            $this->options = [
                'radio' => $this->radioAnswers,
                'checkbox' => $this->checkboxAnswers,
            ];
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
     * @param null $label
     * @return array
     */
    public function combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0, $label = null)
    {
        $radios = array_get($this->options, 'radio');
        $checkboxes = array_get($this->options, 'checkbox');
        $this->__combinations($radios);
        $newItems = [];
        foreach ($checkboxes as $checkbox) {
            if (count($newItems) == 0) {
                $this->__checkboxCombinations($this->items, $checkbox, $newItems);
            } else {
                $this->__checkboxCombinations($newItems, $checkbox, $newItems);
            }
        }
        $this->items = $newItems;
    }

    private function __checkboxCombinations($data, $checkbox, &$newItems)
    {
        foreach ($data as $item) {
            $onItem = $item;
            $onItem[$checkbox->label] = new \stdClass();
            $onItem[$checkbox->label]->value = array_first($checkbox->options)->value;
            $onItem[$checkbox->label]->text = array_first($checkbox->options)->text;
            $newItems[] = $onItem;

            $offItem = $item;
            $newItems[] = $offItem;
        }
    }

    private function __combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0, $label = null)
    {
        $keys = array_keys($data);
        if (isset($value) === true) {
            if (!is_null($label)) {
                array_set($group, $label, $value);
            } else {
                array_push($group, $value);
            }
        }

        if ($i >= count($data)) {
            array_push($all, $group);
        } else {
            $currentKey = $keys[$i];
            $currentElement = $data[$currentKey];
            foreach ($currentElement->options as $val) {
                if (!is_null($data)) {
                    $this->__combinations($data, $all, $group, $val, $i + 1, $currentElement->label);
                }
            }
        }

        $this->items = $all;
    }
}