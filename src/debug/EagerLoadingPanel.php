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

class EagerLoadingPanel extends Panel
{
    /**
     * @var bool
     */
    private $_eagerLoadingOpportunity = false;

    /**
     * @var string
     */
    private $_viewPath = '@vendor/putyourlightson/craft-elements-panel/src/views/eager-loading/';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Event::on(ElementQuery::class, ElementQuery::EVENT_BEFORE_PREPARE,
            function(CancelableEvent $event) {
                /** @var ElementQuery $elementQuery */
                $elementQuery = $event->sender;

                if ($this->_eagerLoadingOpportunity || empty($elementQuery->join)) {
                    return;
                }

                $join = $elementQuery->join[0];

                if ($join[0] == 'INNER JOIN' && $join[1] == ['relations' => '{{%relations}}']) {
                    $this->_eagerLoadingOpportunity = true;
                }
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Eager-Loading';
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
        return [
            'eagerLoadingOpportunity' => $this->_eagerLoadingOpportunity,
        ];
    }
}
