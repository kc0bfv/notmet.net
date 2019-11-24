OUTNAME := $(shell date +notmet_net_%Y%m%d-%H%M%S)

all:
	hugo
	mv public ${OUTNAME}
	tar cfj ${OUTNAME}.tar.bz2 ${OUTNAME}
	rm -rf ${OUTNAME}
