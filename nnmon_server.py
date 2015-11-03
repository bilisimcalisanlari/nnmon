#!/usr/bin/python
# Version 11 20151026

import sys, os, re
import time
import datetime
import psycopg2
import socket
import threading
import traceback

log_file_name = '/home/sunkul/nnmon/nnmon_server.log'
port = 20005
dbname = 'nnbase2'
dbhost = 'localhost'
dbuser = 'nnuser'
dbpass = ''

lock = threading.Lock()
length = 4096 # socket receive buffer length

class clientThread ( threading.Thread ):
  def __init__ ( self, client, address ):
    super(clientThread, self).__init__()
    self._stop = threading.Event()
    self.host = ''
    self.os = ''
    self.lparname = ''
    self.serial = ''
    self.vio = 0
    self.MD_CPU_ALL = ''
    cpu = 0
    self.MD_CPU = []
    while cpu < 100 :
      self.MD_CPU.append('')
      cpu += 1
    self.MD_MEM = ''
    self.MD_MEMNEW = ''
    self.MD_MEMUSE = ''
    self.MD_PAGE = ''
    self.MD_PROC = ''
    self.MD_FILE = ''
    self.MD_SEA = ''
    self.MD_NET = ''
    self.MD_NETPACKET = ''
    self.MD_NETSIZE = ''
    self.MD_NETERROR = ''
    self.MD_DISKBUSY = ''
    self.MD_DISKREAD = ''
    self.MD_DISKWRITE = ''
    self.MD_DISKXFER = ''
    self.MD_DISKRXFER = ''
    self.MD_DISKBSIZE = ''
    self.MD_DISKSERV = ''
    self.MD_DISKREADSERV = ''
    self.MD_DISKWRITESERV = ''
    self.MD_IOADAPT = ''
    self.MD_LPAR = ''
    self.MD_UARG = ''
    self.MD_JFSFILE = ''
    self.MD_JFSINODE = ''
    self.MD_TOP = ''
    self.zzzzseen = 0
    self.zturn = ''
    self.zdate = ''
    self.ztime = ''
    self.prev0 = ''
    self.prev1 = ''
    self.dbconnected = 1
    self.client = client
    self.address = address
    self.ip = re.search("\d+\.\d+\.\d+\.\d+",str(address)).group()
    self.prtlog ( "Remote client connected." )
    try:
      self.conn = psycopg2.connect("dbname='" + dbname + "' user='" + dbuser + "' host='" + dbhost + "' password='" + dbpass + "'");
    except:
      self.prtlog ( "Error connecting database.", traceback.format_exc().splitlines() )
      self.dbconnected = 0
      return # return to the run subrutine
    self.cur = self.conn.cursor()
    threading.Thread.__init__ ( self )

  def stop (self):
    self._stop.set()

  def stopped (self):
    return self._stop.isSet()

  def prtlog( self, msg, fmtlines=[] ):
    lock.acquire()
    if msg <> "" :
      print >> log_file, datetime.datetime.now().strftime("%d/%b/%Y %H:%M:%S.%f"), self.host + "(" + self.ip + ")", msg
    for line in fmtlines :
      print >> log_file, datetime.datetime.now().strftime("%d/%b/%Y %H:%M:%S.%f"), self.host + "(" + self.ip + ")", line
    log_file.flush()
    lock.release()

  def insertDB( self, list, exception_idx = 0 ):
    indx = 2
    while indx < len(list) :
      if (( exception_idx == 0 ) or ( exception_idx != indx)) :
        metric = getattr( self, 'MD_' + list[0] )
        if ( list[0] == '' ):
          return
        try:
          if (( metric[indx] == '' ) or ( list[indx] == '' ) or ( list[indx] == '-nan' )):
            return
          self.cur.execute("insert into perfdata (host, topic, metric, value, daytime) values (%s,%s,%s,%s,to_timestamp(%s, 'DD-Mon-YYYYHH24:MI:SS' ))", ( self.host, list[0], metric[indx], list[indx], self.zdate + self.ztime ))
        except (IndexError):
          self.prtlog ( "Index error. Data format error.", traceback.format_exc().splitlines() )
          self.conn.commit()
          self.client.close()
          self.dbconnected = 0  # disconnect db and exit
          return
        except:
          self.prtlog ( "Database error.", traceback.format_exc().splitlines() )
          self.client.close()
          self.dbconnected = 0
          return
      indx += 1

  def parseLine( self, line ):

    if ( not self.zzzzseen ) and ( re.match( '^ZZZ', line )) :
      self.zzzzseen = 1
      try:
        self.cur.execute("insert into hosts (host, os, lparname, serial, vio) values (%s,%s,%s,%s,%s)", (self.host,self.os,self.lparname,self.serial,self.vio))
        self.conn.commit()
      except psycopg2.IntegrityError:
        try:
          self.conn.rollback()
          self.cur.execute("update hosts set os='" + self.os + "', lparname='" + self.lparname + "', serial='" + self.serial + "', vio=" + str(self.vio) + " where host='" + self.host + "'")
          self.conn.commit()
        except:
          self.prtlog ( "Database error. Cannot update host.", traceback.format_exc().splitlines() )
          self.dbconnected = 0
      except:
        self.prtlog ( "Database error. Cannot insert host.", traceback.format_exc().splitlines() )
        self.dbconnected = 0

    line = line.rstrip()
    list = re.split( ',',line )
    if ( not self.zzzzseen ) :
      if ( re.match( '^BBB', line )) :
        return
      if list[0] == 'AAA' :
        if list[1] == 'host' :
          self.host = list[2]
        elif list[1] == 'OS' :
          self.os = list[2]
        elif list[1] == 'build' :
          self.os = list[2]
        elif list[1] == 'LPARNumberName' :
          self.lparname = list[3]
        elif list[1] == 'VIOS' :
          self.vio = 1
        elif list[1] == 'NodeName' :
          self.host = list[2]
        elif list[1] == 'SerialNumber' :
          self.serial = list[2]
      elif ( list[0] == 'CPU_ALL' ) and ( not self.MD_CPU_ALL ) :
        self.MD_CPU_ALL = list
      elif ( re.match( 'CPU(\d+)', list[0] )) :
        ret = re.match( 'CPU(\d+)', list[0] )
        if self.MD_CPU[ int( ret.group(1) ) ] :
          return
        self.MD_CPU[ int( ret.group(1) ) ] = list
      elif ( list[0] == 'MEM' ) and ( not self.MD_MEM ) :
        self.MD_MEM = list
      elif ( list[0] == 'MEMNEW' ) and ( not self.MD_MEMNEW ) :
        self.MD_MEMNEW = list
      elif ( list[0] == 'MEMUSE' ) and ( not self.MD_MEMUSE ) :
        self.MD_MEMUSE = list
      elif ( list[0] == 'PAGE' ) and ( not self.MD_PAGE ) :
        self.MD_PAGE = list
      elif ( list[0] == 'PROC' ) and ( not self.MD_PROC ) :
        self.MD_PROC = list
      elif ( list[0] == 'FILE' ) and ( not self.MD_FILE ) :
        self.MD_FILE = list
      elif ( list[0] == 'SEA' ) and ( not self.MD_SEA ) :
        self.MD_SEA = list
      elif ( list[0] == 'NET' ) and ( not self.MD_NET ) :
        self.MD_NET = list
      elif ( list[0] == 'NETPACKET' ) and ( not self.MD_NETPACKET ) :
        self.MD_NETPACKET = list
      elif ( list[0] == 'NETSIZE' ) and ( not self.MD_NETSIZE ) :
        self.MD_NETSIZE = list
      elif ( list[0] == 'NETERROR' ) and ( not self.MD_NETERROR ) :
        self.MD_NETERROR = list
      elif ( list[0] == 'DISKBUSY' ) and ( not self.MD_DISKBUSY ) :
        self.MD_DISKBUSY = list
      elif ( list[0] == 'DISKREAD' ) and ( not self.MD_DISKREAD ) :
        self.MD_DISKREAD = list
      elif ( list[0] == 'DISKWRITE' ) and ( not self.MD_DISKWRITE ) :
        self.MD_DISKWRITE = list
      elif ( list[0] == 'DISKXFER' ) and ( not self.MD_DISKXFER ) :
        self.MD_DISKXFER = list
      elif ( list[0] == 'DISKRXFER' ) and ( not self.MD_DISKRXFER ) :
        self.MD_DISKRXFER = list
      elif ( list[0] == 'DISKBSIZE' ) and ( not self.MD_DISKBSIZE ) :
        self.MD_DISKBSIZE = list
      elif ( list[0] == 'DISKSERV' ) and ( not self.MD_DISKSERV ) :
        self.MD_DISKSERV = list
      elif ( list[0] == 'DISKREADSERV' ) and ( not self.MD_DISKREADSERV ) :
        self.MD_DISKREADSERV = list
      elif ( list[0] == 'DISKWRITESERV' ) and ( not self.MD_DISKWRITESERV ) :
        self.MD_DISKWRITESERV = list
      elif ( list[0] == 'IOADAPT' ) and ( not self.MD_IOADAPT ) :
        self.MD_IOADAPT = list
      elif ( list[0] == 'LPAR' ) and ( not self.MD_LPAR ) :
        self.MD_LPAR = list
      elif ( list[0] == 'UARG' ) and ( not self.MD_UARG ) :
        self.MD_UARG = list
      elif ( list[0] == 'JFSFILE' ) and ( not self.MD_JFSFILE ) :
        self.MD_JFSFILE = list
      elif ( list[0] == 'JFSINODE' ) and ( not self.MD_JFSINODE ) :
        self.MD_JFSINODE = list
      elif ( list[0] == 'TOP' ) and ( list[1] == '+PID' ) and ( not self.MD_TOP ) :
        self.MD_TOP = list
    else : # self zzzzseen == 1
      if ( list[0] == self.prev0 ) and ( list[1] == self.prev1 ):
        return
      self.prev0 = list[0]
      self.prev1 = list[1]
      if ( list[0] == 'ZZZZ' ) :
        self.zturn = list[1]
        self.ztime = list[2]
        self.zdate = list[3]
        self.conn.commit() # commit previos nmon monitoring turn's inserts
      elif ( list[0] == 'CPU_ALL' ) :
        self.insertDB( list, 6 )
      elif ( list[0] == 'MEM' ) :
        self.insertDB( list )
      elif ( list[0] == 'MEMNEW' ) :
        self.insertDB( list )
