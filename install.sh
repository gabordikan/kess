#!/bin/bash

composer install

php yii migrate --interactive=0
