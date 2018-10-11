#!/usr/bin/env bash

set -ex
mkdir -p metadata && cd metadata && php ../etc/metadata-build.php && cd - && mv metadata /tmp/ \
&& cd /tmp \
&& git clone https://${GH_TOKEN}@github.com/paycoreio/openfintech-meta.git  \
&& cp metadata/* openfintech-meta/data/ \
&& cd openfintech-meta && ./etc/push.sh