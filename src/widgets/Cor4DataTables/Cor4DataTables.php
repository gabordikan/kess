<?php

namespace app\widgets\Cor4DataTables;

use fedemotta\datatables\DataTables;
use fedemotta\datatables\DataTablesBootstrapAsset;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Cor4DataTables extends DataTables {

    public function init() {
        
        parent::init();
    }

    public function run() {
        $clientOptions = $this->getClientOptions();
        $view = $this->getView();
        $id = $this->tableOptions['id'];
        
        DataTablesBootstrapAsset::register($view);
        Cor4DataTablesAsset::register( $this->getView() );

        $options = Json::encode($clientOptions);

        $view->registerJs("cor4DataTables('#$id', $options);");
        
        
        //base list view run
        if ($this->showOnEmpty || $this->dataProvider->getCount() > 0) {
            $content = preg_replace_callback("/{\\w+}/", function ($matches) {
                $content = $this->renderSection($matches[0]);

                return $content === false ? $matches[0] : $content;
            }, $this->layout);
        } else {
            $content = $this->renderEmpty();
        }
        $tag = ArrayHelper::remove($this->options, 'tag', 'div');
        echo Html::tag($tag, $content, $this->options);

    }

    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());
        if (count($models) === 0) {
            return "<tbody>\n</tbody>";
        } else {
            return parent::renderTableBody();
        }
    }
}

?>