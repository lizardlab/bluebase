#!/usr/bin/python

import bcrypt
import sys
import mysql.connector
from datetime import date

## Database connection configuration
datahost = 'localhost'
datauser = 'bluebase'
dataname = 'bluebase'
datapass = 'yourpasswordhere'

# connect to database and create cursor
conn = mysql.connector.connect(user=datauser, password=datapass, host=datahost, database=dataname)
cursor = conn.cursor()
today = date.today()

# read in the temporary file
file = open(sys.argv[1], 'r')
username = file.readline().rstrip('\n')
password = file.readline().rstrip('\n')

# make the query such that it only gets accounts that are not expired or disabled
query = "SELECT `hashed_pass` FROM `users` WHERE `username` = %s AND (`expires` >= %s OR `expires` IS NULL) AND `disabled` = 0"
params = (username, today.isoformat())
cursor.execute(query, params)

# we just need one record
row = cursor.fetchone()

# make sure to close the connection
def close_connection():
    cursor.close()
    conn.close()

#if there isn't anything then there the user doesn't exist, is disabled or expired
if row != None:
    hash_pass = row[0]
    hash_pass = hash_pass.replace('$2y$', '$2a$')
    # hash the password and make sure they match
    if bcrypt.hashpw(password, hash_pass) == hash_pass:
        close_connection()
        sys.exit(0)
    else:
        close_connection()
        sys.exit(1)
else:
    close_connection()
    sys.exit(1)
