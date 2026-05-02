#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

msg="${1:-Update site $(date '+%Y-%m-%d %H:%M:%S')}"

git add -A

if git diff --cached --quiet; then
  echo "No changes to deploy."
  exit 0
fi

git commit -m "$msg"
git push

echo "Deploy pushed. Cloudflare Pages will auto-redeploy in ~30-90s."
