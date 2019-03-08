down:
	docker-compose down
dul:
	docker-compose down && docker-compose up -d && docker-compose logs -f
