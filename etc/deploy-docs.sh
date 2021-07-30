#!/usr/bin/env bash
set -ex

SCRIPT_PATH="$(dirname $(readlink -f $0))"

build_documentation() {
  cd $SCRIPT_PATH/doc-build && composer install --ignore-platform-reqs && cd /tmp && git clone git@github.com:vfrmn/openfintech-docs.git  \
  && php $SCRIPT_PATH/doc-build/index.php -p /tmp/openfintech-docs && cd openfintech-docs
}

build_documentation
git config --global user.email "actions@github.com"
git config --global user.name "Github Actions"
git add .
if ! (git status | grep -q "nothing to commit");
then
  git commit --message "Github actions build: ${BUILD_NUMBER}"
  git push origin master
fi


