; <?php exit; ?>
dir_scripts = ./scripts

;##############################################################################
; AUTHENTICATION - comment out to disable
[auth]
;password = f00bar

; used to generate hashs, tokens and challenges. please change it.
secret   = 9d9c8a3fb07ccb2c7a56d2f89b2dee6f

; duration in seconds a token is valid (default: 900, so 15 minutes)
expire = 900


;##############################################################################
; FILTERS, that control which probes (actually HTML-Elements) are displayed.
;          The filter's number donates its order in the sequence of filters
;
; the following directives can be used:
;   label      The label of the filter, required
;   selector   CSS-selector, separate multiple selectors with comma (,)
;   default    Use the filter by default, optional

[filter-1]
label    = "CPU, I/O, TOP"
selector = "#mpstat, #zpool_iostat, #nicstat, #top"

[filter-2]
label    = "Health"
selector = "#zpool_status, #svcs_x, #smart_health"

[filter-10]
label    = "CPU"
selector = ".probe-cpu"

[filter-20]
label    = "I/O"
selector = ".probe-io"

[filter-30]
label    = "ZFS"
selector = ".probe-zfs"

[filter-31]
label    = "Gluster"
selector = ".probe-gluster"

[filter-40]
label    = "Processes"
selector = ".probe-ps"

[filter-50]
label    = "Services"
selector = ".probe-svcs"

[filter-60]
label    = "Network"
selector = ".probe-network"

[filter-70]
label    = "Logs"
selector = ".probe-logs"

[filter-80]
label    = "System / Hardware"
selector = ".probe-sys"

[filter-90]
label    = "S.M.A.R.T."
selector = ".probe-smart"

[filter-999]
label    = "All"
selector = ".probe"


;##############################################################################
; MACROS, their uppercased name can be used in CMD-directives in commands
;         where they get expanded
;         example:
;           [macros]
;           smartctl = "/path/to/smartctl"
;           [probe-foo]
;           cmd = "%SMARTCTL --foo --bar"
[macros]

; http://smartmontools.sourceforge.net/man/smartctl.8.html
; smartmon requires raw disk access via /dev/rdsk/
; smartctl requires privileged disk-access, so you may chuid it at your own risk! (sudo chmod u+s smartcl)
smartctl = "/opt/smartmon/sbin/smartctl"



;##############################################################################
; DEVICE-SETS, where each set contains N devices.
; A device-set can be used in commands by using the macro %DEVSET-<NUM> which gets expanded to the set's devices.
;
; A command using a device-sets with N devices, is expanded into N individual commands,
; each using one device of the device-set.
; So a command using a device-set with 3 devices is expanded to 3 individual commands with 3 different results.
;
; Example: If %DEVSET-11 contains 3 devices, a command using this devset is expanded to 3 individual commands,
; 		   where each command uses one of those 3 devices
;		   Directive:	[devset-11]
;						dev[] = <dev-1>
;						dev[] = <dev-2>
;						dev[] = <dev-3>
;		   Command:		echo %DEVSET-11
;		   Expanded:	echo <dev-1>, echo <dev-2>, echo <dev-3>
;						(3 independent commands are executed in sequence)


;*****************************
; device-set #0: OS
[devset-0]
dev[] = /dev/rdsk/c8t0d0s0


;*****************************
; device-set #1: Storage
[devset-1]
dev[] = /dev/rdsk/c8t1d0
dev[] = /dev/rdsk/c8t2d0
dev[] = /dev/rdsk/c8t3d0



;##############################################################################
; PROBES, each probe is a listing that displays either
;         the output of a script or of an configured command
;
; the following directives can be used:
;  label	The label of the probe, required
;  class	Arbitrary CSS-classes, primarily used for filtering the probes by above filters, recommended
;  cmd      EITHER a command to get executed, macros defined in [macros] are automatically expanded
;  script   OR name of a script in the scripts-directory
;  order    An integer to determine the order in which the probes are displayed, optional
;  confirm  Probe is not auto refreshed, but explicitly by user-confirmation (e.g. SMART self-tests)


;*****************************
;* CPU

[probe-vmstat]
label  = "CPU (vmstat)"
class  = probe-cpu
cmd    = "/usr/bin/vmstat 1 2"
order  = 10

[probe-mpstat]
label  = "CPU-Cores (mpstat)"
class  = probe-cpu
cmd    = "/usr/bin/mpstat 1 2"
order  = 11


