Write-Host "Stopping Docker Compose and removing volumes..."
docker compose down --volumes --remove-orphans

docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build