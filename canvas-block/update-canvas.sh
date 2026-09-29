#!/usr/bin/env bash
#
# Rebuild canvas.zip from Automattic/canvas and record exactly what was built.
#
# Usage:
#   ./update-canvas.sh              # latest commit on Canvas trunk
#   ./update-canvas.sh latest       # same as above
#   ./update-canvas.sh <ref>        # a commit SHA, tag or branch
#
# Writes canvas.zip and CANVAS_VERSION next to this script. Nothing is
# committed; review, test in Playground, then commit both files together.

set -euo pipefail

REQUESTED="${1:-latest}"
REF="$REQUESTED"
[ "$REF" = "latest" ] && REF="trunk"

HERE="$(cd "$(dirname "$0")" && pwd)"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

echo "Cloning Automattic/canvas..."
git clone --quiet https://github.com/Automattic/canvas.git "$WORK/canvas"
cd "$WORK/canvas"

if ! git checkout --quiet "$REF" 2>/dev/null; then
	echo "Could not find '$REF' in Automattic/canvas." >&2
	exit 1
fi

COMMIT="$(git rev-parse HEAD)"
COMMIT_DATE="$(git log -1 --format=%aI)"
SUBJECT="$(git log -1 --format=%s)"
echo "Building $COMMIT ($SUBJECT)..."

npm ci --silent --no-audit --no-fund
npm run --silent package:plugin

cp dist/canvas.zip "$HERE/canvas.zip"
SHA256="$(shasum -a 256 "$HERE/canvas.zip" | cut -d' ' -f1)"

cat > "$HERE/CANVAS_VERSION" <<EOF
# Canvas build installed by blueprint.json.
# Written by update-canvas.sh - rerun the script rather than editing by hand.
requested=$REQUESTED
commit=$COMMIT
commit_date=$COMMIT_DATE
commit_subject=$SUBJECT
commit_url=https://github.com/Automattic/canvas/commit/$COMMIT
built_at=$(date -u +%Y-%m-%dT%H:%M:%SZ)
built_with_node=$(node -v)
sha256=$SHA256
EOF

echo
echo "Done. canvas.zip is now Canvas ${COMMIT:0:7} ($COMMIT_DATE)."
echo "Next: test the blueprint in Playground, then commit canvas.zip and CANVAS_VERSION together."
