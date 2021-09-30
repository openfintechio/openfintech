#!/usr/bin/env bash

set -ex

setup_git() {
  git config --global user.email "actions@github.com"
  git config --global user.name "Github Actions"
}

push_files() {
  git add .
  if ! (git status | grep -q "nothing to commit"); then
    git commit --message "Github action #${GITHUB_RUN_NUMBER}: ID ${GITHUB_RUN_ID}"
    git push origin master
  fi
}

build_meta() {
  mkdir -p metadata && cd metadata && php ../etc/metadata-build.php && cd - && mv metadata /tmp/ && cd /tmp \
  && git clone git@github.com:openfintechio/meta.git \
  && cp metadata/* meta/data/ \
  && cd meta
}

build_meta
setup_git
push_files