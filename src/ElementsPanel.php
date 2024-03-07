<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\elementspanel;

use Craft;
use craft\base\Plugin;
use craft\web\Application;
use putyourlightson\elementspanel\debug\EagerLoadingPanel;
use putyourlightson\elementspanel\debug\ElementPanel;
use yii\base\Application as BaseApplication;
use yii\base\Event;
use yii\debug\Module;

class ElementsPanel extends Plugin
{
    /**
     * @var ElementsPanel
     */
    public static ElementsPanel $plugin;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Application::class,
            BaseApplication::EVENT_BEFORE_REQUEST,
            function() {
                /** @var Module|null $debugModule */
                $debugModule = Craft::$app->getModule('debug');

                if ($debugModule) {
                    $debugModule->panels['elements'] = new ElementPanel([
                        'id' => 'elements',
                        'module' => $debugModule,
                    ]);

                    $debugModule->panels['eager-loading'] = new EagerLoadingPanel([
                        'id' => 'eager-loading',
                        'module' => $debugModule,
                    ]);
                }
            }
        );
    }
}
