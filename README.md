# nnmon
nnmon 11

nnmon is a central database and a web interface for the nmon program's data. It's completely free software  and released under GPLv3.

The first version was ready on 10/25/2010 for internal use of my employer company and the first publicly released version was 0.4 with the date of 06/29/2011.

nnmon_sender.pl is a pure Perl script. It runs the external nmon program and opens it's output file for reading. Every line read from the file is sent to nnmon_server.py program via TCP socket.

nnmon_server.py uses psycopg2 Phyton Postgresql module and is a Phyton script. It reads incoming data, parses and inserts the results to the database.

nnmon_sum.py is a summarization script. It summarizes the data of the day before because it will be truncated in four(It can be changed. Refer to the INSTALL file.) days. Resulting data will be available in one day and will be shown in the "average screens".

nnmon_truncate_partition.py is a script which truncates the patrtition with the oldest data inside.

nnmonweb uses the free software HTML_Tree component and is a php web site. You can see the monitored systems in its left tree and click the menu items. If there is data for the system for last 12 hours you will see a chart on the right hand side. To refresh the chart click the 'Submit' button.

It's database and server components have tested on RHEL 5.5, 6.7, 7.0. Also client parts have tested on AIX 7.1, 6.1, RHEL 5.5, 6.2, Ubuntu 14/04.

20151103 Baris Ozel, ozelbaris@gmail.com
