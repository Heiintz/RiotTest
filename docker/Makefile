launch:
	@sudo docker-compose up -d

up: launch

down:
	@sudo docker-compose down

re:
	@sudo docker-compose restart

list:
	@sudo docker container ls

deleteAll:
	@sudo docker container rm -f `sudo docker container ls -aq`

