#!/bin/sh

status() {
  echo 'status:'
  ../bin/yam status
  echo 'history:'
  ../bin/yam history
}

../bin/yam init
../bin/yam new test1
../bin/yam new test2
echo 'list:'
../bin/yam list

status
../bin/yam migrate up
status
../bin/yam migrate up
status
../bin/yam migrate down
status
../bin/yam migrate down
status
../bin/yam drop

../bin/yam migrate up
status
../bin/yam migrate down
status
../bin/yam drop
rm -rf migrations
