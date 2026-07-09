# Startup Kit — Orders

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) ![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white) ![License](https://img.shields.io/badge/license-MIT-blue)

Order management domain module — orders and order lines.

Part of **Startup Kit**, a modular backend for SaaS products built on **PHP 8.4 / Laravel 12** with DDD, CQRS and ports & adapters.

## Architecture

```text
src/Orders
├── Domain          # Order aggregate + OrderLine, OrderId, OrderStatus, OrderPlaced event
├── Application     # PlaceOrder command, GetOrderById / ListOrders queries + handlers
├── Contracts       # OrderRepository port
├── Http            # Controller, FormRequest, API Resource
└── Infrastructure  # Persistence adapters: Eloquent, Postgres, MySQL, Mongo, InMemory
```

## Highlights

- **Rich domain model** — `Order` aggregate with `OrderLine` children and dedicated domain tests
- **CQRS** — commands and queries dispatched through the core buses (logging, tracing and transactions come for free)
- **Domain events** — `OrderPlaced` published via the core event bus with transactional outbox
- **Swappable persistence** — Postgres, MySQL, Mongo, Eloquent or in-memory, selected by config

## Install

```bash
composer require pedropardoso/startup-kit-orders
```

> Not yet on Packagist — add the repo as a VCS repository in your `composer.json` first:
>
> ```json
> { "repositories": [{ "type": "vcs", "url": "https://github.com/PedroPCardoso/startup-kit-orders" }] }
> ```

Requires [`startup-kit-core`](https://github.com/PedroPCardoso/startup-kit-core). The service provider is auto-discovered by Laravel; configuration lives in `config/startup-kit-orders.php` and migrations ship with the package.

## Testing

```bash
composer install
vendor/bin/phpunit
```

Unit and integration tests run on Orchestra Testbench. Repository adapters share a contract test case, so every persistence backend is verified against the same behaviour.

## Startup Kit modules

| Module | Description |
| --- | --- |
| [startup-kit-core](https://github.com/PedroPCardoso/startup-kit-core) | Primitives, contracts and cross-cutting infrastructure |
| [startup-kit-users](https://github.com/PedroPCardoso/startup-kit-users) | User registration & management |
| [startup-kit-payments](https://github.com/PedroPCardoso/startup-kit-payments) | Payments with multi-gateway support (Stripe, Mercado Pago) |
| [startup-kit-orders](https://github.com/PedroPCardoso/startup-kit-orders) | Orders & order lines |
| [startup-kit-subscriptions](https://github.com/PedroPCardoso/startup-kit-subscriptions) | Recurring subscriptions |
| [startup-kit-notifications](https://github.com/PedroPCardoso/startup-kit-notifications) | Multi-channel notifications (SendGrid, Twilio, Telegram) |

## License

MIT
