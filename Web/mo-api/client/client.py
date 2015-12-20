#Socket client example in python
 
import socket   #for sockets
import sys  #for exit
import time
 
#create an INET, STREAMing socket
try:
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
except socket.error:
    print 'Failed to create socket'
    sys.exit()
     
print 'Socket Created'
 
host = '127.0.0.1';
port = 6666;
 
try:
    remote_ip = socket.gethostbyname( host )
 
except socket.gaierror:
    #could not resolve
    print 'Hostname could not be resolved. Exiting'
    sys.exit()
 
#Connect to remote server
s.connect((remote_ip , port))
 
print 'Socket Connected to ' + host + ' on ip ' + remote_ip
 
#Send some data to remote server
message = "123421412gsegsegtwetgwetwewetweftew\n"
 
try :
    #Set the whole string
    s.sendall(message)
except socket.error:
    #Send failed
    print 'Send failed'
    sys.exit()
 
print 'Message send successfully'
 
#Now receive data
buf = ''
tmp = ''
while 1:
	while 1:
		tmp = tmp + s.recv(8096)
		if ('\n' in tmp):
			loc = tmp.find('\n')
			buf = tmp[0:loc]
			tmp = buf[loc + 1: len(tmp)]
			break
	print buf
	
#	s.sendall(message)
#	time.sleep(1)
