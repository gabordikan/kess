<?php

namespace app\widgets;

use yii\jui\DatePicker;
use yii\jui\DatePickerLanguageAsset;
use yii\jui\JuiAsset;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;

class MyDatePicker extends DatePicker {
    
    /**
     * Renders the widget.
     */
    public function run()
    {
        $view = $this->getView();

        echo $this->renderWidget() . "\n";

        $view->registerJs('$.datepicker.setDefaults({
            showOn: "both",
            buttonImageOnly: false,
            buttonText: \'<i class="fa-solid fa-calendar">\'
          });');

        $containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
        $containerIDIcon = $containerID."-icon";
        $language = $this->language ? $this->language : Yii::$app->language;

        if (strncmp($this->dateFormat, 'php:', 4) === 0) {
            $this->clientOptions['dateFormat'] = FormatConverter::convertDatePhpToJui(substr($this->dateFormat, 4));
        } else {
            $this->clientOptions['dateFormat'] = FormatConverter::convertDateIcuToJui($this->dateFormat, 'date', $language);
        }

        if ($language !== 'en-US') {
            $assetBundle = DatePickerLanguageAsset::register($view);
            $assetBundle->language = $language;
            $options = Json::htmlEncode($this->clientOptions);
            $language = Html::encode($language);

            $view->registerJs("jQuery('#{$containerID}').datepicker($.extend({}, $.datepicker.regional['{$language}'], $options));");
        } else {
            $this->registerClientOptions('datepicker', $containerID);
        }

        $this->registerClientEvents('datepicker', $containerID);
        JuiAsset::register($this->getView());
    }

    /**
     * Renders the DatePicker widget.
     * @return string the rendering result.
     */
    protected function renderWidget()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        if ($value !== null && $value !== '') {
            // format value according to dateFormat
            try {
                $value = Yii::$app->formatter->asDate($value, $this->dateFormat);
            } catch(InvalidParamException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }
        $options = $this->options;
        if (empty($options['style'])) {
            $options['style'] = "width: 90px;";
        }
        $options['value'] = $value;

        if ($this->inline === false) {
            // render a text input
            if ($this->hasModel()) {
                $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::textInput($this->name, $value, $options);
            }

        } else {
            // render an inline date picker with hidden input
            if ($this->hasModel()) {
                $contents[] = Html::activeHiddenInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::hiddenInput($this->name, $value, $options);
            }
            $this->clientOptions['defaultDate'] = $value;
            $this->clientOptions['altField'] = '#' . $this->options['id'];
            $contents[] = Html::tag('div', null, $this->containerOptions);
        }

        return implode("\n", $contents);
    }
}

?>