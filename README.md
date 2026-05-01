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