#      elif ( list[0] == 'MEMUSE' ) :
#        self.insertDB( list )
#      elif ( list[0] == 'PAGE' ) :
#        self.insertDB( list )
#      elif ( list[0] == 'PROC' ) :
#        self.insertDB( list )
#      elif ( list[0] == 'FILE' ) :
#        self.insertDB( list )
      elif ( list[0] == 'SEA' ) :
        self.insertDB( list )
      elif ( list[0] == 'NET' ) :
        self.insertDB( list )
#      elif ( list[0] == 'NETPACKET' ) :
#        self.insertDB( list )
#      elif ( list[0] == 'NETSIZE' ) :
#        self.insertDB( list )
#      elif ( list[0] == 'NETERROR' ) :
#        self.insertDB( list )
      elif ( list[0] == 'DISKBUSY' ) :
        self.insertDB( list )
      elif ( list[0] == 'DISKREAD' ) :
        self.insertDB( list )
      elif ( list[0] == 'DISKWRITE' ) :
        self.insertDB( list )
      elif ( list[0] == 'DISKXFER' ) :
        self.insertDB( list )
      elif ( list[0] == 'DISKRXFER' ) :
        self.insertDB( list )
#      elif ( list[0] == 'DISKBSIZE' ) :
#        self.insertDB( list )
      elif ( list[0] == 'DISKSERV' ) :
        self.insertDB( list )
      elif ( list[0] == 'DISKREADSERV' ) :
        self.insertDB( list )
      elif ( list[0] == 'DISKWRITESERV' ) :
        self.insertDB( list )
      elif ( list[0] == 'IOADAPT' ) :
        self.insertDB( list )
      elif ( list[0] == 'LPAR' ) :
        self.insertDB( list )
      elif ( list[0] == 'JFSFILE' ) :
        self.insertDB( list )
