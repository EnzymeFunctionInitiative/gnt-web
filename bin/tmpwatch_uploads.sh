#! /bin/sh
flags=-umaM
/usr/sbin/tmpwatch "$flags" 10d /var/www/efi-gnt-test/uploads/
/usr/sbin/tmpwatch "$flags" 10d /var/www/efi-gnt-test/html/output/

