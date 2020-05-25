#!/usr/bin/env bash

set -ex

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
  && git clone https://${GH_TOKEN}@github.com/openfintechio/meta.git  \
  && cp metadata/* openfintech-meta/data/ \
  && cd openfintech-meta
}

build_meta
setup_git
commit_files
push_files