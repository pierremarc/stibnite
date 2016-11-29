#!/bin/sh

SOURCE=./
DEST=$1
rsync \
    --verbose  \
    --progress \
    --omit-dir-times \
    -rt  \
    --exclude '.git' \
    ${SOURCE} \
    ${DEST}
