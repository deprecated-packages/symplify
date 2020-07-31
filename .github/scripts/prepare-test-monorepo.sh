#!/bin/bash

set -e

PREFIX="$1"
REPOS="${PREFIX}/repos"
WORKD="${PREFIX}/top"

mkdir -p "${REPOS}/top.git"
mkdir -p "${REPOS}/foo.git"
mkdir -p "${REPOS}/bar.git"
mkdir -p "${WORKD}"

(cd $REPOS/top.git && git init --bare)
(cd $REPOS/foo.git && git init --bare)
(cd $REPOS/bar.git && git init --bare)

pushd "$WORKD"

git init;
echo "TOP" > README.txt;
cat > monorepo-builder.yml <<!
parameters:
  package_directories:
    - 'packages'
  directories_to_repositories:
    packages/foo: "file://$REPOS/foo.git"
    packages/bar: "file://$REPOS/bar.git"
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

git remote add origin "$REPOS/top.git"
git push -u origin master

popd
