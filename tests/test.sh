#!/bin/sh

status() {
  echo 'status:'
  ../bin/yam status
  echo 'history:'
  ../bin/yam history
}
../bin/yam drop
status
echo 'list:'
../bin/yam list

../bin/yam create
../bin/yam status
echo 'history:'
../bin/yam history
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
