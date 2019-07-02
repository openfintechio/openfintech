#!/usr/bin/env bash

set -ex

SCRIPT_PATH="$(dirname $(readlink -f $0))"

setup_git() {
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "Travis CI"
}

commit_files() {
  git add .
  git commit --message "Travis build: ${TRAVIS_BUILD_NUMBER}"
}

push_files() {
  git push origin master
}

build_meta() {
  mkdir -p metadata && cd metadata && php ../etc/metadata-build.php && cd - && mv metadata /tmp/ && cd /tmp \
  && git clone https://${GH_TOKEN}@github.com/paycoreio/openfintech-meta.git  \
  && cp metadata/* openfintech-meta/data/ \
  && cd openfintech-meta
}

build_documentation() {
  cd $SCRIPT_PATH/doc-build && composer install && cd /tmp && git clone https://${GH_TOKEN}github.com:paycoreio/openfintech-docs.git \
  && php $SCRIPT_PATH/doc-build/index.php -p /tmp/openfintech-docs && cd openfintech-docs
}

# build_meta
# setup_git
# commit_files
# push_files

build_documentation
commit_files
push_files