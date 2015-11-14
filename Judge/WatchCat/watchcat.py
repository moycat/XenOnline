#!/usr/bin/env python3
# -*- coding: utf-8 -*

#
#	File:		watchcat.py
#	Author:		Moycat
#	E-mail:		mxq.1203@gmail.com
#
'''
		MoyOJ 评测端 - WatchCat version 0.1
		授权：	GNU GENERAL PUBLIC LICENSE
'''

import os
import sys
import base64
import ConfigParser
import pymysql
import signal
import time
import threading

config_file = '/etc/judge.conf'
log = open("/var/log/judge.log", "a+", 0)
ext = (None, 'cpp', 'pas', 'java')

db_host = ''
db_port = 3306
db_base = ''
db_user = ''
db_pass = ''
client_id = 0
client_hash = ''
client_name = ''
web_url = ''

conn = 0
has_conn = False
has_mounted = False
exiting = False

def get_time():
	return time.strftime("%Y-%m-%d %X", time.localtime())

def write_log(message):
	global log
	log.write(get_time() + " " + message + "\n")

def mount():
	sig = os.system("losetup /dev/loop20 /judge/judge.img")
	if sig != 0:
		write_log("Error Mounting the Image\n")
		conn.close()
		sys.exit(1)

def exit(signum, frame):
	global exiting
	exiting = True
	if has_conn:
		conn.close()
	if has_mounted:
		os.system("umount /judge/inside")
		os.system("losetup -d /dev/loop20")

def db_op(query):
	global db_host, db_port, db_base, db_user, db_pass
	global conn, has_conn
	while not exiting:
		try:
			if has_conn == True:
				op = conn.cursor()
				op.execute(query)
			else:
				raise NoConnection
		except:
			write_log("Error when operating the datebase, trying to connect to the it in 3 seconds...")
			has_conn = False
			time.sleep(3)
			try:
				conn.close()
			except:
				pass
			try:
				conn = pymysql.connect(host = db_host, user = db_user, password = db_pass, db = db_base)
				conn.autocommit(True)
			except:
				write_log("Error Establishing a Database Connection")
			else:
				has_conn = True
		else:
			op.close()
			return op.fetchall()
			break

def has_new():
	query = "SELECT `id`, `pid`, `uid`, `state`, `language` FROM `mo_judge_solution` WHERE `client` = " + client_id + " AND `state` = 0 ORDER BY `post_time` ASC"
	get = db_op(query)
	if len(get) > 0:
		return get[0][0], get[0][1], get[0][2], get[0][4]
	return False

def clean():
	if not os.path.exists('/judge/judge.img'):
		os.system("dd if=/dev/zero of=/judge/judge.img bs=10M count=15")
		mount()
		os.system("mkfs.ext3 -F /dev/loop20")
	if os.popen("losetup | grep /judge/judge.img").read() != '':
		if os.popen("mount -l | grep /judge/inside").read() != '':
			os.system("umount /judge/inside")
	else:
		mount()
	os.system("rm /judge/inside/* -R")
	os.system("rm /judge/stdout/* -R")
	os.system("mkdir /judge/inside/in /judge/inside/out")
	os.system("chmod 777 /judge/inside/out")

def heart_beat():
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
			if len(line) < 2: continue
			name = line.split(':')[0]
			var = line.split(':')[1].split()[0]
			mem[name] = long(var) * 1024.0
		mem_ratio = str(round((mem['MemTotal'] - mem['MemAvailable']) / mem['MemTotal'] * 100, 1))
		timestamp = get_time()
		query = ("UPDATE `mo_judge_client` SET `load_1` = '" + loadavg['lavg_1'] + "', `load_5` = '" + loadavg['lavg_5'] + "', `load_15` = '" + loadavg['lavg_15'] +
			"', `memory` = '" + mem_ratio + "', `last_ping` = '" + timestamp + "' WHERE `mo_judge_client`.`hash` = '" + client_hash + "';")
		db_op(query)
		time.sleep(60)
		while(has_conn == False):
			time.sleep(5)

