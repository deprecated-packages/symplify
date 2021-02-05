#!/bin/sh

ssh -i $GIT_SSH_KEY -p $GIT_SSH_PORT -o StrictHostKeyChecking=no -o IdentitiesOnly=yes "$@"
