# MM10 Academy - WordPress Site

Sports academy website built with WordPress, WooCommerce, Astra theme (child), and Beaver Builder.

## Tech Stack

- **WordPress** 6.x
- **Theme:** Astra Child (`astra-child-mm10`)
- **Page Builder:** Beaver Builder + Ultimate Addon + Power Pack
- **E-commerce:** WooCommerce
- **SEO:** Rank Math Pro
- **Caching:** LiteSpeed Cache
- **Sports:** SportsPress (Soccer)

## Project Structure

```
├── wp-config-production.php   # Production config (uses .env)
├── .env.example               # Environment template
├── .gitignore                 # Git exclusions
├── wp-content/
│   ├── themes/
│   │   ├── astra/             # Parent theme
│   │   └── astra-child-mm10/  # Custom child theme
│   ├── plugins/               # All plugins
│   └── uploads/               # Media (excluded from git)
└── [WordPress Core Files]
```

## Deployment

### Code-Only Deploy (Recommended for your current stage)

If your live WordPress is already installed and data is already migrated, do not run Backuply restore for every release.

Use code-only deployment:
- Deploy theme/plugin/code changes only.
- Keep live database and uploads untouched.
- Keep live `wp-config.php` and `.env` untouched.

This repository now uses `.deployignore` for rsync deployment, so server-only/runtime files are preserved even with `--delete`.

Recommended flow:
1. Backup live DB once before release.
2. Push your tested branch to `main`.
3. Let GitHub Actions deploy code.
4. Clear cache and verify homepage, shop, checkout, and Beaver Builder pages.
5. If any visual issue appears, purge all caches and regenerate CSS in builder/cache plugins.

### Full Backup + Full Restore (Safe Server Method)

Use this when you need a complete site snapshot and a full rollback path.

Scripts:
- `ops/backup_live.sh`
- `ops/restore_live.sh`

Run on Linux server (SSH):

1. Make scripts executable:
   ```bash
   chmod +x ops/backup_live.sh ops/restore_live.sh
   ```

2. Create full backup:
   ```bash
   ./ops/backup_live.sh /var/www/mm10academy /var/backups/mm10academy
   ```

3. Restore from a backup folder (example):
   ```bash
   ./ops/restore_live.sh /var/www/mm10academy /var/backups/mm10academy/20260503-120000
   ```

Safety built into restore script:
- Auto-creates emergency rollback backup before restore.
- Enables and auto-removes `.maintenance` even if restore fails.
- Keeps live `wp-config.php` and `.env` untouched.
- Reapplies safe file permissions.

### Restore All Beaver Builder Pages Only

Use this when code is already correct but Beaver Builder pages differ between local and live.

Scripts:
- `ops/export_bb_layouts.php`
- `ops/import_bb_layouts.php`

Export from the healthy site:

```bash
php ops/export_bb_layouts.php --output=/tmp/beaver-layouts-export.json
```

Copy the JSON file to the target site, then dry-run the import:

```bash
php ops/import_bb_layouts.php --input=/tmp/beaver-layouts-export.json
```

Apply the import once the target matches look correct:

```bash
php ops/import_bb_layouts.php --input=/tmp/beaver-layouts-export.json --apply
```

What is restored:
- `_fl_builder_enabled`
- `_fl_builder_data`
- `_fl_builder_draft`
- `_fl_builder_data_settings`

The importer updates all Beaver Builder-enabled posts and clears Beaver/WordPress cache after apply.

After restore, always:
1. Purge LiteSpeed and any object cache.
2. Check homepage, Beaver Builder pages, WooCommerce cart/checkout.
3. Save permalinks once from admin if routes look broken.

### First-time Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/raselahmed-cc/mm10academy.git
   cd mm10academy
   ```

2. Copy environment config:
   ```bash
   cp .env.example .env
   ```

3. Edit `.env` with your production database credentials and salts.
   Generate salts at: https://api.wordpress.org/secret-key/1.1/salt/

4. On production, rename the config:
   ```bash
   cp wp-config-production.php wp-config.php
   ```

5. Import your database and update URLs if needed:
   ```bash
   wp search-replace 'http://mm10academy.local' 'https://mm10academy.com' --all-tables
   ```

6. Set file permissions:
   ```bash
   find . -type d -exec chmod 755 {} \;
   find . -type f -exec chmod 644 {} \;
   ```

### CI/CD (GitHub Actions)

The project includes automated deployment via GitHub Actions:
- Push to `main` → deploys to production
- Push to `staging` → deploys to staging

Configure these GitHub Secrets:
- `DEPLOY_HOST` - Server IP/hostname
- `DEPLOY_USER` - SSH username
- `DEPLOY_KEY` - SSH private key
- `DEPLOY_PATH` - Server path (e.g., `/var/www/mm10academy`)

## Development (Local by Flywheel)

This project is developed using Local by Flywheel:
- Local URL: `http://mm10academy.local`
- Database: `local` / `root` / `root`

## Security Notes

- `wp-config.php` and `.env` are **never** committed to git
- File editing disabled in admin (`DISALLOW_FILE_EDIT`)
- SSL forced for admin
- Debug mode automatically disabled in production
