#!/usr/bin/env bash
set -ex

SCRIPT_PATH="$(dirname $(readlink -f $0))"

build_documentation() {
  cd $SCRIPT_PATH/doc-build && composer install --ignore-platform-reqs && cd /tmp && git clone https://${GH_TOKEN}@github.com:paycoreio/openfintech-docs.git \
  && php $SCRIPT_PATH/doc-build/index.php -p /tmp/openfintech-docs && cd openfintech-docs
}

build_documentation
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis CI"
git add .
git commit --message "Travis build: ${TRAVIS_BUILD_NUMBER}"
git push origin master