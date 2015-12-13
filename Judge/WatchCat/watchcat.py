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
import signal
import socket
import subprocess
import time
import threading
import json

SYSTEM_ERROR = '-1'
COMPILING = '-2'
RUNNING = '-3'
AC = '10'
CE = '1'
MLE = '2'
TLE = '3'
RE = '4'
FORBIDDEN = '5'
WA = '6'

socket_host = ''
socket_port = 6666
client_id = 0
client_hash = ''
client_name = ''
web_url = ''
max_thread = 2
now_thread = 0
threadlock = threading.Lock()
timeoutlock = threading.Lock()

if os.geteuid() != 0:
	print "Not run by root. Exiting"
	sys.exit(1)

config_file = '/etc/judge.conf'
log = open("/var/log/judge.log", "a+", 0)
ext = (None, 'cpp', 'pas', 'java')

sock = None
connected = False
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
	mount()
	try:
		socket_host = socket.gethostbyname(socket_host)
	except:
		p("Error Resolving the Domain", True)
	connect_socket()
	start_deamon()
	login()

def mount():
	status = os.popen("losetup | grep /judge/judge.img").read()
	if status != '':
		status = status.split(" ")
		os.system("losetup -d " + status[0])
	status = os.popen("mount | grep /judge/judge.img").read()
	if status != '':
		status = status.split(" ")
		os.system("umount " + status[2])
	if not os.path.exists('/judge/judge.img'):
		os.system("dd if=/dev/zero of=/judge/judge.img bs=10M count=" + str(max_thread * 5))
		os.system("mkfs.ext3 -F /dev/loop20")
	sig = os.system("losetup /dev/loop20 /judge/judge.img")
	if sig != 0:
		global exiting
		exiting = True
		p("Error mounting the Image", True)
	os.system("mount /dev/loop20 /judge/inside")

def clean(path = ""):
	if path != '':
		path = path + "/"
	else:
		path = "*"
	os.system("rm /judge/inside/" + path + " -R -f")
	os.system("rm /judge/stdout/" + path + " -R -f")

def compare(sid, now):
	stdout = "/judge/stdout/" + sid + "/std" + str(now) + ".out "
	userout = "/judge/inside/" + sid + "/out/out" + str(now) + ".out"
	result = os.popen("diff -q -B -b --strip-trailing-cr " + stdout + userout).read()
	if result == '':
		return True
	else:
		return False

def docker_run(sid):
	run = subprocess.Popen(["docker", "run", "-u", "nobody", "-v", "/judge/inside/" + sid + ":/judge", "-t" ,"--net", "none", "-i", "moyoj:cell", "/judge/Cell", sid + "s"])
	while not os.path.exists('/judge/inside/' + sid + '/out/compile'):
		time.sleep(0.1)
	update = {'action': 'update_state', 'sid': sid, 'state': -3, 'timestamp': (int)(time.time())}
	send(update)
	while os.popen("docker ps | grep '/judge/Cell " + sid + "s'").read() != '':
		time.sleep(0.05)

def docker(sid):
	_docker2 = threading.Thread(target=docker_run, name='docker_run', args=(sid,))
	_docker2.setDaemon(True)
	_docker2.start()
	_docker2.join(60)
	get = os.popen("docker ps | grep '/judge/Cell " + sid + "s'").read()
	if get == '':
		return
	get = get.split(" ")
	os.system("docker stop " + get[0])

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
	p("Connected to the server")

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
			time.sleep(1)

