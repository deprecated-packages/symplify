#!/bin/bash

set -e

init_local_repo() {
  mkdir -p "${1}"; (cd "${1}" && git init --bare)
}

if [[ $# -lt 1 ]]; then
  cat >&2 <<!
error: missing argument

Usage:
    $0 <path> [repos]

Arguments:
    path  base path for working directories
    repos base url for remote repositories

!
exit 1;
fi

PREFIX="$1"

if [[ $# -lt 2 ]]; then
  REPOS="${PREFIX}/repos"
  init_local_repo "${REPOS}/monorepo-test-top.git"
  init_local_repo "${REPOS}/monorepo-test-foo.git"
  init_local_repo "${REPOS}/monorepo-test-bar.git"
  TOP_GIT="file://${REPOS}/monorepo-test-top.git"
  FOO_GIT="file://${REPOS}/monorepo-test-foo.git"
  BAR_GIT="file://${REPOS}/monorepo-test-bar.git"
else
  REPOS="${2}"
  TOP_GIT="${REPOS}/monorepo-test-top.git"
  FOO_GIT="${REPOS}/monorepo-test-foo.git"
  BAR_GIT="${REPOS}/monorepo-test-bar.git"
fi

WORKD="${PREFIX}/monorepo-test-top"
mkdir -p "${WORKD}"

pushd "${WORKD}"

git init;
echo "TOP" > README.txt;
cat > monorepo-builder.yml <<!
parameters:
  package_directories:
    - 'packages'
  directories_to_repositories:
    packages/foo: "${FOO_GIT}"
    packages/bar: "${BAR_GIT}"
!
git add -A
git commit -m 'initial commit'
git tag -a -m 'release 0.0' '0.0';

mkdir -p packages/foo
echo "FOO" > packages/foo/README.txt;
git add -A
git commit -m 'added foo' && git tag -a -m 'release 0.1' '0.1';

mkdir -p packages/bar
echo "BAR" > packages/bar/README.txt;
git add -A
git commit -m 'added bar' && git tag -a -m 'release 0.2' '0.2';

git remote add origin "${TOP_GIT}"

popd
