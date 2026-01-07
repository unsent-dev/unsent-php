#!/bin/bash
set -e

# Ensure we are in the sdk root or handle paths correctly
# This script assumes it's run from the cli/php-sdk directory or we can find the schema relative to it.

SCHEMA_PATH="../../apps/docs/public/api-reference.json"
TEMP_DIR=".openapi-temp"
TYPES_OUTPUT_DIR="src/Model"

echo "Generating PHP types from ${SCHEMA_PATH}..."

# check if schema exists
if [ ! -f "$SCHEMA_PATH" ]; then
    echo "Error: Schema file not found at $SCHEMA_PATH"
    exit 1
fi

# Check if pnpm is available
if ! command -v pnpm &> /dev/null; then
    echo "Error: pnpm not found. Please install Node.js and pnpm."
    exit 1
fi

# Clean up temp directory if it exists
rm -rf "$TEMP_DIR"

# Generate PHP client using openapi-generator-cli to temp directory
# We only want the models
pnpm dlx @openapitools/openapi-generator-cli generate \
  -i "$SCHEMA_PATH" \
  -g php \
  -o "$TEMP_DIR" \
  --global-property models,modelDocs=false,modelTests=false \
  --additional-properties=invokerPackage=Souravsspace\\Unsent,packageName=Unsent,modelPackage=Model

# Clean output dir
rm -rf "$TYPES_OUTPUT_DIR"
mkdir -p "$TYPES_OUTPUT_DIR"

# Copy generated models
# The generator typically outputs to lib/Model or src/Model depending on settings. 
# For 'php' generator, it often uses 'lib/Model' or just 'Model' inside the output.
# We'll check both locations.

if [ -d "$TEMP_DIR/lib/Model" ]; then
    cp -r "$TEMP_DIR/lib/Model/"* "$TYPES_OUTPUT_DIR/"
elif [ -d "$TEMP_DIR/src/Model" ]; then
    cp -r "$TEMP_DIR/src/Model/"* "$TYPES_OUTPUT_DIR/"
elif [ -d "$TEMP_DIR/Model" ]; then
    cp -r "$TEMP_DIR/Model/"* "$TYPES_OUTPUT_DIR/"
else 
    echo "Warning: Could not find generated models in expected locations. checking $TEMP_DIR"
    find "$TEMP_DIR" -maxdepth 3
fi

# Copy supporting files (ObjectSerializer, ModelInterface)
# They are usually in the parent directory of Model
BASE_SRC_DIR=""
if [ -d "$TEMP_DIR/lib/Model" ]; then
    BASE_SRC_DIR="$TEMP_DIR/lib"
elif [ -d "$TEMP_DIR/src/Model" ]; then
    BASE_SRC_DIR="$TEMP_DIR/src"
elif [ -d "$TEMP_DIR/Model" ]; then
    # If Model is at root, supporting files might be there too?
    # Usually for PHP they are in lib/ or src/
    BASE_SRC_DIR="$TEMP_DIR"
fi

if [ -n "$BASE_SRC_DIR" ]; then
    echo "Copying supporting files from $BASE_SRC_DIR to src/..."
    cp "$BASE_SRC_DIR/ObjectSerializer.php" "src/" 2>/dev/null || echo "Warning: ObjectSerializer.php not found"
    cp "$BASE_SRC_DIR/ModelInterface.php" "src/" 2>/dev/null || echo "Warning: ModelInterface.php not found"
fi

# Cleanup
rm -rf "$TEMP_DIR"

echo ""
echo "✓ Types generated at ${TYPES_OUTPUT_DIR}"
