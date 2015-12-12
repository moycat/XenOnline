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
client_name = ''
web_url = ''
max_thread = 2
now_thread = 0

config_file = '/etc/judge.conf'
log = open("/var/log/judge.log", "a+", 0)
ext = (None, 'cpp', 'pas', 'java')

sock = None
connected = False
has_mounted = False
exiting = False
deadline = 0

def p(message, over = False):
	global log, exiting
	to_write = time.strftime("[%Y-%m-%d %X] ", time.localtime()) + message
	print to_write
	log.write(to_write + "\n")
	if over:
		log.write("\n")
		exiting = True
		sys.exit(1)

def init():
	if os.geteuid() != 0:
		p("Not run by root. Exiting", True)
	global socket_host, socket_port, client_id, client_hash, client_name, web_url
	global sock, connected
	config = ConfigParser.ConfigParser()
	config.read(config_file)
	try:
		socket_host = config.get("socket", "socket_host")
		socket_port = config.getint("socket", "socket_port")
		client_id = config.get("client", "client_id")
		client_hash = config.get("client", "client_hash")
		web_url = config.get("judge", "web_url")
		max_thread = (int)(config.get("judge", "max_thread"))
	except:
		p("Error Reading the Config File", True)
	try:
		socket_host = socket.gethostbyname(socket_host)
	except:
		p("Error Resolving the Domain", True)
	connect_socket()

def connect_socket():
	global connected, sock
	while not connected:
		try:
			sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
			sock.connect((socket_host , socket_port))
			connected = True
		except:
			p("Failed to connect to the server")
			time.sleep(3)
	start_deamon()
	p("Connected to the server")
	login()

def heart_beat():
	while not connected:
		time.sleep(1)
	while not exiting:
		loadavg = {}
		get_load = open("/proc/loadavg")
		get = get_load.read().split()
		get_load.close()
		loadavg['lavg_1'] = get[0]
		loadavg['lavg_5'] = get[1]
		loadavg['lavg_15'] = get[2]
		mem = {}
		get_mem = open("/proc/meminfo")
		get = get_mem.readlines()
		get_mem.close()
		for line in get:
			if len(line) > 1:
				name = line.split(':')[0]
				var = line.split(':')[1].split()[0]
				mem[name] = long(var) * 1024.0
		mem_ratio = str(round((mem['MemTotal'] - mem['MemAvailable']) / mem['MemTotal'] * 100, 1))
		data = {'action': 'heartbeat', 'loadavg': loadavg, 'mem_ratio': mem_ratio, 'timestamp': (int)(time.time())}
		send(data)
		time.sleep(60)
		while not connected:
			time.sleep(5)

def start_judge(data):
	while 1:
		time.sleep(1)
	pass

def login():
	login_request = {'action': 'login', 'client_id': client_id, 'client_hash': client_hash}
	send(login_request)
	while client_name == '':
		if exiting:
			sys.exit(1)
		time.sleep(0.2)
	p("Now the judge client <" + client_name + "> has started successfully. Waiting for judge requests...")
#	Clean()

def send(msg):
	while not connected:
		time.sleep(0.2)
	global sock
	sock.sendall(json.dumps(msg) + "\n")

def receiver():
	global sock, deadline, client_name
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
		action = buf['action']
		if action == 'online':
			deadline = 0
		elif action == 'judge':
			new_judge = threading.Thread(target=start_judge, name='JudgeLoader' + (str)(buf['sid']), args=(buf,))
			new_judge.setDaemon(True)
			new_judge.start()
		elif action == 'refuse':
			p("Client ID or hash refused by the server.", True)
		elif action == 'admit':
			client_name = buf['client_name']
		else:
			p("Unknown Action")
		while not connected:
			sleep(0.5)

def killer():
	global deadline, connected, exiting
	while not exiting:
		while deadline < 10:
			deadline = deadline + 1
			time.sleep(1)
		connected = False
		p("Lost the connection with the server.")
		connect_socket()
		deadline = 0

def start_deamon():
	_Receiver = threading.Thread(target=receiver, name='Receiver')
	_Receiver.setDaemon(True)
	_Receiver.start()
	_Killer = threading.Thread(target=killer, name='Killer')
	_Killer.setDaemon(True)
	_Killer.start()
	_hb = threading.Thread(target=heart_beat, name='heart_beat')
	_hb.setDaemon(True)
	_hb.start()

p("MoyOJ Judge Client Starting...")
init()
while not exiting:
	time.sleep(1)
