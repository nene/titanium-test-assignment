#!/usr/bin/env bash
#
# Little helper script to re-create the database.
#

echo 'drop database car_prices;' | mysql
echo 'create database car_prices;' | mysql
cat schema.sql | mysql car_prices
