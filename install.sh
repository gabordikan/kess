#!/bin/bash

composer update

php yii migrate --interactive=0
