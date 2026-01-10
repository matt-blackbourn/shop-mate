Write-Host "Stopping Docker Compose and removing volumes..."
docker compose down

Write-Host "Starting Docker Compose..."
docker compose up -d

