#!/usr/bin/env bash

# trailing whitespaces
sed -i -E 's/\s+$//g' packages/*/README.md *.md

# remove extra lines
sed -i -E ':a;$!N;s/^\s*\n\s*$//;ta;P;D' packages/*/README.md *.md
