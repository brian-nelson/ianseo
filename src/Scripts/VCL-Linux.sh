#!/usr/bin/bash

video="/dev/video1";
width='1280';
height='960';

if [ $# -eq 1 ]; then
	video=$1;
elif [ $# -eq 3 ]; then
	video=$1;
	width=$2;
	height=$3;
elif [ $# -gt 0 ]; then
	echo "\n\nusage: $0 DEV WIDTH HEIGHT";
	exit ;
fi

#vlc v4l2://$video --no-sout-audio --input-repeat 10000 --sout "#transcode{ vcodec=mjpg, fps=3, width=$width, height=$height }:standard{ access=http, mux=mpjpeg, dst=0.0.0.0:8050/stream.mjpg }"
#vlc v4l2://$video :sout="#transcode{vcodec=MJPG,vb=1800,fps=3,scale=1,acodec=none}:duplicate{dst=http{mux=mpjpeg,dst=:8050/stream.mjpg},dst=display}" :sout-keep
#vlc v4l2://$video :sout "#transcode{vcodec=MJPG,vb=2800,fps=3,scale=1,acodec=none}:duplicate{dst=http{mux=mpjpeg,dst=:8050/stream.mjpg},dst=display}" :no-sout-rtp-sap :no-sout-standard-sap :sout-keep


# vlc v4l2:///dev/video1 -vv --v4l2-width 1280 --v4l2-height 960 :input-repeat 10000 :sout='#transcode{vcodec=MJPG,vb=2800,fps=5,scale=1,acodec=none}:duplicate{dst=http{mux=mpjpeg,dst=:8050/stream.mjpg},dst=display}' :no-sout-rtp-sap :no-sout-standard-sap :sout-keep

# vlc -v v4l2:///dev/video1 --input-repeat 10000 --sout-transcode-vcodec MJPG --sout-transcode-vb 4096 --sout-transcode-fps 2 --sout-transcode-scale 1 --sout-transcode-acodec none --sout-standard-dst ':8050/stream.mjpg' --sout-standard-access http --sout-standard-mux mpjpg --sout-keep

# ='#transcode{vcodec=MJPG,vb=800,fps=5,scale=1,acodec=none}:standard{dst=display}' --sout-keep

vlc v4l2://$video --v4l2-width $width --v4l2-height $height --v4l2-fps 5 #\
#	--no-sout-audio \
#	--sout "#transcode{vcodec=MJPG,vb=1024,scale=1,fps=5, width=$width, height=$height}:duplicate{dst=http{mux=mpjpeg,dst=:8050/stream.mjpg},dst=display}" \
#	--no-sout-rtp-sap --no-sout-standard-sap  \
#	--sout-keep
	
	