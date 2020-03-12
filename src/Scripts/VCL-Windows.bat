cd c:\program_1\videolan\

vlc v4l2:///dev/video0 --no-sout-audio --input-repeat=10000 --sout="#transcode{ vcodec=mjpg, fps=1, width=640, height=480 }:standard{ access=http, mux=mpjpeg, dst=0.0.0.0:8050/stream.mjpg }"
