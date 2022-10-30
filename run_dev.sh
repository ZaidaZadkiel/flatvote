#!/bin/bash
echo "trying to build docker"
docker-compose -f docker-compose.dev.yml build
echo "trying to up docker"
docker-compose -f docker-compose.dev.yml up
