<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel\debug;

use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\events\PopulateElementEvent;
use yii\base\Event;
use yii\debug\Panel;

class ElementPanel extends Panel
{
    /**
     * @var array
     */
    private array $_elements = [];

    /**
     * @var string
     */
    private string $_viewPath = '@vendor/putyourlightson/craft-elements-panel/src/views/element/';

    /**
     * @inheritdoc
     */
    public function init(): void
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
    public function getName(): string
    {
        return 'Elements';
    }

    /**
     * @inheritdoc
     */
    public function getSummary(): string
    {
        return Craft::$app->getView()->render($this->_viewPath . 'summary', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail(): string
    {
        return Craft::$app->getView()->render($this->_viewPath . 'detail', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function save(): array
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
