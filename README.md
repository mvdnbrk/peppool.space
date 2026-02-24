# peppool.space

<p align="center"><a href="https://peppool.space" target="_blank"><img src="https://cdn.peppool.space/opengraph/default-card-large.png" width="800" height="418" alt="peppool.space"></p>

This is the source of the [peppool.space][link-website] website, providing a real-time Pepecoin explorer experience and powering the [peppool-wallet][link-peppool-wallet] browser extension.

## Development Stack

- [Pepecoin Core][link-pepecoin-core]
- [Electrs-pepe][link-electrs-pepe]
- [Laravel][link-laravel]
- [Tailwind CSS][link-tailwind]
- [Vue.js][link-vue]
- [Lightweight Charts][link-lightweight-charts]

## Development Setup

### Prerequisites

- PHP 8.4+
- Node.js 22+
- Composer
- A running Pepecoin node with RPC enabled
- [Electrs-pepe][link-electrs-pepe] indexer (optional for most features, but recommended)

### Getting Started

1. **Clone the repository:**
   ```bash
   git clone https://github.com/mvdnbrk/peppool.space.git
   cd peppool.space
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your database and Pepecoin node RPC/Electrs credentials.

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

5. **Build assets:**
   ```bash
   # For development
   npm run dev

   # For production
   npm run build
   ```

6. **Run tests:**
   ```bash
   php artisan test
   ```

## Support the project

If you find this project useful and want to support its development:

- GitHub Sponsors: [link][link-sponsors]
- thanks.dev: [link][link-thanks]
- Pepecoin: [sponsor][link-sponsor-page]

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mark van den Broek](https://github.com/mvdnbrk)
- [All Contributors](../../contributors)

[link-website]: https://peppool.space
[link-laravel]: https://laravel.com
[link-vue]: https://vuejs.org
[link-tailwind]: https://tailwindcss.com
[link-lightweight-charts]: https://www.tradingview.com/lightweight-charts/
[link-pepecoin-core]: https://github.com/pepecoinppc/pepecoin
[link-electrs-pepe]: https://github.com/mvdnbrk/electrs-pepe
[link-peppool-wallet]: https://github.com/mvdnbrk/peppool-wallet
[link-sponsors]: https://github.com/sponsors/mvdnbrk
[link-thanks]: https://thanks.dev/u/gh/mvdnbrk
[link-sponsor-page]: https://peppool.space/sponsor
