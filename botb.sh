#!/bin/bash

PID_FILE=./botb.pid

if [ "$1" = "start" ]; then

	if [ -e $PID_FILE ]; then
		pid=`cat $PID_FILE`
		if kill -0 &>1 > /dev/null $pid; then
			echo "Already running with PID:" $pid
			exit 1
		else
			rm $PID_FILE
		fi
	fi


  #execute some command in the background here, using
  #the "&"-sign at the end of the command
  
  $2 $3 &

  PID=$!
  echo "$PID" > $PID_FILE
elif [ "$1" = "stop" ]; then
  
    kill -9 `cat $PID_FILE`
  rm $PID_FILE
else
  echo "Usage: <both.sh> start|stop [PHPBINARY] [TWITTER-SCRIPT]"
  exit 1
fi