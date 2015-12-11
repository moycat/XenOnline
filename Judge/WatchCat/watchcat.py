#!/usr/bin/env python3
# -*- coding: utf-8 -*

#
#	File:		watchcat.py
#	Author:		Moycat
#	E-mail:		mxq.1203@gmail.com
#
'''
		MoyOJ 评测端 - WatchCat version 1.0
		授权：	GNU GENERAL PUBLIC LICENSE
'''

import os
import sys
import base64
import ConfigParser
import pymysql
import signal
import socket
import subprocess
import time
import threading
import json

socket_host = ''
socket_port = 6666
client_id = 0
client_hash = ''
client_info = ''
web_url = ''

config_file = '/etc/judge.conf'
log = open("/var/log/judge.log", "a+", 0)
ext = (None, 'cpp', 'pas', 'java')

sock = None
has_conn = False
has_mounted = False
exiting = False
deadline = 0

def GetTime():
	return time.strftime("%Y-%m-%d %X", time.localtime())

def WriteLog(message):
	global log
	to_write = GetTime() + " " + message
	print to_write
	log.write(to_write + "\n")

def init():
	if os.geteuid() != 0:
		WriteLog("Not run by root. Exiting\n")
		sys.exit(1)
	global socket_host, socket_port, client_id, client_hash, client_name, web_url
	global sock, has_conn
	config = ConfigParser.ConfigParser()
	config.read(config_file)
	try:
		socket_host = config.get("socket", "socket_host")
		socket_port = config.getint("socket", "socket_port")
		client_id = config.get("client", "client_id")
		client_hash = config.get("client", "client_hash")
		web_url = config.get("judge", "web_url")
	except:
		WriteLog("Error Reading the Config File\n")
		sys.exit(1)
	try:
		sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		socket_host = socket.gethostbyname( socket_host )
		sock.connect((socket_host , socket_port))
	except:
		WriteLog("Failed to connect to the server\n")
		sys.exit(1)
	has_conn = True
	WriteLog("Connected to the server")

def login():
	global sock, client_id, client_hash, client_info, exiting
	login_request = {'action': 'login', 'client_id': client_id, 'client_hash': client_hash}
	send(login_request)
	while client_info == '':
		if exiting:
			sys.exit(1)
		time.sleep(1)
	WriteLog("Now the judge client <" + client_info['name'] + "> has started successfully.\nWaiting for judge requests...")
#	Clean()

def send(msg):
	global sock
	sock.sendall(json.dumps(msg) + "\n")

def receiver():
	global sock, deadline, exiting
	buf = ''
	tmp = ''
	while not exiting:
		while not exiting:
			tmp = tmp + sock.recv(8096)
			if ('\n' in tmp):
				loc = tmp.find('\n')
				buf = tmp[0:loc]
				tmp = buf[loc + 1: len(tmp)]
				break
		buf = json.loads(buf)
		if buf == 'online':
			deadline = 0
			continue
		elif buf == 'refuse':
			WriteLog("Client ID or hash refused by the server.\n")
			exiting = True
			sys.exit(1)
		if buf['action'] == 'admit':
			pass
		else:
			WriteLog("Unknown Action")

def killer():
	global deadline, has_conn, exiting
	while not exiting:
		while deadline < 10:
			deadline = deadline + 1
			time.sleep(1)
		has_conn = False
		init()
		deadline = 0

WriteLog("MoyOJ Judge Client Starting...")
init()
Receiver = threading.Thread(target=receiver, name='Receiver')
Receiver.setDaemon(True)
Receiver.start()
Killer = threading.Thread(target=killer, name='Killer')
Killer.setDaemon(True)
Killer.start()
login()
while not exiting:
	time.sleep(1)