def init():
	if os.geteuid() != 0:
		print "Not run by root. Exiting"
		sys.exit(1)
	global db_host, db_port, db_base, db_user, db_pass, client_id, client_hash, client_name, web_url
	global conn, has_conn
	config = ConfigParser.ConfigParser()
	config.read(config_file)
	try:
		db_host = config.get("db", "db_host")
		db_port = config.getint("db", "db_port")
		db_base = config.get("db", "db_base")
		db_user = config.get("db", "db_user")
		db_pass = config.get("db", "db_pass")
		client_id = config.get("client", "client_id")
		client_hash = config.get("client", "client_hash")
		web_url = config.get("web", "web_url")
	except:
		write_log("Error Reading the Config File\n")
		sys.exit(1)
	try:
		conn = pymysql.connect(host = db_host, user = db_user, password = db_pass, db = db_base)
	except:
		write_log("Error Establishing a Database Connection\n")
		sys.exit(1)
	conn.autocommit(True)
	has_conn = True
	query = "SELECT name FROM mo_judge_client WHERE id = " + client_id + " AND hash = \"" + client_hash + "\""
	info = db_op(query)
	if len(info) != 1:
		write_log("Error Getting client_name from the Database\n" 
			+ "Make sure the config file is correct and this client has been added to the datebase.\n")
		conn.close()
		sys.exit(1)
	client_name = info[0][0]
	clean()
	write_log("Now the judge client <" + client_name + "> has started successfully.\nWaiting for judge requests...")

class Judge(object):
	def __init__(self, sid, pid, uid, lang):
		self.sid = str(sid)
		self.pid = str(pid)
		self.uid = str(uid)
		self.lang = str(lang)
	def prepare(self):
		query = "SELECT * FROM `mo_judge_code` WHERE `sid` = " + self.sid
		get = db_op(query)
		post_code = base64.decodestring(get[0][1])
		out = open("/judge/inside/post." + ext[int(self.lang)], "w", 0)
		out.write(post_code)
		out.close()
		query = "SELECT `hash`, `time_limit`, `memory_limit`, `test_turn` FROM `mo_judge_problem` WHERE `id` = " + self.pid
		get = db_op(query)
		self.p_hash = get[0][0]
		self.time_limit = str(get[0][1])
		self.memory_limit = str(get[0][2])
		self.test_turn = str(get[0][3])
		out = open("/judge/inside/judge.conf", "w", 0)
		out.write(self.time_limit + "\n" + self.memory_limit + "\n" + self.test_turn + "\n" + self.lang)
		out.close()
		i = 0
		out = open("/judge/downlist", "w", 0)
		while i < int(self.test_turn):
			out.write(web_url + "/data/" + self.p_hash + "/test" + str(i) + ".in\n")
			out.write(web_url + "/data/" + self.p_hash + "/std" + str(i) + ".out\n")
			i += 1
		out.close()
		os.system("wget -P /judge/stdout/ -i /judge/downlist --no-check-certificate")
		os.system("mv /judge/stdout/test* /judge/inside/in/")
		os.system("rm /judge/downlist")
	def run(self):
		return True
		pass
	def judge(self):
		pass
	def update(self):
		pass

signal.signal(signal.SIGINT, exit)
signal.signal(signal.SIGTERM, exit)
write_log("MoyOJ Judge Client Starting...")
init()
_hb = threading.Thread(target=heart_beat, name='HeartBeat')
_hb.setDaemon(True)
_hb.start()
while not exiting:
	get = has_new()
	if get != False:
		print(get)
		judge = Judge(get[0], get[1], get[2], get[3])
		judge.prepare()
		if judge.run() == True:
			judge.judge()
		judge.update()
		time.sleep(10)
		clean()
	else:
		time.sleep(1)
	time.sleep(1)


