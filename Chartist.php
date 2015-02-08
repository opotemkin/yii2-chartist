<?php
/**
 * @package okeanos\chartist
 * @author Nikolas Grottendieck <github@nikolasgrottendieck.com>
 * @copyright Copyright &copy; Nikolas Grottendieck
 * @license BSD-3-Clause
 * @version 1.0
 */

namespace okeanos\chartist;

use yii;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Base Widget for Chartist
 *
 * @author Nikolas Grottendieck <github@nikolasgrottendieck.com>
 * @since  1.0
 */
class Chartist extends Widget
{
    /**
     * @var array Chartist options, split into [['options' => [], 'responsiveOptions' => []], each section will be JSON encoded and used as parameter for the Chart accordingly
     * @see http://gionkunz.github.io/chartist-js/api-documentation.html
     */
    public $chartOptions = [];

    /**
     * @var array Data to be rendered as a chart, should be an array that can be JSON encoded resulting in data Chartist can work with, check their documentation
     */
    public $data = [];

    /**
     * @var array HTML attributes or other settings for widget container
     */
    public $htmlOptions = [];

    /**
     * @var string The name of the container element that contains the chart. Defaults to 'div'.
     */
    public $tagName = 'div';

    /**
     * @var array The widget options: type of chart ; whether to force an identifier different than the HTML-id, e.g. in case you want to use a class instead; ['type','useClass']
     */
    public $widgetOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        // checks for the element id
        if (!isset($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->getId();
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerChart();
        echo Html::tag($this->tagName, '', $this->htmlOptions);
    }

    /**
     * Create the chart, register the JS, etc.
     */
    protected function registerChart()
    {
        ChartistAsset::register($this->view);

        $data = Json::encode($this->data);
        $options = isset($this->chartOptions['options']) && empty($this->chartOptions['options']) ? Json::encode($this->chartOptions['options']) : [];
        $responsiveOptions = '';

        if (isset($this->chartOptions['responsiveOptions']) && !empty($this->chartOptions['responsiveOptions'])) {
            $responsiveOptions = ', ' . Json::encode($this->chartOptions['responsiveOptions']);
        }

        $identifier = isset($this->widgetOptions['useClass']) && is_string($this->widgetOptions['useClass']) ? '.' . $this->widgetOptions['useClass'] : '#' . $this->htmlOptions['id'];

        $this->view->registerJs('var '.$this->htmlOptions['id'].' = new Chartist.' . $this->widgetOptions['type'] . '("' . $identifier . '", ' . $data . ', ' . $options . $responsiveOptions . ');',
            View::POS_READY);
    }
}