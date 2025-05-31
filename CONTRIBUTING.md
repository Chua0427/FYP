# Contributing to VeroSports Shopping Site

Thank you for your interest in contributing to **VeroSports**! We welcome bug reports, improvements, and new features.

---

## Table of Contents

- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [How to Contribute](#how-to-contribute)
- [Commit Message Guidelines](#commit-message-guidelines)
- [Reporting Issues](#reporting-issues)
- [Pull Request Process](#pull-request-process)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Additional Resources](#additional-resources)

---

## Getting Started

1. Fork the repository on GitHub.
2. Clone your fork locally:
   ```bash
   git clone https://github.com/your-org/verosports.git
   cd verosports
   ```
3. Install PHP dependencies with Composer:
   ```bash
   composer install
   ```
4. Set up database config:
   - Copy `connect_db/config.sample.php` to `connect_db/config.php`
   - Update your database credentials
5. Import the database schema:
   ```bash
   mysql -u root -p verosports < schema.sql
   ```
6. Configure your web server to serve the `User/` directory as the document root.

## Development Setup

- PHP 8.2 or higher
- MySQL / MariaDB
- XAMPP or similar local environment
- Composer installed globally

## Coding Standards

All code contributions must adhere to our guidelines:

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Declare strict types at the top of all PHP files:
  ```php
  <?php
declare(strict_types=1);
  ```
- Use prepared statements (no string concatenation) for SQL queries
- Sanitize all output with `htmlspecialchars()` to prevent XSS
- Implement CSRF token verification on all state-changing forms
- Use `password_hash()` for storing passwords
- Log via Monolog, never `var_dump()` or `print_r()` in production
- Store monetary amounts in minor units (cents)

## How to Contribute

1. Create an issue to discuss major changes or new features.
2. Create a descriptive branch name:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Make your changes in the branch.
4. Ensure all existing tests pass, and add new tests if applicable.
5. Run the linter:
   ```bash
   ./vendor/bin/phpcs --standard=PSR12 FYP
   ```

## Commit Message Guidelines

- Use the imperative, present tense: "Add feature", not "Added feature" or "Adding feature".
- Reference issues by number: `Fixes #123`.
- Keep the subject line under 50 characters and wrap the body at 72 characters.

## Reporting Issues

- Check for existing issues before opening a new one.
- Provide a clear title and description, including steps to reproduce.

## Pull Request Process

1. Submit your pull request against the `main` branch.
2. Include a clear description of your changes and the problem they solve.
3. Link to any related issues.
4. Ensure your PR includes relevant tests and documentation updates.
5. A maintainer will review your PR and suggest any changes.

## Security Vulnerabilities

Please report security issues privately by emailing [security@verosports.com]. Do **not** open a public issue.

## Additional Resources

- [Code of Conduct](CODE_OF_CONDUCT.md)
- [README](README.md)
- [Contribution Guidelines](CONTRIBUTING.md)
- [Issue Templates](.github/ISSUE_TEMPLATE/)
- [Pull Request Template](.github/PULL_REQUEST_TEMPLATE.md)

---

Thank you for helping make VeroSports better! 