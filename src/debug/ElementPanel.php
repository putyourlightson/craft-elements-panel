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
    private array $elements = [];

    /**
     * @var string
     */
    private string $viewPath = '@vendor/putyourlightson/craft-elements-panel/src/views/element/';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT,
            function(PopulateElementEvent $event) {
                $this->addElement($event->element);
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
        return Craft::$app->getView()->render($this->viewPath . 'summary', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail(): string
    {
        return Craft::$app->getView()->render($this->viewPath . 'detail', ['panel' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function save(): array
    {
        $total = 0;
        $elements = [];

        foreach ($this->elements as $elementType => $elementIds) {
            $duplicates = 0;

            foreach ($elementIds as $count) {
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
    private function addElement(ElementInterface $element): void
    {
        $elementType = get_class($element);

        if (empty($this->elements[$elementType])) {
            $this->elements[$elementType] = [];
        }

        if (empty($this->elements[$elementType][$element->getId()])) {
            $this->elements[$elementType][$element->getId()] = 0;
        }

        $this->elements[$elementType][$element->getId()]++;
    }
}