def judge(data):
	global now_thread, threadlock
	p("Got a new request! SID = " + str(data['sid']) + ", Lang = " + str(data['lang']))
	while now_thread >= max_thread:
		time.sleep(0.2)
	threadlock.acquire()
	now_thread = now_thread + 1
	threadlock.release()
	
	sid = str(data['sid'])
	lang = str(data['lang'])
	test_turn = str(data['test_turn'])
	time_limit = str(data['time_limit'])
	memory_limit = str(data['memory_limit'])
	p_hash = data['hash']
	code = base64.decodestring(data['code'])
	result = AC
	error = False
	used_time = 0
	used_memory = 0
	detail = ''
	detail_result = ''
	detail_time = ''
	detail_memory = ''
	
	os.system("mkdir /judge/inside/" + sid + " /judge/inside/" + sid + "/in /judge/inside/" + sid + "/out /judge/stdout/" + sid)
	os.system("cp /judge/Cell /judge/inside/" + sid + "/Cell")
	os.system("chmod 777 /judge/inside/" + sid + "/out")
	os.system("chmod 755 /judge/inside/" + sid + "/Cell")
	out = open("/judge/inside/" + sid + "/post." + ext[int(lang)], "w", 0)
	out.write(code)
	out.close()
	out = open("/judge/inside/" + sid + "/judge.conf", "w", 0)
	out.write(time_limit + "\n" + memory_limit + "\n" + test_turn + "\n" + lang)
	out.close()
	i = 0
	out = open("/judge/inside/" + sid + "/downlist", "w", 0)
	while i < int(test_turn):
		out.write(web_url + "/data/" + p_hash + "/test" + str(i) + ".in\n")
		out.write(web_url + "/data/" + p_hash + "/std" + str(i) + ".out\n")
		i += 1
	out.close()
	os.system("wget -P /judge/stdout/" + sid + "/ -i /judge/inside/" + sid + "/downlist -q --no-check-certificate")
	os.system("mv /judge/stdout/" + sid + "/test* /judge/inside/" + sid + "/in/")
	os.system("rm /judge/inside/" + sid + "/downlist")
	
	p("Now starting to compile & run... ( sid = " + sid + " )")
	update = {'action': 'update_state', 'sid': sid, 'state': -2, 'timestamp': (int)(time.time())}
	send(update)
	_docker = threading.Thread(target=docker, name='Docker', args=(sid,))
	_docker.start()
	_docker.join()
	
	if not os.path.exists('/judge/inside/' + sid + '/out/summary.out'):
		error = True
	summary = open("/judge/inside/" + sid + "/out/summary.out")
	result = summary.read()
	summary.close()
	result = result.split("\n")
	all_result = 10
	if int(result[0]) < 0 or result[0] == CE:
		error = True
		all_result = result[0]
	i = 0
	for row in result:
		if len(row) < 3:
			continue
		now = row
		now = now.split(" ")
		if now[0] == RE:
			all_result = RE
		elif now[0] == MLE and result != RE:
			all_result = MLE
		elif now[0] == TLE and result != RE and result != MLE:
			all_result = TLE
		detail_time += now[1] + " "
		detail_memory += now[2] + " "
		used_time += int(now[1])
		if int(now[2]) > used_memory:
			used_memory = int(now[2])
		if now[0] == '0':
			if not compare(sid, i):
				detail_result += WA + " "
			else:
				detail_result += AC + " "
		elif int(now[0]) < 0:
			detail_result += "0 "
		else:
			detail_result += now[0] + " "
		i += 1
	
	error_log = open("/judge/inside/" + sid + "/out/error.log")
	detail = error_log.read()
	update = {'action': 'update', 'sid': sid, 'state': all_result, 'used_time': used_time, 'used_memory': used_memory, 'detail': detail, 'detail_result': detail_result, 
					'detail_time': detail_time, 'detail_memory': detail_memory}
	send(update)
	p("The request (SID = " + sid + ") has been dealt.")
	clean(sid)
	threadlock.acquire()
	now_thread = now_thread - 1
	threadlock.release()

def login():
	login_request = {'action': 'login', 'client_id': client_id, 'client_hash': client_hash}
	send(login_request)
	while client_name == '':
		if exiting:
			sys.exit(1)
		time.sleep(0.2)
	p("Now the judge client <" + client_name + "> has started successfully. Waiting for judge requests...")
	clean()

def send(msg):
	while not connected:
		time.sleep(0.1)
	global sock
	sock.sendall(json.dumps(msg) + "\n")

def receiver():
	global sock, deadline, client_name, timeoutlock
	buf = ''
	tmp = ''
	while not exiting:
		while not exiting:
			while not connected:
				time.sleep(0.1)
			tmp = tmp + sock.recv(8096)
			if ('\n' in tmp):
				loc = tmp.find('\n')
				buf = tmp[0:loc]
				tmp = tmp[loc + 2: len(tmp)]
				break
		buf = json.loads(buf)
		action = buf['action']
		if action == 'online':
			timeoutlock.acquire()
			deadline = 0
			timeoutlock.release()
		elif action == 'judge':
			new_judge = threading.Thread(target=judge, name='JudgeLoader' + (str)(buf['sid']), args=(buf,))
			new_judge.setDaemon(True)
			new_judge.start()
		elif action == 'refuse':
			p("Client ID or hash refused by the server.", True)
		elif action == 'admit':
			client_name = buf['client_name']
		elif action == 'another':
			p("Client ID logged in somewhere else.", True)
		else:
			p("Unknown Action")

def killer():
	global sock, deadline, connected, exiting, timeoutlock
	while not exiting:
		sign = 1
		while deadline < 15 and sign == 1:
			timeoutlock.acquire()
			deadline = deadline + 1
			timeoutlock.release()
			time.sleep(1)
			sign = sock.getsockopt(socket.IPPROTO_TCP, socket.TCP_INFO)
		connected = False
		p("Lost the connection with the server.")
		time.sleep(1)
		connect_socket()
		time.sleep(0.5)
		login()
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
