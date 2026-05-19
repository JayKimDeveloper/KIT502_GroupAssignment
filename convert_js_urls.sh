#!/bin/bash
set -e
TARGET_DIR="${1:-./public/js}"

if [ ! -d "$TARGET_DIR" ]; then
    echo "❌ 디렉토리를 찾을 수 없습니다: $TARGET_DIR"
    exit 1
fi

TARGET_DIR=$(cd "$TARGET_DIR" && pwd)
echo "📁 대상 디렉토리: $TARGET_DIR"

BACKUP_DIR="${TARGET_DIR}_backup_$(date +%Y%m%d_%H%M%S)"
cp -r "$TARGET_DIR" "$BACKUP_DIR"
echo "💾 백업: $BACKUP_DIR"
echo ""

TOTAL=0
CHANGED=0

while IFS= read -r -d '' file; do
    TOTAL=$((TOTAL + 1))
    ORIGINAL=$(cat "$file")
    
    perl -i -pe '
        s{fetch\(\s*"(/[^"\s]+)"}{fetch(APP_URL + "$1"}g;
        s{fetch\(\s*'"'"'(/[^'"'"'\s]+)'"'"'}{fetch(APP_URL + "$1"}g;
        s{apiRequest\(\s*"(/[^"\s]+)"}{apiRequest(APP_URL + "$1"}g;
        s{apiRequest\(\s*'"'"'(/[^'"'"'\s]+)'"'"'}{apiRequest(APP_URL + "$1"}g;
        s{fetch\(\s*`(/[^`]+)`}{fetch(`\${APP_URL}$1`}g;
        s{apiRequest\(\s*`(/[^`]+)`}{apiRequest(`\${APP_URL}$1`}g;
        s{(window\.location\.href\s*=\s*)"(/[^"\s]+)"}{$1APP_URL + "$2"}g;
        s{(window\.location\.href\s*=\s*)'"'"'(/[^'"'"'\s]+)'"'"'}{$1APP_URL + "$2"}g;
        s{(?<!\.)(location\.href\s*=\s*)"(/[^"\s]+)"}{$1APP_URL + "$2"}g;
        s{(?<!\.)(location\.href\s*=\s*)'"'"'(/[^'"'"'\s]+)'"'"'}{$1APP_URL + "$2"}g;
    ' "$file"
    
    if [ "$ORIGINAL" != "$(cat "$file")" ]; then
        CHANGED=$((CHANGED + 1))
        echo "✓ ${file#$TARGET_DIR/}"
    fi
done < <(find "$TARGET_DIR" -name "*.js" -print0)

echo ""
echo "전체: $TOTAL / 변경: $CHANGED"
