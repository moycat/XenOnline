#Generate a Docker image
FROM ubuntu:15.10
MAINTAINER Moycat
RUN apt-get update
RUN apt-get -y install g++ fp-compiler
ADD https://github.com/moycat/MoyOJ/blob/master/Judge/Cell/bin/Debug/Cell?raw=true /bin
