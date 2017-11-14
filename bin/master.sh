#!/bin/bash
DATE=$(date +"%Y-%m-%d %H:%M:%S")
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
echo "$DATE: Start EFI-GNT Master script"

#export EFI_DEBUG=1

php $DIR/check_gnn.php

sleep 1
php $DIR/gnn.php

sleep 1
php $DIR/check_diagrams.php

sleep 1
php $DIR/diagrams.php

DATE=$(date +"%Y-%m-%d %H:%M:%S")
echo "$DATE: Finish EFI-GNT Master script"