;*****************************
;* I/O

[probe-iostat]
label  = "I/O"
class  = probe-io
;cmd    = "iostat -dcnx 1 2"
cmd    = "/usr/bin/iostat -dnx 1 2"
order  = 20

[probe-zpool_iostat]
label  = "ZFS I/O"
class  = "probe-zfs probe-io"
;cmd    = "zpool iostat -v 1 2"
cmd    = "/sbin/zpool iostat 1 2"
order  = 21

; http://www.brendangregg.com/Perf/network.html#nicstat
[probe-nicstat]
label  = "Network-Interfaces (<a href='http://www.brendangregg.com/K9Toolkit/nicstat.c' target='_blank'>nicstat</a>)"
class  = probe-io
#cmd    = "/usr/sbin/nicstat 1 2"
cmd    = "/usr/bin/ifstat 1 2"
order  = 22


;******************************
;* ZFS

[probe-zpool_status]
label  = "ZFS Status"
class  = probe-zfs
cmd    = "sudo /sbin/zpool status -x"
order  = 31

[probe-zpool]
label  = "ZFS Pools"
class  = probe-zfs
cmd    = "sudo /sbin/zpool status -v"
order  = 32

[probe-zfs]
label  = "ZFS Filesystems"
class  = probe-zfs
cmd    = "sudo /sbin/zfs list -t filesystem -o name,used,avail,mountpoint,sharesmb,sharenfs,compressratio"
order  = 33

[probe-zfs_snaps]
label  = "ZFS Snapshots"
class  = probe-zfs
cmd    = "sudo /sbin/zfs list -t snapshot"
order  = 34

; https://github.com/mharsch/arcstat
; http://hardforum.com/showpost.php?p=1037874802&postcount=22
[probe-zfs_arc_stat]
label  = "ZFS ARC Stat"
class  = probe-zfs
cmd    = "sudo /opt/tools/arcstat.pl -f read,hits,miss,hit%,l2read,l2hits,l2miss,l2hit%,arcsz,l2size 1 3"
order  = 35

[probe-gluster_vol]
label  = "Gluster Volumes"
class  = probe-gluster
cmd    = "sudo /usr/sbin/gluster vol list"
order  = 36

[probe-gluster_status]
label  = "Gluster Volume Status"
class  = probe-gluster
cmd    = "sudo /usr/sbin/gluster vol status"
order  = 37

[probe-gluster_Peer]
label  = "Gluster peer status"
class  = probe-gluster
cmd    = "sudo /usr/sbin/gluster peer status"
order  = 38


;*****************************
;* Services

[probe-svcs_x]
label  = "Service-Problems"
class  = probe-svcs
cmd    = "/usr/sbin/service --status-all"
order  = 40

[probe-svcs]
label  = "Services"
class  = probe-svcs
;cmd    = "svcs -a -o state,stime,fmri"
cmd    = "/usr/bin/svcs"
order  = 41


;*****************************
;* Network

[probe-links]
label  = "Links"
class  = probe-network
cmd    = "/bin/netstat -rn"
order  = 42

[probe-interfaces]
label  = "Interfaces"
class  = probe-network
cmd    = "/sbin/ifconfig"
order  = 43

[probe-hosts]
label  = "Hosts"
class  = probe-network
cmd    = "/usr/lib/klibc/bin/cat /etc/hosts | egrep -v '^#'"
order  = 44

[probe-shares]
label  = "Shares"
class  = probe-network
cmd    = "/usr/sbin/sharemgr show -p"
order  = 45


;*****************************
;* Processes

[probe-prstat]
label  = "Top CPU Processes (prstat)"
class  = probe-ps
cmd    = "/usr/bin/prstat -a -n 10 -s cpu 1 1"
order  = 50

[probe-top]
label  = "Top CPU Processes (CPU-Load now)"
class  = probe-ps
cmd    = "/usr/bin/top --batch --full-commands --quick --displays 1 10"
order  = 51

[probe-top_time]
label  = "Top CPU Processes (Most CPU-Time)"
class  = probe-ps
cmd    = "/usr/bin/top --batch --full-commands --quick --displays 1 10 --sort-order time"
order  = 51

[probe-ps]
label  = "Processes"
class  = probe-ps
;cmd    = "ps axuw"
;cmd    = "ps -e -o pid -o user -o s -o pcpu -o vsz  -o stime -o args"
cmd    = "/bin/ps -e -o pid -o user -o s -o pcpu -o pmem -o vsz  -o stime -o comm"
order  = 53


