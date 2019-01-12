#!/bin/sh
echo `head -n1337 /dev/urandom | shasum5.16 | cut -d' ' -f1` > steg0_initial_root_password
