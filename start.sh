#!/bin/bash

docker compose up -d
ngrok http --url=working-helping-puma.ngrok-free.app 3000
docker compose down
