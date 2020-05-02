<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel;

use Craft;
use craft\base\Plugin;
use craft\web\Application;
use putyourlightson\elementspanel\debug\ElementPanel;
use yii\base\Event;
use yii\debug\Module;

class ElementsPanel extends Plugin
{
    /**
     * @var ElementsPanel
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        Event::on(
            Application::class,
            Application::EVENT_BEFORE_REQUEST,
            function() {
                /** @var Module|null $debugModule */
                $debugModule = Craft::$app->getModule('debug');

                if ($debugModule) {
                    $debugModule->panels['elements'] = new ElementPanel([
                        'id' => 'elements',
                        'module' => $debugModule,
                    ]);
                }
            }
        );
    }
}
