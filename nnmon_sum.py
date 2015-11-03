#!/usr/bin/python
# Version 11 20151020

import sys, os, re
import time
import datetime
from datetime import date, timedelta
import psycopg2

start = time.time()
t = datetime.datetime.now() - timedelta(1)
yesterday = t.strftime("%Y%m%d")
yestermonth = t.strftime("%Y%m")
yesteryear = t.strftime("%Y")

try:
  conn = psycopg2.connect("dbname='nnbase2' user='nnuser' host='localhost' password='CHANGE'");
except:
  print "Error connecting database."
  sys.exit(1)
cur = conn.cursor()
cur.execute("select host from hosts where os='AIX'")
for row in cur.fetchall():
  curin = conn.cursor()
  curin.execute("select avg(value) from perfdata where host='"+row[0]+"' AND topic='LPAR' and metric='PhysicalCPU' and daytime>current_date-interval '1 day' and daytime<current_date")
  avgcpu = curin.fetchone()[0]
  curin.execute("select avg(value) from perfdata where host='"+row[0]+"' AND topic='LPAR' and metric='PhysicalCPU' and daytime>current_date-interval '1 day'+ interval '9 hours' and daytime<current_date-interval'6 hours'")
  avgcpu9_18 = curin.fetchone()[0]
  curin.execute("select avg(value) from perfdata where topic='MEM' and metric='Real total(MB)' and host='"+row[0]+"' and daytime>current_date-interval '1 day' and daytime<current_date")
  avgmem = curin.fetchone()[0]
  if ((avgcpu is not None) or (avgmem is not None)):
    try:
      curin.execute("insert into perfsum (host,date,avgcpu,avgcpu9_18,avgmem) values (%s,%s,%s,%s,%s)", (row[0],yesterday,avgcpu,avgcpu9_18,avgmem))
    except psycopg2.IntegrityError:
      conn.rollback()
    conn.commit()
    curin.execute("select avg(avgcpu),avg(avgcpu9_18),avg(avgmem) from perfsum where host='"+row[0]+"' and date like'"+yestermonth+"__'")
    retval = curin.fetchone()
    avgcpu = retval[0]
    avgcpu9_18 = retval[1]
    avgmem = retval[2]
    try:
      curin.execute("insert into perfsum (host,date,avgcpu,avgcpu9_18,avgmem) values (%s,%s,%s,%s,%s)", (row[0],yestermonth,avgcpu,avgcpu9_18,avgmem))
    except psycopg2.IntegrityError:
      conn.rollback()
      if (avgcpu is None):
        avgcpu = 0
      if (avgcpu9_18 is None):
        avgcpu9_18 = 0
      if (avgmem is None):
        avgmem = 0
      curin.execute("update perfsum set avgcpu="+str(avgcpu)+", avgcpu9_18="+str(avgcpu9_18)+", avgmem="+str(avgmem)+" where host='"+row[0]+"' and date='"+yestermonth+"'")
    conn.commit()
    curin.execute("select avg(avgcpu),avg(avgcpu9_18),avg(avgmem) from perfsum where host='"+row[0]+"' and date like'"+yesteryear+"__'")
    retval = curin.fetchone()
    avgcpu = retval[0]
    avgcpu9_18 = retval[1]
    avgmem = retval[2]
    try:
      curin.execute("insert into perfsum (host,date,avgcpu,avgcpu9_18,avgmem) values (%s,%s,%s,%s,%s)", (row[0],yesteryear,avgcpu,avgcpu9_18,avgmem))
    except psycopg2.IntegrityError:
      conn.rollback()
      if (avgcpu is None):
        avgcpu = 0
      if (avgcpu9_18 is None):
        avgcpu9_18 = 0
      if (avgmem is None):
        avgmem = 0
      curin.execute("update perfsum set avgcpu="+str(avgcpu)+", avgcpu9_18="+str(avgcpu9_18)+", avgmem="+str(avgmem)+" where host='"+row[0]+"' and date='"+yesteryear+"'")
    conn.commit()

t = datetime.datetime.now()
datestr = t.strftime("%Y%m%d%H%M%S")
print datestr,"Summarization completed in", str(round(time.time() - start,3)), "seconds."
conn.close()
