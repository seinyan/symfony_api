#!/usr/bin/env bash

php bin/console doctrine:schema:update --force

#php bin/console doctrine:schema:update --force --em=call