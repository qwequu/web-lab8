# ЛР8. DevOps для веб-розробників

Застосунок обліку корпоративних замовлень на уніформу (ЛР6–7), підготовлений до CI/CD.
Студент: Юхатов Олег, група БІП3-23, варіант 24.

- `tests/` — модульні тести PHPUnit
- `Dockerfile`, `docker-compose.yml` — контейнеризація (застосунок + MySQL)
- `.github/workflows/ci.yml` — GitHub Actions: тести → збірка образу → публікація в ghcr.io → запуск і перевірка `health.php`

Локальний запуск у Docker: `docker compose up -d`, далі http://localhost:8080