#      elif ( list[0] == 'JFSINODE' ) :
#        self.insertDB( list )
    return

  def run ( self ):
    olddata = ""
    while 1:
      if self.dbconnected == 0 :
        self.client.close()
        self.prtlog ( "Connection closed. Exiting..." )
        return #exit the thread
      try:
        data = self.client.recv(length)
      except:
        self.prtlog ( "Recv error.", traceback.format_exc().splitlines() )
        break
      if (not data) :
        break;
      if re.match('.*\n', data) :
        data = olddata + data
        olddata = ""
        segmentedline = 0
        if not data.endswith('\n'):
          segmentedline = 1
        lines = re.split( '\n', data )
        if segmentedline == 1 :
          olddata = lines[-1]
          del lines[-1]
        for line in lines :
          if (( self.dbconnected <> 0 ) and ( line <> '' )):
            self.parseLine(line)
      else:
        olddata += data
    self.client.close()
    self.conn.commit() 
    self.prtlog ( "Connection closed. Exiting..." )
    return #exit the thread

log_file = open( log_file_name, 'a' )
def prtMainLog( msg, fmtlines=[] ):
  lock.acquire()
  if msg <> "" :
    print >> log_file, datetime.datetime.now().strftime("%d/%b/%Y %H:%M:%S.%f"), msg 
  for line in fmtlines :
    print >> log_file, datetime.datetime.now().strftime("%d/%b/%Y %H:%M:%S.%f"), line 
  log_file.flush()
  lock.release()

def my_fork():
  fork_result = os.fork()
  if fork_result == 0:
    prtMainLog( 'nnmon_server.py started. PID#: %s' % os.getpid() )
  else:
    sys.exit(0)

if __name__ == "__main__":
  my_fork()

server = socket.socket ( socket.AF_INET, socket.SOCK_STREAM )
try:
  server.bind ( ( '', port ) )
except Exception:
  prtMainLog ( "", traceback.format_exc().splitlines() )
  sys.exit(2)
try:
  server.listen ( 64 )
except Exception:
  prtMainLog ( "", traceback.format_exc().splitlines() )
  sys.exit(2)

while True:
  try:
    channel, details = server.accept()
  except Exception:
    prtMainLog ( "", traceback.format_exc().splitlines() )
  channel.settimeout(300.0)
  try:
    clientThread ( channel, details ).start()
  except Exception:
    prtMainLog ( "", traceback.format_exc().splitlines() )
server.close()
log_file.close()
sys.exit(0)