;*****************************
;* Logs

[probe-dmesg]
label  = "Kernel Ring Buffer (dmesg)"
class  = probe-logs
cmd    = "/usr/lib/klibc/bin/dmesg"
order  = 60

[probe-adm_msgs]
label  = "Messages (/var/adm/messages)"
class  = probe-logs
cmd    = "/usr/lib/klibc/bin/cat /var/adm/messages"
order  = 61
confirm = "Display?"


;*****************************
;* System / Hardware

[probe-uname]
label  = "System Information"
class  = probe-sys
cmd    = "/usr/lib/klibc/bin/uname -a"
order  = 70

[probe-psrinfo]
label  = "Processor"
class  = probe-sys
#cmd    = "/usr/sbin/psrinfo -p -v"
cmd    = "/usr/lib/klibc/bin/cat /proc/cpuinfo"
order  = 71

[probe-cpu_freq]
label  = "CPU Current Frequency"
class  = probe-cpu probe-sys
cmd    = "/usr/bin/kstat -p -m cpu_info -i 0 -s current_clock_Hz"
order  = 72

[probe-cpu_supported_freq]
label  = "CPU Supported Frequencies"
class  = probe-sys
cmd    = "/usr/bin/kstat -p -m cpu_info -s supported_frequencies_Hz"
order  = 73

[probe-prtdiag]
label  = "System Configuration & Diagnostic Information (prtdiag)"
class  = probe-sys
cmd    = "/usr/sbin/prtdiag -v"
order  = 74

[probe-intrstat]
label  = "Interrupt Statistics"
class  = probe-sys
cmd    = "/usr/sbin/intrstat 1 2"
order  = 75


;*****************************
;* SMART

; Error for Command: <undecoded cmd 0xa1>    Error Level: Recovered"
; see http://sourceforge.net/mailarchive/message.php?msg_id=27470552
[probe-smart_health]
label   = "S.M.A.R.T Health"
class   = probe-smart
cmd     = "%SMARTCTL --health -d scsi %DEVSET-1"
order   = 81
confirm = "SMART commands will wake-up your disks!"

[probe-smart_temp]
label   = "S.M.A.R.T Temperature"
class   = probe-smart
cmd     = "%SMARTCTL --attributes -d sat,12 %DEVSET-1 | grep -i temperature"
order   = 82
confirm = "SMART commands will wake-up your disks!"

[probe-smart_attr]
label   = "S.M.A.R.T Attributes (<a href='http://sourceforge.net/apps/trac/smartmontools/wiki/Howto_ReadSmartctlReports_ATA' target='_blank'>HowTo</a>)"
class   = probe-smart
cmd     = "%SMARTCTL --attributes -d sat,12 %DEVSET-1"
order   = 83
confirm = "SMART commands will wake-up your disks!"

[probe-smart_all]
label   = "S.M.A.R.T Information"
class   = probe-smart
cmd     = "%SMARTCTL --all -d sat,12 %DEVSET-1"
order   = 84
confirm = "SMART commands will wake-up your disks!"

[probe-smart_devinfo]
label   = "Device Information"
class   = probe-smart
cmd     = "%SMARTCTL --info -d sat,12 %DEVSET-1"
order   = 85
confirm = "SMART commands will wake-up your disks!"

[probe-iostat_errors]
label   = "IOStat Error Summary"
class   = probe-smart
cmd     = "iostat -En"
order   = 86

[probe-smart_test_results]
label   = "S.M.A.R.T. Self-Test Results"
class   = probe-smart
cmd     = "%SMARTCTL --log=selftest -d sat,12 %DEVSET-1"
order   = 91
confirm = "SMART commands will wake-up your disks!"

[probe-smart_test_short]
label   = "S.M.A.R.T. Short Self-Test"
class   = probe-smart
cmd     = "%SMARTCTL --test=short -d sat,12 %DEVSET-1"
order   = 92
confirm = "SMART commands will wake-up your disks!\nPerform a short Self-Test?"

[probe-smart_test_long]
label   = "S.M.A.R.T. Long Self-Test"
class   = probe-smart
cmd     = "%SMARTCTL --test=long -d sat,12 %DEVSET-1"
order   = 93
confirm = "SMART commands will wake-up your disks!\nPerform a LONG Self-Test?"
