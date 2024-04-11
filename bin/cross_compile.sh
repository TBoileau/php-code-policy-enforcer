#!/bin/bash
PLATFORMS="darwin/amd64 darwin/arm64 linux/amd64 linux/arm64 windows/amd64"
SRC_PATH="class_mapper.go"

cd "../scripts" || exit

for PLATFORM in $PLATFORMS; do
    GOOS=${PLATFORM%/*}
    GOARCH=${PLATFORM#*/}
    OUTPUT_NAME="class_mapper-$GOOS-$GOARCH"

    if [ $GOOS = "windows" ]; then
        OUTPUT_NAME+=".exe"
    fi

    echo "Compiling for $GOOS $GOARCH..."
    env GOOS=$GOOS GOARCH=$GOARCH go build -o "../bin/$OUTPUT_NAME" $SRC_PATH

    if [ $? -ne 0 ]; then
        echo 'An error occurred during cross-compilation' 1>&2
        exit 1
    fi
done

echo "Cross-compilation completed."
