#!/usr/bin/env bash
set -ex

if [ ! -z "$TRAVIS_TAG" ]; then
  LATEST_TAG=$(git describe --tags `git rev-list --tags --max-count=1`)
  FILES=()
  DIFF=$(git diff --diff-filter=ACM --name-only ${TRAVIS_TAG} ${LATEST_TAG})
  for i in ${DIFF}; do
      FILES+=( "$i" )
  done
  echo "${FILES[@]}"

  CMDS=()
  for i in "${FILES[@]}"; do
      CMDS+=("--include=$i")
  done

  echo "${CMDS[@]}" | xargs aws s3 sync --quiet resources s3://resources.openfintech.io
  echo "${CMDS[@]}" | xargs aws s3 sync --quiet resources/payment_methods s3://resources.openfintech.io/payment-methods
  echo "${CMDS[@]}" | xargs aws s3 sync --quiet resources/payout_methods s3://resources.openfintech.io/payout-methods
  echo "${CMDS[@]}" | xargs aws s3 sync --quiet resources/payment_providers s3://resources.openfintech.io/payment-providers
fi