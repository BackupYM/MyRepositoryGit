#!/bin/bash
#Start application

export DISPLAY=:0.0
soffice -minimized -norestore -show $1
#soffice -show $1