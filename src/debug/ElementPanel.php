<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel\debug;

use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\events\CancelableEvent;
use craft\events\PopulateElementEvent;
use yii\base\Event;
use yii\debug\Panel;

class ElementPanel extends Panel
{
    /**
     * @var array
     */
    private $_elements = [];

    /**
     * @var string
     */
    private $_viewPath = '@vendor/putyourlightson/craft-elements-panel/src/views/element/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT,
            function(PopulateElementEvent $event) {
                $this->_addElement($event->element);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Elements';
    }

    /**
     * @inheritdoc
     */
    public function getSummary()
    {
        return Craft::$app->getView()->render($this->_viewPath.'summary', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        return Craft::$app->getView()->render($this->_viewPath.'detail', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $total = 0;
        $elements = [];

        foreach ($this->_elements as $elementType => $elementIds) {
            $duplicates = 0;

            foreach ($elementIds as $elementId => $count) {
                $total++;

                if ($count > 1) {
                    $duplicates++;
                }
            }

            $elements[] = [
                'elementType' => $elementType,
                'count' => count($elementIds),
                'duplicates' => $duplicates,
            ];
        }

        return [
            'total' => $total,
            'elements' => $elements,
        ];
    }

    /**
     * Adds populated element count.
     *
     * @param ElementInterface $element
     */
    private function _addElement(ElementInterface $element)
    {
        $elementType = get_class($element);

        if (empty($this->_elements[$elementType])) {
            $this->_elements[$elementType] = [];
        }

        if (empty($this->_elements[$elementType][$element->getId()])) {
            $this->_elements[$elementType][$element->getId()] = 0;
        }

        $this->_elements[$elementType][$element->getId()]++;
    }
}
