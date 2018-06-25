import socket
import mimetypes
import MySQLdb
import hashlib
import re

#Describe the client 
HOST = '127.0.0.1'
PORT = 9000

#check the username and password (for registration purposes)
def checkUserName(Muser,content,checker):
	if((not Muser) or len(Muser)<5 or len(Muser)>10):
		checker = 1
		return checker
def checkPass(Mpass, content, checker):
	if((not Mpass) or not (len(re.findall('[a-zA-Z]+',Mpass))>0 and len(re.findall('[0-9]+',Mpass))>0 and len(re.findall('[._^%$#!~@-]+',Mpass))>0 )):
		checker = 2
		return checker

#Establish TCP connections using Ports
serSock=socket.socket(socket.AF_INET, socket.SOCK_STREAM)
serSock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
serSock.bind((HOST, PORT))
serSock.listen(0) #accept a maximum of only one request at a time
print('Listening on port %s ....' %PORT)
while True: 
	con, addr = serSock.accept() #accept requests from the client
	req = con.recv(1024).decode() #Recieve from the client
	print("REQUEST IS: ")
	print(req)

	#PARSE the request
	lines = req.split('\r\n')
	reqMethod = lines[0].split()[0]
	reqPath = lines[0].split()[1]
	content = ""

	#GET REQUEST IS PLACED:
	if(reqMethod == "GET"):
		db = MySQLdb.connect("localhost","root","root","SYSAD")
		cursor = db.cursor()
		query = "SELECT COUNT,USERNAME FROM SESSION;"
		cursor.execute(query)
		results = cursor.fetchone()

		#check for an existing session and load it
		if(results[0] != -1):
			query1 = "SELECT USERNAME, COUNT FROM USERS WHERE USERNAME = '%s'"%results[1]
			cursor.execute(query1)
			results1 = cursor.fetchone()
			count = results1[1]+1
			content = "Welcome , %s. You have been succesfully logged in %d times!. To Logout <a href='logout.php'> LOGOUT </a>" %(results[1],count)
			updateQuery = "UPDATE USERS SET COUNT = '%d' WHERE USERNAME = '%s'" %(count,results[1])
			try:
				cursor.execute(updateQuery)
				db.commit()
			except:
				db.rollback()
		db.close()	

		#Close the Session when logout is called for
		if(reqPath == "/logout.php"):
				content = "You have been logged out succesfully, click here to login again <a href='/'>LOGIN</a>"
				db = MySQLdb.connect("localhost","root","root","SYSAD")
				cursor = db.cursor()
				#To reset the session table
				query = "UPDATE SESSION SET USERNAME = '%s', PASSWORD = '%s', NAME = '%s', EMAIL = '%s', COUNT =%d ;"%("","","","",-1)
				try:
					cursor.execute(query)
					db.commit()
				except:
					db.rollback()
				db.close()	
			
		print("GET request\n\n")
	
		#Serve for other file paths
		if(results[0]==-1):
			if reqPath == "/":
				fil = open('login.php')
				content = fil.read()
				fil.close()
			if(reqPath == "/Register.php"):
				fil = open('Register.php')
				content = fil.read()
				fil.close()

	#POST METHOD IS REQUESTED			
	if(reqMethod == "POST"):
		db = MySQLdb.connect("localhost","root","root","SYSAD")
		cursor = db.cursor()
		#Welcome page request
		if( reqPath ==  "/welcome.php"):
			reqPost = lines[-1]
			#parse the key values pairs
			data = reqPost.split('&')
			UserName = data[0].split('=')[1]
			PassWord = data[1].split('=')[1]

			query = "SELECT * FROM USERS WHERE USERNAME = '%s' ;" %UserName

			cursor.execute(query)
			results = cursor.fetchone()
			
			#if no user is found
			if results is None:
				content = "User Doesnt exist, Please Register using <a href='Register.php'>REGISTER</a>"
			else:
				hashPassword = results[1]
				count = results[4] + 1
				PassWord = hashlib.sha1(PassWord.encode()).hexdigest() #python hashes are hex digests
				print(hashPassword + " " + PassWord)

				#check if passwords match, if yes serve with welcome content
				if(hashPassword == PassWord):
					content = "Welcome , %s. You have been succesfully logged in %d times!" %(UserName,count)
					updateQuery = "UPDATE USERS SET COUNT = '%d' WHERE USERNAME = '%s'" %(count,UserName)
					try:
						cursor.execute(updateQuery)
						db.commit()
					except:
						db.rollback()
					if(len(data)>3): #data has length of 4 if checkbox is selected
						sessionQuery = "UPDATE SESSION SET USERNAME = '%s', PASSWORD = '%s', NAME = '%s', EMAIL = '%s', COUNT =%d WHERE COUNT = -1"%(results[0],results[1],results[2],results[3],results[4])
						print(sessionQuery)
						try:
							cursor.execute(sessionQuery)
							db.commit()
						except:
							db.rollback()
						
				else:
					content = "Invalid Password"
			db.close()

		#Similar request to the register page
		if(reqPath == "/Register.php"):
			db = MySQLdb.connect("localhost","root","root","SYSAD")
			cursor = db.cursor()
			reqPost = lines[-1]
			data = reqPost.split('&')
			UserName = data[0].split('=')[1]
			PassWord = data[1].split('=')[1]
			Name = data[2].split('=')[1]
			Email = data[3].split('=')[1]
			checker = 0
			checker = checkUserName(UserName,content,checker)
			if(checker==1):
				content += "username cannot be empty, has to be between 5-10 characters<br>"
			checker = checkPass(PassWord,content,checker)
			if(checker == 2):
				content += "password cannot be empty, must contain one alphabet, one numeric and one special character <br>"
			print(UserName, PassWord,checker)

			
			if(checker is None):
				PassWord = hashlib.sha1(PassWord.encode()).hexdigest()
				query = "INSERT INTO USERS (USERNAME, PASSWORD, NAME, EMAIL, COUNT) VALUES ('%s', '%s', '%s', '%s', '%d')"%(UserName,PassWord,Name,Email,0)
				try:
					cursor.execute(query)
					db.commit()
					content += "REGISTRATION SUCCESS"
				except:
					db.rollback()			
			db.close()
			fil = open('Register.php')
			content += fil.read()
			fil.close()

		
	response = 'HTTP/1.0 200 OK\n'+'Content-type: text/html; charset=UTF-8\n\n'+content 
	#send the response to the client via the connection
	con.sendall(response.encode())
	con.close()
serSock.close()


	
