Write-Host "Stopping Docker Compose and removing volumes..."
docker compose down --volumes --remove-orphans

docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build


# docker compose exec db mysql -u matt -p404404 shopmate