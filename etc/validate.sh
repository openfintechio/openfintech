#!/usr/bin/env bash

for file in $(ls -1 data/*json | awk -F '.' '{print $1}' |awk -F '/' '{print $2}');do ./bin/validate-json "data/${file}.json" "schemas/${file}_schema.json" --verbose;done