#!/usr/bin/perl
# Version 11 20151026
use IO::Socket;

$previous_default = select(STDOUT);  # save previous default
$|++;                                   # autoflush STDOUT
select(STDERR);
$|++;                                   # autoflush STDERR, to be sure
select($previous_default);

sub prtlog {
  printf(`date '+%Y%m%d_%H%M%S'|tr -d '\n'` . " ");
  printf(@_);
  printf("\n");
}

$host = "10.10.10.10";
$port = "20005";

# nmon -s 30 -c 2879 -T -F yourhost.`date '+%Y%d%m_%H%M%S'`.nmon
$seconds = 30;
$count = 2878; # 30 * 2878 = 1 day - 1 minute
$dir = '/home/aix/bin';
$output_dir = 'nnmon';
$file_path = "$dir/$output_dir/" . `hostname|tr -d '\n'` . "." . `date '+%Y%m%d_%H%M%S'|tr -d '\n'` . ".nmon";

chdir($dir);

if ( not -e $output_dir )
{
  mkdir $output_dir;
}

$os = `uname -s`;
chomp($os);
if ( $os eq "AIX" )
{
  $instanceCount = 1;
  $command = "topas_nmon";
  $extraargs = "-O -d"
}
else
{
  $instanceCount = 2;
  $command = "nmon";
}
($outfilesize, $fn) = `du -k $dir/nnmon_sender.out`;

if ( $outfilesize > 10240 )
{
  system( "cat $dir/nnmon_sender.out > $dir/nnmon_sender.out.old" );
  system( ">$dir/nnmon_sender.out" );
}

$args = " -s $seconds -c $count -T $extraargs -F $file_path";

$howmany = `ps -ef|grep nnmon_sender.pl|grep -v grep 2>/dev/null|wc -l`;
if ( $howmany > $instanceCount )
{
  #print "Only one instance allowed. Kill previously created instance first.\n";
  exit 1;
}

system "unalias rm 2>/dev/null; find $output_dir -name '*.nmon' -mtime +14 -exec rm {} \\;";
system "unalias rm 2>/dev/null; find $output_dir -name '*.nmon' -size -1000 -exec rm {} \\;";

sub signal_handler1 {
  $_ = `ps -ef|grep $command|grep -v grep|grep '$file_path' 2>/dev/null`;
  ($user, $pid) = /(\w+) +(\d+)/;
  if ( $pid > 1 )
  {
    prtlog("Closing nmon process... $pid");
    system "kill -9 $pid";
  }
  exit 2;
};
local $SIG{INT} = $SIG{TERM} = $SIG{__DIE__} = $SIG{QUIT} = $SIG{ABORT} = \&signal_handler1;

$remote = IO::Socket::INET->new( Proto     => "tcp",
                                 PeerAddr  => $host,
                                 PeerPort  => $port,);
if ( ! $remote )
{
  prtlog("Cannot connect remote nnmon server. Check host and port parameters.");
  prtlog("Current remote host: " . $host . " Remote port: " . $port);
  exit 1;
}
$remote->autoflush(1);

open(NMON, $command . $args ." 2>/dev/null & |") or  die "Cannot run nmon.\n";
prtlog($command . $args ." 2>/dev/null");
sleep 15;

open(NMONFILE, $file_path) or die "Cannot open nmon file.\n";

local $SIG{ALRM} = sub { prtlog("Time Out."); die; };
local $SIG{PIPE} = sub { prtlog("Remote host closed the connection."); die; };
alarm $seconds * 30;
while(1)
{
  $line = <NMONFILE>;
  if ( $line eq "" )
  {
    sleep 1;
    next;
  }
  alarm $seconds * 30;
  print $remote $line;
}
close($remote);

END {
  $_ = `ps -ef|grep $command|grep -v grep|grep '$file_path' 2>/dev/null`;
  ($user, $pid) = /(\w+) +(\d+)/;
  if ( $pid > 1 )
  {
    prtlog("Closing nmon process... $pid");
    system "kill -9 $pid";
  }
}
