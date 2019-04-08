<?php

/**
 * Created by PhpStorm.
 * User: Yuchen Yao
 * Date: 4/04/2019
 * Time: 4:36 PM
 */

/* @var $this \yii\web\View */


use app\assets\AppAsset;
use app\models\Car;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\assets\MapAsset;
MapAsset::register($this);

use app\assets\GoogleMapCallback;
GoogleMapCallback::register($this);

$this->title = 'Booking car';
$cars = Car::find()->orderBy('id')->all();
$carList = ArrayHelper::toArray($cars, [
    'app\models\Car' => [
        'id',
        'latitude' => function($post){
            return $temp = $post->latitude+0;
        },
        'longitude' => function($post){
            return $temp = $post->longitude+0;
        },
        'pendingTime',
        'inUse',
    ],
]);

?>
<head>
    <title><?= HTML::encode($this->title) ?></title>
    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            width: 512px;
            height: 512px;
            position: relative;
            overflow: hidden;
        }
        /* Optional: Makes the sample page fill the window. */

        /* The popup bubble styling. */
        .popup-bubble {
            /* Position the bubble centred-above its parent. */
            position: absolute;
            top: 0;
            left: 0;
            transform: translate(-50%, -100%);
            /* Style the bubble. */
            background-color: white;
            padding: 5px;
            border-radius: 5px;
            font-family: sans-serif;
            overflow-y: auto;
            max-height: 60px;
            box-shadow: 0px 2px 10px 1px rgba(0,0,0,0.5);
        }
        /* The parent of the bubble. A zero-height div at the top of the tip. */
        .popup-bubble-anchor {
            /* Position the div a fixed distance above the tip. */
            position: absolute;
            width: 100%;
            bottom: /* TIP_HEIGHT= */ 8px;
            left: 0;
        }
        /* This element draws the tip. */
        .popup-bubble-anchor::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            /* Center the tip horizontally. */
            transform: translate(-50%, 0);
            /* The tip is a https://css-tricks.com/snippets/css/css-triangle/ */
            width: 0;
            height: 0;
            /* The tip is 8px high, and 12px wide. */
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: /* TIP_HEIGHT= */ 8px solid white;
        }
        /* JavaScript will position this div at the bottom of the popup tip. */
        .popup-container {
            cursor: auto;
            height: 0;
            position: absolute;
            /* The max width of the info window. */
            width: 200px;
        }
    </style>
</head>
<body>
<h1>Booking</h1>

<div id="map"></div>


</body>