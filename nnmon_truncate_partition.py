#!/usr/bin/python
# Version 11 20151020
import sys, os, re
import time
import datetime
import psycopg2

start = time.time()

partitioncount = 4

try:
  conn = psycopg2.connect("dbname='nnbase2' user='nnuser' host='localhost' password='CHANGE'");
except:
  print "Error connecting database."
  sys.exit(1)
#conn.set_isolation_level(psycopg2.extensions.ISOLATION_LEVEL_AUTOCOMMIT)
cur = conn.cursor()

today = time.time()
today /= 86400
today = int( today )
theday = today + 1
modday = theday % partitioncount
tablename = "perfdata_" + str(modday)

cur.execute("truncate table " + tablename)
conn.commit()

t = datetime.datetime.now()
print t.strftime("%Y%m%d%H%M%S"),"Truncate",tablename,"table completes in", str(round((time.time() - start)/3,3)), "minutes."
conn.close()

