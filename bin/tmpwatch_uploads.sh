#! /bin/sh
flags=-umaM
/usr/sbin/tmpwatch "$flags" 10d /var/www/efi-gnt/uploads/
/usr/sbin/tmpwatch "$flags" 10d /var/www/efi-gnt/html/output/
