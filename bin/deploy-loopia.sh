#!/usr/bin/env bash
# Driftsätter bygget (dist/loopia/) till en domän hos Loopia via SSH/rsync.
#
#   LOOPIA_SSH=användare@ssh.loopia.se bin/deploy-loopia.sh beta.swinggolf.se
#
# Valfritt:
#   UPLOAD_ENV=.env.production   laddar upp filen som swing-app/.env (chmod 600)
#   SSH_KEY=~/.ssh/annan_nyckel  standard: ~/.ssh/id_ed25519_loopia
#   CONFIRM_PROD=yes             krävs för swinggolf.se (live-sajten)
#
# Kontot hos Loopia har många domäner. Skriptet skriver bara i
# ~/<domän>/public_html och ~/<domän>/swing-app, och bara för domänerna nedan.
# writable/ (loggar, cache, sessioner) skrivs aldrig över.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DOMAIN="${1:-}"
DIST="$ROOT/dist/loopia"
SSH_KEY="${SSH_KEY:-$HOME/.ssh/id_ed25519_loopia}"

case "$DOMAIN" in
  beta.swinggolf.se) ;;
  swinggolf.se)
    [ "${CONFIRM_PROD:-}" = "yes" ] || { echo "swinggolf.se är live-sajten. Sätt CONFIRM_PROD=yes om det är meningen." >&2; exit 1; } ;;
  *) echo "Användning: LOOPIA_SSH=användare@ssh.loopia.se $0 beta.swinggolf.se|swinggolf.se" >&2; exit 1 ;;
esac
[ -n "${LOOPIA_SSH:-}" ] || { echo "Sätt LOOPIA_SSH=användare@ssh.loopia.se" >&2; exit 1; }
[ -f "$SSH_KEY" ] || { echo "Hittar inte SSH-nyckeln $SSH_KEY" >&2; exit 1; }

SSH_OPTS=(-o BatchMode=yes -o ConnectTimeout=15 -o IdentitiesOnly=yes -o IdentityAgent=none -i "$SSH_KEY")
RSH="ssh ${SSH_OPTS[*]}"
remote() { ssh "${SSH_OPTS[@]}" "$LOOPIA_SSH" "$@"; }

[ -f "$DIST/public_html/index.php" ] && [ -d "$DIST/swing-app/vendor" ] || { echo "Bygg först: composer build:loopia" >&2; exit 1; }

echo "→ Kontrollerar ~/$DOMAIN på servern"
remote "test -d '$DOMAIN/public_html'" || { echo "~/$DOMAIN/public_html finns inte på servern" >&2; exit 1; }
remote "mkdir -p '$DOMAIN/swing-app'"

echo "→ public_html/"
rsync -az --delete --exclude '.well-known/' -e "$RSH" "$DIST/public_html/" "$LOOPIA_SSH:$DOMAIN/public_html/"

echo "→ swing-app/ (kod och beroenden)"
for dir in app vendor; do
  rsync -az --delete -e "$RSH" "$DIST/swing-app/$dir/" "$LOOPIA_SSH:$DOMAIN/swing-app/$dir/"
done
rsync -az -e "$RSH" "$DIST/swing-app/composer.json" "$DIST/swing-app/composer.lock" "$DIST/swing-app/spark" \
  "$DIST/swing-app/.env.example" "$DIST/swing-app/.htaccess" "$LOOPIA_SSH:$DOMAIN/swing-app/"

echo "→ writable/ (bara mappar och filer som saknas)"
rsync -az --ignore-existing -e "$RSH" "$DIST/swing-app/writable/" "$LOOPIA_SSH:$DOMAIN/swing-app/writable/"

if [ -n "${UPLOAD_ENV:-}" ]; then
  [ -f "$ROOT/$UPLOAD_ENV" ] || { echo "Hittar inte $UPLOAD_ENV" >&2; exit 1; }
  echo "→ $UPLOAD_ENV → swing-app/.env"
  # Via ssh med umask 077: filen är bara läsbar för ägaren från första stund.
  remote "umask 077 && cat > '$DOMAIN/swing-app/.env.tmp' && mv '$DOMAIN/swing-app/.env.tmp' '$DOMAIN/swing-app/.env' && chmod 600 '$DOMAIN/swing-app/.env'" < "$ROOT/$UPLOAD_ENV"
fi
remote "test -f '$DOMAIN/swing-app/.env'" || echo "OBS: swing-app/.env saknas på servern. Kör med UPLOAD_ENV=.env.production."

echo "→ Tömmer sidcachen"
remote "find '$DOMAIN/swing-app/writable/cache' -type f ! -name index.html -delete"

echo
echo "Klart: https://$DOMAIN/"
echo "Nya migreringar körs via https://$DOMAIN/admin/setup/?token=… (se docs/drift-loopia.md)."
