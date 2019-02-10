#!/usr/bin/env bash

if [ $1 = "stop" ]
then
    cd .docker/ && docker-compose stop
elif [ $1 = "start" ]
then
    cd .docker/ && docker-compose up -d
elif [ $1 = "workspace" ]
then
    cd .docker/ && docker-compose exec --user=laradock workspace bash
fi
