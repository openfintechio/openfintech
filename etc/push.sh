#!/usr/bin/env bash

setup_git() {
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "Travis CI"
}

commit_files() {
  git add .
  git commit --message "Travis build: ${TRAVIS_BUILD_NUMBER}"
}

push_files() {
  git remote add origin https://${GH_TOKEN}@github.com/paycoreio/openfintech-meta.git > /dev/null 2>&1
  git push --quiet --set-upstream origin master
}

setup_git
commit_files
push_files