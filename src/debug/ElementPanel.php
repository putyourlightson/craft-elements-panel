<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel\debug;

use Craft;
use craft\elements\db\ElementQuery;
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
    private $_viewPath = '@vendor/putyourlightson/craft-elements-panel/src/views/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Event::on(ElementQuery::class, ElementQuery::EVENT_AFTER_POPULATE_ELEMENT,
            function(PopulateElementEvent $event) {
                $elementType = get_class($event->element);

                if (empty($this->_elements[$elementType])) {
                    $this->_elements[$elementType] = [];
                }

                if (empty($this->_elements[$elementType][$event->element->getId()])) {
                    $this->_elements[$elementType][$event->element->getId()] = 0;
                }

                $this->_elements[$elementType][$event->element->getId()]++;
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
        return $this->_elements;
    }
}
